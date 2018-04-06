<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {
        if (TYPO3_MODE === 'BE') {

            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
                'Ujamii.UjamiiDsgvo',
                'web', // Make module a submodule of 'web'
                'dsgvocheck', // Submodule key
                '', // Position
                [
                    'Dsgvo' => 'index, delete'
                ],
                [
                    'access' => 'user,group',
                    'icon'   => 'EXT:ujamii_dsgvo/ext_icon.svg',
                    'labels' => 'LLL:EXT:ujamii_dsgvo/Resources/Private/Language/locallang_dsgvocheck.xlf',
                ]
            );

        }

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('ujamii_dsgvo', 'Configuration/TypoScript', 'DSGVO Compliance');
	    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
	    	'<INCLUDE_TYPOSCRIPT: source="FILE:EXT:ujamii_dsgvo/Configuration/TypoScript/pagets.ts">'
	    );
    }
);
