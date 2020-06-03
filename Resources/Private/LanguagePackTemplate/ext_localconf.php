<?php
defined('TYPO3_MODE') or die();

call_user_func(
    function ($extKey) {
		// Configure translation of suggestions labels
		if (version_compare(TYPO3_branch, '9.5', '>=')) {
			\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
				'<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $extKey . '/Configuration/PageTSconfig/Suggest.tsconfig">'
			);
		} else {
			\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
				'<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $extKey . '/Configuration/PageTSconfig/Suggest_prior_9.tsconfig">'
			);
		}
    },
    'static_info_tables_###LANG_ISO_LOWER###'
);