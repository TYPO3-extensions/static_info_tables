<?php
defined('TYPO3_MODE') or die();
// Compatibility with 6.2
if (\TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(\TYPO3\CMS\Core\Utility\VersionNumberUtility::getNumericTypo3Version()) < 7000000) {
	$GLOBALS['TCA']['static_country_zones']['ctrl']['iconfile'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('static_info_tables') . 'Resources/Public/Images/Icons/static_country_zones.svg';
}