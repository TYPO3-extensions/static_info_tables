<?php
defined('TYPO3_MODE') or die();
// Compatibility with 6.2
if (\TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(\TYPO3\CMS\Core\Utility\VersionNumberUtility::getNumericTypo3Version()) < 7000000) {
	$GLOBALS['TCA']['static_territories']['ctrl']['label_userFunc'] = 'SJBR\\StaticInfoTables\\Hook\\Backend\\Form\\ElementRenderingHelper->addIsoCodeToLabel';
	$GLOBALS['TCA']['static_territories']['ctrl']['iconfile'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('static_info_tables') . 'Resources/Public/Images/Icons/static_territories.svg';
	$GLOBALS['TCA']['static_territories']['columns']['tr_parent_territory_uid']['config']['itemsProcFunc'] = 'SJBR\\StaticInfoTables\\Hook\\Backend\\Form\\ElementRenderingHelper->translateTerritoriesSelector';
	$GLOBALS['TCA']['static_territories']['columns']['tr_parent_territory_uid']['config']['noIconsBelowSelect'] = 1;
}