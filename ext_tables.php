<?php
defined('TYPO3_MODE') or die();

if (TYPO3_MODE == 'BE' && !(TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_INSTALL)) {
    /**
     * Registers the Static Info Tables Manager backend module, if enabled
     */
    if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['static_info_tables']['enableManager']) {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            'SJBR.static_info_tables',
            // Make module a submodule of 'tools'
            'tools',
            // Submodule key
            'Manager',
            // Position
            '',
            // An array holding the controller-action combinations that are accessible
            [
                'Manager' => 'information,newLanguagePack,createLanguagePack,testForm,testFormResult,sqlDumpNonLocalizedData',
            ],
            [
                'access' => 'user,group',
                'icon' => 'EXT:static_info_tables/Resources/Public/Icons/Extension.svg',
                'labels' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_mod.xlf',
            ]
        );
        // Add module configuration setup
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
            'static_info_tables',
            'setup',
            '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:static_info_tables/Configuration/TypoScript/Manager/setup.txt">'
        );
    }
}
