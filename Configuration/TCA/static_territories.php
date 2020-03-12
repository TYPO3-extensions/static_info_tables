<?php
// UN Territory reference data
return [
    'ctrl' => [
        'label' => 'tr_name_en',
        'label_alt' => 'tr_iso_nr',
        'label_alt_force' => 1,
        'label_userFunc' => 'SJBR\\StaticInfoTables\\Hook\\Backend\\Form\\FormDataProvider\\TcaLabelProcessor->addIsoCodeToLabel',
        'adminOnly' => true,
        'rootLevel' => 1,
        'is_static' => 1,
        'readOnly' => 1,
        'default_sortby' => 'ORDER BY tr_name_en',
        'delete' => 'deleted',
        'title' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_territories.title',
        'iconfile' => 'EXT:static_info_tables/Resources/Public/Images/Icons/static_territories.svg',
        'searchFields' => 'tr_name_en',
    ],
    'interface' => [
        'showRecordFieldList' => 'tr_iso_nr,tr_parent_iso_nr,tr_name_en',
    ],
    'columns' => [
        'deleted' => [
            'readonly' => 1,
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:deleted',
            'config' => [
                'type' => 'check',
            ],
        ],
        'tr_iso_nr' => [
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_territories_item.tr_iso_nr',
            'exclude' => '0',
            'config' => [
                'type' => 'input',
                'size' => '7',
                'max' => '7',
                'eval' => 'int',
                'default' => '0',
            ],
        ],
        'tr_parent_territory_uid' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_territories_item.tr_parent_territory_uid',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'static_territories',
                'foreign_table_where' => 'ORDER BY static_territories.tr_name_en',
                'itemsProcFunc' => 'SJBR\\StaticInfoTables\\Hook\\Backend\\Form\\FormDataProvider\\TcaSelectItemsProcessor->translateTerritoriesSelector',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'tr_parent_iso_nr' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'tr_name_en' => [
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
    ],
    'types' => [
        '1' => [
            'showitem' => 'tr_iso_nr,tr_name_en,fk_billing_country,--palette--;;1;;',
        ],
    ],
    'palettes' => [
        '1' => [
            'showitem' => 'tr_parent_territory_uid,tr_parent_iso_nr',
            'canNotCollapse' => '1',
        ],
    ],
];
