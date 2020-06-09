<?php
/**
 * Extension Manager configuration file for ext "static_info_tables_###LANG_ISO_LOWER###"
 */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Static Info Tables (###LANG_ISO_LOWER###)',
    'description' => '###LANG_NAME### (###LANG_ISO_LOWER###) language pack for the Static Info Tables providing localized names for countries, currencies and so on.',
    'category' => 'misc',
    'version' => '###VERSION###',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'author' => '###AUTHOR###',
    'author_email' => '###AUTHOR_EMAIL###',
    'author_company' => '###AUTHOR_COMPANY###',
    'constraints' => [
        'depends' => [
            'typo3' => '###TYPO3_VERSION_RANGE###',
            'static_info_tables' => '###VERSION_BASE###-',
        ],
        'conflicts' => [],
        'suggests' => [],
    ]
];
