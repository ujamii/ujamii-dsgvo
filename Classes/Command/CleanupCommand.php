<?php
declare(strict_types=1);

namespace Ujamii\UjamiiDsgvo\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use Ujamii\UjamiiDsgvo\Service\DbOperationsService;

class CleanupCommand extends Command
{

    /**
     * @var DbOperationsService
     */
    protected $service;

    /**
     * @var SymfonyStyle
     */
    private $io;

    public function configure()
    {
        $this->addOption('page-uid', null, InputOption::VALUE_OPTIONAL,
            'The uid of the page where the ts config is read from. If you have different configs for subtrees, use the uid here.', 1);
        $this->addOption('mode', null, InputOption::VALUE_OPTIONAL,
            'See DbOperationsService::MODE_* constants [select|delete|anonymize]".', DbOperationsService::MODE_ANONYMIZE);

        $this->setDescription('Cleans up old and deleted records in the database to comply with the DSGVO rules in Germany, which are based on the privacy shield regulations valid in the whole EU.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $pageUid = $input->getOption('page-uid');
        $mode    = $input->getOption('mode');
        $this->io = new SymfonyStyle($input, $output);

        $tsConfig = BackendUtility::getPagesTSconfig($pageUid);
        if (isset($tsConfig['module.']['tx_ujamiidsgvo_dsgvocheck.']) && is_array($tsConfig['module.']['tx_ujamiidsgvo_dsgvocheck.'])) {
            $tsConfig = GeneralUtility::removeDotsFromTS($tsConfig['module.']['tx_ujamiidsgvo_dsgvocheck.']);

            $this->service = GeneralUtility::makeInstance(DbOperationsService::class);
            $this->service->setTsConfiguration($tsConfig);

            $result = $this->service->getDbCheckResult($mode);
            $this->io->info((new \DateTime())->format('Y-m-d H:i:s'));
            $this->io->info(DebuggerUtility::var_dump($result,
                'FALSE means Extension not installed, integer is amount of handled records.', 8, true, true, true));
        } else {
            $this->io->error('TS could not be loaded!');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

}
