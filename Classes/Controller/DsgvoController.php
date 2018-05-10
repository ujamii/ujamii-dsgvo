<?php

namespace Ujamii\UjamiiDsgvo\Controller;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\FormProtection\FormProtectionFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use Ujamii\UjamiiDsgvo\Service\DbOperationsService;

/**
 * Class DsgvoController
 * @package Ujamii\UjamiiDsgvo\Controller
 */
class DsgvoController extends ActionController {

	/**
	 * Backend Template Container
	 *
	 * @var BackendTemplateView
	 */
	protected $defaultViewObjectName = BackendTemplateView::class;

	/**
	 * Page uid
	 *
	 * @var int
	 */
	protected $pageUid = 0;

	/**
	 * TsConfig configuration
	 *
	 * @var array
	 */
	protected $tsConfiguration = [];

	/**
	 * @var \Ujamii\UjamiiDsgvo\Service\DbOperationsService
	 */
	protected $service;

	/**
	 * Function will be called before every other action
	 *
	 */
	public function initializeAction() {
		$this->pageUid         = (int) GeneralUtility::_GET( 'id' );
		$this->setTsConfig();
		parent::initializeAction();

		$this->service = $this->objectManager->get( 'Ujamii\UjamiiDsgvo\Service\DbOperationsService' );
		$this->service->setTsConfiguration( $this->tsConfiguration );
	}

	/**
	 * Index action
	 */
	public function indexAction() {
		$this->view->assignMultiple( [
			'recordsToDelete' => $this->service->getDbCheckResult(),
			'page'            => $this->pageUid,
			'moduleToken'     => $this->getToken( true ),
			'settings'        => $this->tsConfiguration['settings'],
		] );
	}

	/**
	 * @param string $mode See DbOperationsService::MODE_* constants
	 *
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
	 */
	public function deleteAction($mode = DbOperationsService::MODE_ANONYMIZE) {
		$this->service->getDbCheckResult( $mode );
		$this->forward( 'index' );
	}

	/**
	 * Set the TsConfig configuration for the extension
	 *
	 */
	protected function setTsConfig() {
		$tsConfig = BackendUtility::getPagesTSconfig( $this->pageUid );
		if ( isset( $tsConfig['module.']['tx_ujamiidsgvo_dsgvocheck.'] ) && is_array( $tsConfig['module.']['tx_ujamiidsgvo_dsgvocheck.'] ) ) {
			$this->tsConfiguration = GeneralUtility::removeDotsFromTS( $tsConfig['module.']['tx_ujamiidsgvo_dsgvocheck.'] );
		}
	}

	/**
	 * Get a CSRF token
	 *
	 * @param bool $tokenOnly Set it to TRUE to get only the token, otherwise including the &moduleToken= as prefix
	 *
	 * @return string
	 */
	protected function getToken( $tokenOnly = false ) {
		$token = FormProtectionFactory::get()->generateToken( 'moduleCall', 'web_UjamiiDsgvoDsgvocheck' );
		if ( $tokenOnly ) {
			return $token;
		} else {
			return '&moduleToken=' . $token;
		}
	}

}