<?php

namespace Ujamii\UjamiiDsgvo\Service;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\QueryHelper;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class DbOperationsService
 * @package Ujamii\UjamiiDsgvo\Service
 */
class DbOperationsService
{

    const MODE_SELECT = 'select';
    const MODE_DELETE = 'delete';
    const MODE_ANONYMIZE = 'anonymize';

    /**
     * TsConfig configuration
     *
     * @var array
     */
    protected $tsConfiguration = [];

    /**
     * Fields that will be set=0 if anonymized, also acts as "marker" for already handled records.
     *
     * @var array
     */
    protected $ctrlTimeFields = ['tstamp', 'crdate'];

    /**
     * @param string $mode
     *
     * @return array
     */
    public function getDbCheckResult($mode = self::MODE_SELECT)
    {
        $recordsToDelete = [];

        foreach ($this->tsConfiguration['settings']['db'] as $extensionName => $tables) {
            if (ExtensionManagementUtility::isLoaded($extensionName)) {
                foreach ($tables as $tableName => $tableConfig) {
                    $recordsToDelete[$extensionName][$tableName] = $this->getRecordCount($tableName, $tableConfig,
                        $mode);
                }
            } else {
                $recordsToDelete[$extensionName] = false;
            }
        }

        return $recordsToDelete;
    }

    /**
     * @param string $table Table name present in $GLOBALS['TCA']
     * @param array $tableConfig
     * @param string $mode
     *
     * @return int
     */
    protected function getRecordCount($table, $tableConfig, $mode)
    {
        //@deprecated, will be removed when 7.6 LTS end of life occurs
        if (!GeneralUtility::compat_version('8.7.0')) {
            return $this->getRecordCountLegacy($table, $tableConfig, $mode);
        }

        if (!empty($GLOBALS['TCA'][$table])) {
            $queryBuilder = $this->getQueryBuilderForTable($table);

            // do not use enabled fields here
            $queryBuilder->getRestrictions()->removeAll();

            switch ($mode) {
                default:
                case self::MODE_SELECT:
                    // set table and where clause
                    $queryBuilder
                        ->select($tableConfig['select'] ?? 'uid')
                        ->from($table);
                    break;

                case self::MODE_DELETE:
                    $queryBuilder->delete($table);
                    break;

                case self::MODE_ANONYMIZE:
                    if (empty($tableConfig['anonymize'])) {
                        // may not be defined for certain tables, so skip this table then
                        return 0;
                    } else {
                        $queryBuilder->update($table);
                        foreach ($tableConfig['anonymize'] as $fieldName => $newValue) {
                            $queryBuilder->set($fieldName, $newValue);
                        }

                        // set time values to 0 to "mark" handled records
                        foreach ($this->ctrlTimeFields as $ctrlField) {
                            if (!empty($GLOBALS['TCA'][$table]['ctrl'][$ctrlField])) {
                                $queryBuilder->set($GLOBALS['TCA'][$table]['ctrl'][$ctrlField], 0);
                            }
                        }
                    }
                    break;
            }

            // add custom where clause
            if ($tableConfig['andWhere']) {
                $queryBuilder->andWhere(QueryHelper::stripLogicalOperatorPrefix($tableConfig['andWhere']));
            }

            // all deleted items?
            if ($tableConfig['allDeleted'] && !empty($GLOBALS['TCA'][$table]['ctrl']['delete'])) {
                $queryBuilder->orWhere(
                    $queryBuilder->expr()->eq(
                        $GLOBALS['TCA'][$table]['ctrl']['delete'],
                        $queryBuilder->createNamedParameter(1, \PDO::PARAM_INT)
                    )
                );
            }

            // add where part to skip handled records
            foreach ($this->ctrlTimeFields as $ctrlField) {
                if (!empty($GLOBALS['TCA'][$table]['ctrl'][$ctrlField])) {
                    $queryBuilder->andWhere(
                        $queryBuilder->expr()->neq(
                            $GLOBALS['TCA'][$table]['ctrl'][$ctrlField],
                            $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
                        )
                    );
                }
            }

            $result = $queryBuilder->execute();
            if ($mode == self::MODE_SELECT) {
                return $result->rowCount();
            } else {
                return $result;
            }
        }

        return 0;
    }

    /**
     * @see getRecordCount
     * @deprecated, will be removed when 7.6 LTS end of life occurs
     */
    protected function getRecordCountLegacy($table, $tableConfig, $mode)
    {
        if (!empty($GLOBALS['TCA'][$table])) {
            /* @var $db DatabaseConnection */
            $db = $GLOBALS['TYPO3_DB'];

            $where = '';
            // add custom where clause
            if ($tableConfig['andWhere']) {
                $where .= '(' . $tableConfig['andWhere'] . ')';
            }

            // all deleted items?
            if ($tableConfig['allDeleted'] && !empty($GLOBALS['TCA'][$table]['ctrl']['delete'])) {
                if (!empty($where)) {
                    $where .= ' OR';
                }

                $where .= ' ' . $table . '.' . $GLOBALS['TCA'][$table]['ctrl']['delete'] . ' = 1';
            }

            // add where part to skip handled records
            $whereAnds = [];
            foreach ($this->ctrlTimeFields as $ctrlField) {
                if (!empty($GLOBALS['TCA'][$table]['ctrl'][$ctrlField])) {
                    $whereAnds[] = $table . '.' . $GLOBALS['TCA'][$table]['ctrl'][$ctrlField] . ' != 0';
                }
            }
            if (!empty($whereAnds)) {
                if (!empty($where)) {
                    $where = '(' . $where . ') AND ';
                }
                $where .= implode(' AND ', $whereAnds);
            }

            switch ($mode) {
                default:
                case self::MODE_SELECT:
                    return $db->exec_SELECTcountRows(
                        $tableConfig['select'] ?? 'uid',
                        $table,
                        $where
                    );
                    break;

                case self::MODE_DELETE:
                    $res = $db->exec_DELETEquery($table, $where);
                    if (false !== $res) {
                        return $db->sql_affected_rows();
                    } else {
                        return 0;
                    }
                    break;

                case self::MODE_ANONYMIZE:
                    if (empty($tableConfig['anonymize'])) {
                        // may not be defined for certain tables, so skip this table then
                        return 0;
                    } else {
                        $fieldsValues = $tableConfig['anonymize'];

                        // set time values to 0 to "mark" handled records
                        foreach ($this->ctrlTimeFields as $ctrlField) {
                            if (!empty($GLOBALS['TCA'][$table]['ctrl'][$ctrlField])) {
                                $fieldsValues[$GLOBALS['TCA'][$table]['ctrl'][$ctrlField]] = 0;
                            }
                        }

                        $res = $db->exec_UPDATEquery(
                            $table,
                            $where,
                            $fieldsValues
                        );
                        if (false !== $res) {
                            return $db->sql_affected_rows();
                        } else {
                            return 0;
                        }
                    }
                    break;
            }
        }
        return 0;
    }

    /**
     * @param string $table
     *
     * @return QueryBuilder
     */
    protected function getQueryBuilderForTable($table)
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
    }

    /**
     * @return array
     */
    public function getTsConfiguration(): array
    {
        return $this->tsConfiguration;
    }

    /**
     * @param array $tsConfiguration
     */
    public function setTsConfiguration(array $tsConfiguration)
    {
        $this->tsConfiguration = $tsConfiguration;
    }

}