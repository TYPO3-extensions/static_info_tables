<?php
// Country subdivision reference data from ISO 3166-2
return [
    'ctrl' => [
        'label' => 'zn_name_local',
        'label_alt' => 'zn_name_local,zn_code',
        'adminOnly' => true,
        'rootLevel' => 1,
        'is_static' => 1,
        'readOnly' => 1,
        'default_sortby' => 'ORDER BY zn_name_local',
        'delete' => 'deleted',
        'title' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_country_zones.title',
        'iconfile' => 'EXT:static_info_tables/Resources/Public/Images/Icons/static_country_zones.svg',
        'searchFields' => 'zn_name_en,zn_name_local',
    ],
    'interface' => [
        'showRecordFieldList' => 'zn_country_iso_nr,zn_country_iso_2,zn_country_iso_3,zn_code,zn_name_local,zn_name_en',
    ],
    'columns' => [
        'deleted' => [
            'readonly' => 1,
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:deleted',
            'config' => [
                'type' => 'check',
            ],
        ],
        'zn_country_uid' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'zn_country_table' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'zn_country_iso_nr' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'zn_country_iso_2' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'zn_country_iso_3' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'zn_code' => [
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_country_zones_item.zn_code',
            'exclude' => '0',
            'config' => [
                'type' => 'input',
                'size' => '18',
                'max' => '45',
                'eval' => 'trim',
                'default' => '',
                '_is_string' => '1',
            ],
        ],
        'zn_name_local' => [
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.name',
            'exclude' => '0',
            'config' => [
                'type' => 'input',
                'size' => '18',
                'max' => '45',
                'eval' => 'trim',
                'default' => '',
                '_is_string' => '1',
            ],
        ],
        'zn_name_en' => [
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_country_zones_item.zn_name_en',
            'exclude' => '0',
            'config' => [
                'type' => 'input',
                'size' => '18',
                'max' => '45',
                'eval' => 'trim',
                'default' => '',
                '_is_string' => '1',
            ],
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => 'zn_name_local,zn_code,--palette--;;1;;,zn_name_en',
        ],
    ],
    'palettes' => [
        '1' => [
            'showitem' => 'zn_country_uid,zn_country_iso_nr,zn_country_iso_2,zn_country_iso_3',
            'canNotCollapse' => '1',
        ],
    ],
];
