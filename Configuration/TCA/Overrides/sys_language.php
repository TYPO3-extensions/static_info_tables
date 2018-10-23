<?php
defined('TYPO3_MODE') or die();

// Configure static_lang_isocode field in TCA
$GLOBALS['TCA']['sys_language']['columns']['static_lang_isocode'] = [
    'exclude' => 1,
    'label' => 'LLL:EXT:lang/locallang_tca.xlf:sys_language.isocode',
    'config' => [
        'type' => 'group',
        'internal_type' => 'db',
        'allowed' => 'static_languages',
        'foreign_table' => 'static_languages',
        'suggestOptions' => [
            'default' => [
                'pidList' => '0',
                'additionalSearchFields' => 'lg_name_local',
            ],
        ],
        'fieldWizard' => [
            'recordsOverview' => [
                'disabled' => true,
            ],
            'tableList' => [
                'disabled' => true,
            ],
        ],
        'size' => 1,
        'minitems' => 0,
        'maxitems' => 1,
    ],
];
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'sys_language',
    'static_lang_isocode',
    '',
    'after:language_isocode'
);
