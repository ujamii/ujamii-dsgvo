<?php

namespace Ujamii\UjamiiDsgvo\Service;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\QueryHelper;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class DbOperationsService
 * @package Ujamii\UjamiiDsgvo\Service
 */
class DbOperationsService {

	const MODE_SELECT = 'select';
	const MODE_DELETE = 'delete';

	/**
	 * TsConfig configuration
	 *
	 * @var array
	 */
	protected $tsConfiguration = [];

	/**
	 * @param string $mode
	 *
	 * @return array
	 */
	public function getDbCheckResult( $mode = self::MODE_SELECT ) {
		$recordsToDelete = [];

		foreach ( $this->tsConfiguration['settings']['db'] as $extensionName => $tables ) {
			if ( ExtensionManagementUtility::isLoaded( $extensionName ) ) {
				foreach ( $tables as $tableName => $tableConfig ) {
					$recordsToDelete[ $extensionName ][ $tableName ] = $this->getRecordCount( $tableName, $tableConfig, $mode );
				}
			} else {
				$recordsToDelete[ $extensionName ] = false;
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
	protected function getRecordCount( $table, $tableConfig, $mode ) {
		if ( ! empty( $GLOBALS['TCA'][ $table ] ) ) {
			$queryBuilder = $this->getQueryBuilderForTable( $table );

			// do not use enabled fields here
			$queryBuilder->getRestrictions()->removeAll();

			switch ( $mode ) {
				default:
				case self::MODE_SELECT:
					// set table and where clause
					$queryBuilder
						->select( $tableConfig['select'] ?? 'uid' )
						->from( $table );
					break;

				case self::MODE_DELETE:
					$queryBuilder->delete( $table );
					break;
			}

			// add custom where clause
			if ( $tableConfig['andWhere'] ) {
				$queryBuilder->andWhere( QueryHelper::stripLogicalOperatorPrefix( $tableConfig['andWhere'] ) );
			}

			// all deleted items?
			if ( $tableConfig['allDeleted'] && ! empty( $GLOBALS['TCA'][ $table ]['ctrl']['delete'] ) ) {
				$queryBuilder->orWhere(
					$queryBuilder->expr()->eq(
						$GLOBALS['TCA'][ $table ]['ctrl']['delete'],
						$queryBuilder->createNamedParameter( 1, \PDO::PARAM_INT )
					)
				);
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
	 * @param string $table
	 *
	 * @return QueryBuilder
	 */
	protected function getQueryBuilderForTable( $table ) {
		return GeneralUtility::makeInstance( ConnectionPool::class )->getQueryBuilderForTable( $table );
	}

	/**
	 * @return array
	 */
	public function getTsConfiguration(): array {
		return $this->tsConfiguration;
	}

	/**
	 * @param array $tsConfiguration
	 */
	public function setTsConfiguration( array $tsConfiguration ) {
		$this->tsConfiguration = $tsConfiguration;
	}

}