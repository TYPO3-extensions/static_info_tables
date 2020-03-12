<?php
defined('TYPO3_MODE') or die();

$additionalFields = [
    'cn_short_en' => 'cn_short_###LANG_ISO_LOWER###',
];
foreach ($additionalFields as $sourceField => $destField) {
    $additionalColumns = [];
    $additionalColumns[$destField] = $GLOBALS['TCA']['static_countries']['columns'][$sourceField];
    $additionalColumns[$destField]['label'] = 'LLL:EXT:static_info_tables_###LANG_ISO_LOWER###/Resources/Private/Language/locallang_db.xlf:static_countries_item.' . $destField;
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('static_countries', $additionalColumns);
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('static_countries', $destField, '', 'after:' . $sourceField);
    // Add as search field
    $GLOBALS['TCA']['static_countries']['ctrl']['searchFields'] .= ',' . $destField;
}
unset($additionalColumns, $additionalFields);
