<?php
/**
 * Extension Manager configuration file for ext "static_info_tables".
 */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Static Info Tables',
    'description' => 'Data and API for countries, languages and currencies.',
    'category' => 'misc',
    'version' => '6.9.3',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'author' => 'Stanislas Rolland/René Fritz',
    'author_email' => 'typo32020@sjbr.ca',
    'author_company' => 'SJBR',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-10.4.99',
        ]
    ],
    'autoload' => [
        'psr-4' => [
        	'SJBR\\StaticInfoTables\\' => 'Classes'
        ]
    ]
];
