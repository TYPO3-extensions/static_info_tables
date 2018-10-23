<?php
defined('TYPO3_MODE') or die();

$additionalFields = [
    'lg_name_en' => 'lg_name_###LANG_ISO_LOWER###',
];
foreach ($additionalFields as $sourceField => $destField) {
    $additionalColumns = [];
    $additionalColumns[$destField] = $GLOBALS['TCA']['static_languages']['columns'][$sourceField];
    $additionalColumns[$destField]['label'] = 'LLL:EXT:static_info_tables_###LANG_ISO_LOWER###/Resources/Private/Language/locallang_db.xlf:static_languages_item.' . $destField;
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('static_languages', $additionalColumns);
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('static_languages', $destField, '', 'after:' . $sourceField);
    // Add as search field
    $GLOBALS['TCA']['static_languages']['ctrl']['searchFields'] .= ',' . $destField;
}
unset($additionalColumns, $additionalFields);
