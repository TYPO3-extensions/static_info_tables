<?php
defined('TYPO3_MODE') or die();

$additionalFields = array(
	'zn_name_en' => 'zn_name_###LANG_ISO_LOWER###'
);
foreach ($additionalFields as $sourceField => $destField) {
	$additionalColumns = array();
	$additionalColumns[$destField] = $GLOBALS['TCA']['static_country_zones']['columns'][$sourceField];
	$additionalColumns[$destField]['label'] = 'LLL:EXT:static_info_tables_###LANG_ISO_LOWER###/Resources/Private/Language/locallang_db.xlf:static_country_zones_item.' . $destField;
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('static_country_zones', $additionalColumns);
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('static_country_zones', $destField, '', 'after:' . $sourceField);
	// Add as search field
	$GLOBALS['TCA']['static_country_zones']['ctrl']['searchFields'] .= ',' . $destField;
}
unset($additionalColumns);
unset($additionalFields);