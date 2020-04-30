<?php
declare(strict_types = 1);

return [
    \SJBR\StaticInfoTables\Domain\Model\Country::class => [
        'tableName' => 'static_countries',
        'properties' => [
            'addressFormat' => [
                'fieldName' => 'cn_address_format'
            ],
            'capitalCity' => [
                'fieldName' => 'cn_capital'
            ],
            'currencyIsoCodeA3' => [
                'fieldName' => 'cn_currency_iso_3'
            ],
            'currencyIsoCodeNumber' => [
                'fieldName' => 'cn_currency_iso_nr'
            ],
            'euMember' => [
                'fieldName' => 'cn_eu_member'
            ],
            'isoCodeA2' => [
                'fieldName' => 'cn_iso_2'
            ],
            'isoCodeA3' => [
                'fieldName' => 'cn_iso_3'
            ],
            'isoCodeNumber' => [
                'fieldName' => 'cn_iso_nr'
            ],
            'officialNameLocal' => [
                'fieldName' => 'cn_official_name_local'
            ],
            'officialNameEn' => [
                'fieldName' => 'cn_official_name_en'
            ],
            'parentTerritoryUnCodeNumber' => [
                'fieldName' => 'cn_parent_tr_iso_nr'
            ],
            'phonePrefix' => [
                'fieldName' => 'cn_phone'
            ],
            'shortNameLocal' => [
                'fieldName' => 'cn_short_local'
            ],
            'shortNameEn' => [
                'fieldName' => 'cn_short_en'
            ],
            'topLevelDomain' => [
                'fieldName' => 'cn_tldomain'
            ],
            'unMember' => [
                'fieldName' => 'cn_uno_member'
            ],
            'zoneFlag' => [
                'fieldName' => 'cn_zone_flag'
            ],
            'countryZones' => [
                'fieldName' => 'cn_country_zones'
            ],
            'deleted' => [
                'fieldName' => 'deleted'
            ],
        ]
    ],
    \SJBR\StaticInfoTables\Domain\Model\CountryZone::class => [
        'tableName' => 'static_country_zones',
        'properties' => [
            'countryIsoCodeA2' => [
                'fieldName' => 'zn_country_iso_2'
            ],
            'countryIsoCodeA3' => [
                'fieldName' => 'zn_country_iso_3'
            ],
            'countryIsoCodeNumber' => [
                'fieldName' => 'zn_country_iso_nr'
            ],
            'isoCode' => [
                'fieldName' => 'zn_code'
            ],
            'localName' => [
                'fieldName' => 'zn_name_local'
            ],
            'nameEn' => [
                'fieldName' => 'zn_name_en'
            ],
            'deleted' => [
                'fieldName' => 'deleted'
            ],
        ]
    ],
    \SJBR\StaticInfoTables\Domain\Model\Currency::class => [
        'tableName' => 'static_currencies',
        'properties' => [
            'decimalDigits' => [
                'fieldName' => 'cu_decimal_digits'
            ],
            'decimalPoint' => [
                'fieldName' => 'cu_decimal_point'
            ],
            'isoCodeA3' => [
                'fieldName' => 'cu_iso_3'
            ],
            'isoCodeNumber' => [
                'fieldName' => 'cu_iso_nr'
            ],
            'nameEn' => [
                'fieldName' => 'cu_name_en'
            ],
            'subdivisionNameEn' => [
                'fieldName' => 'cu_sub_name_en'
            ],
            'subdivisionSymbolLeft' => [
                'fieldName' => 'cu_sub_symbol_left'
            ],
            'subdivisionSymbolRight' => [
                'fieldName' => 'cu_sub_symbol_right'
            ],
            'symbolLeft' => [
                'fieldName' => 'cu_symbol_left'
            ],
            'symbolRight' => [
                'fieldName' => 'cu_symbol_right'
            ],
            'thousandsPoint' => [
                'fieldName' => 'cu_thousands_point'
            ],
            'deleted' => [
                'fieldName' => 'deleted'
            ],
        ]
    ],
    \SJBR\StaticInfoTables\Domain\Model\Language::class => [
        'tableName' => 'static_languages',
        'properties' => [
            'collatingLocale' => [
                'fieldName' => 'lg_collate_locale'
            ],
            'countryIsoCodeA2' => [
                'fieldName' => 'lg_country_iso_2'
            ],
            'constructedLanguage' => [
                'fieldName' => 'lg_constructed'
            ],
            'isoCodeA2' => [
                'fieldName' => 'lg_iso_2'
            ],
            'localName' => [
                'fieldName' => 'lg_name_local'
            ],
            'nameEn' => [
                'fieldName' => 'lg_name_en'
            ],
            'sacredLanguage' => [
                'fieldName' => 'lg_sacred'
            ],
            'typo3Code' => [
                'fieldName' => 'lg_typo3'
            ],
            'deleted' => [
                'fieldName' => 'deleted'
            ],
        ]
    ],
    \SJBR\StaticInfoTables\Domain\Model\Territory::class => [
        'tableName' => 'static_territories',
        'properties' => [
            'unCodeNumber' => [
                'fieldName' => 'tr_iso_nr'
            ],
            'nameEn' => [
                'fieldName' => 'tr_name_en'
            ],
            'parentTerritoryUnCodeNumber' => [
                'fieldName' => 'tr_parent_iso_nr'
            ],
            'deleted' => [
                'fieldName' => 'deleted'
            ],
        ]
    ],
    \SJBR\StaticInfoTables\Domain\Model\SystemLanguage::class => [
        'tableName' => 'sys_language',
        'properties' => [
            'title' => [
                'fieldName' => 'title'
            ],
            'isoLanguage' => [
                'fieldName' => 'static_lang_isocode'
            ],
        ]
    ]
];