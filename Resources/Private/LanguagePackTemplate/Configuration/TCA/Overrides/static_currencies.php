<?php
defined('TYPO3_MODE') or die();

$additionalFields = [
    'cu_name_en' => 'cu_name_###LANG_ISO_LOWER###',
    'cu_sub_name_en' => 'cu_sub_name_###LANG_ISO_LOWER###',
];
foreach ($additionalFields as $sourceField => $destField) {
    $additionalColumns = [];
    $additionalColumns[$destField] = $GLOBALS['TCA']['static_currencies']['columns'][$sourceField];
    $additionalColumns[$destField]['label'] = 'LLL:EXT:static_info_tables_###LANG_ISO_LOWER###/Resources/Private/Language/locallang_db.xlf:static_currencies_item.' . $destField;
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('static_currencies', $additionalColumns);
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('static_currencies', $destField, '', 'after:' . $sourceField);
    // Add as search field
    $GLOBALS['TCA']['static_currencies']['ctrl']['searchFields'] .= ',' . $destField;
}
unset($additionalColumns, $additionalFields);
