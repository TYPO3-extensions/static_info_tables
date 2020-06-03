<?php
declare(strict_types = 1);

return [
    \SJBR\StaticInfoTables\Domain\Model\Country::class => [
        'tableName' => 'static_countries',
        'properties' => [
            'shortName###LANG_ISO_CAMEL###' => [
                'fieldName' => 'cn_short_###LANG_ISO_LOWER###'
            ]
        ]
    ],
    \SJBR\StaticInfoTables\Domain\Model\CountryZone::class => [
        'tableName' => 'static_country_zones',
        'properties' => [
            'name###LANG_ISO_CAMEL###' => [
                'fieldName' => 'zn_name_###LANG_ISO_LOWER###'
            ]
        ]
    ],
    \SJBR\StaticInfoTables\Domain\Model\Currency::class => [
        'tableName' => 'static_currencies',
        'properties' => [
            'name###LANG_ISO_CAMEL###' => [
                'fieldName' => 'cu_name_###LANG_ISO_LOWER###'
            ],
            'subdivisionName###LANG_ISO_CAMEL###' => [
                'fieldName' => 'cu_sub_name_###LANG_ISO_LOWER###'
            ]
        ]
    ],
    \SJBR\StaticInfoTables\Domain\Model\Language::class => [
        'tableName' => 'static_languages',
        'properties' => [
            'name###LANG_ISO_CAMEL###' => [
                'fieldName' => 'lg_name_###LANG_ISO_LOWER###'
            ]
        ]
    ],
    \SJBR\StaticInfoTables\Domain\Model\Territory::class => [
        'tableName' => 'static_territories',
        'properties' => [
            'name###LANG_ISO_CAMEL###' => [
                'fieldName' => 'tr_name_###LANG_ISO_LOWER###'
            ]
        ]
    ]
];