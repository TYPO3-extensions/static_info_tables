<?php
// Country subdivision reference data from ISO 3166-2
return array(
	'ctrl' => array(
		'label' => 'zn_name_local',
		'label_alt' => 'zn_name_local,zn_code',
		'adminOnly' => 1,
		'rootLevel' => 1,
		'is_static' => 1,
		'readOnly' => 1,
		'default_sortby' => 'ORDER BY zn_name_local',
		'delete' => 'deleted',
		'title' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_country_zones.title',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('static_info_tables') . 'Resources/Public/Images/Icons/icon_static_countries.gif',
		'searchFields' => 'zn_name_en,zn_name_local'
	),
	'interface' => array(
		'showRecordFieldList' => 'zn_country_iso_nr,zn_country_iso_2,zn_country_iso_3,zn_code,zn_name_local,zn_name_en'
	),
	'columns' => array(
		'deleted' => array(
			'readonly' => 1,
			'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:deleted',
			'config' => array(
				'type' => 'check'
			)
		),
		'zn_country_uid' => array(
			'config' => array(
				'type' => 'passthrough',
			)
		),
		'zn_country_table' => array(
			'config' => array(
				'type' => 'passthrough',
			)
		),
		'zn_country_iso_nr' => array(
			'config' => array(
				'type' => 'passthrough',
			)
		),
		'zn_country_iso_2' => array(
			'config' => array(
				'type' => 'passthrough',
			)		
		),
		'zn_country_iso_3' => array(
			'config' => array(
				'type' => 'passthrough',
			)
		),
		'zn_code' => array(
			'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_country_zones_item.zn_code',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '18',
				'max' => '45',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		),
		'zn_name_local' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.name',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '18',
				'max' => '45',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		),
		'zn_name_en' => array(
			'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_country_zones_item.zn_name_en',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '18',
				'max' => '45',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		),
	),
	'types' => array(
		'1' => array(
			'showitem' => 'zn_name_local,zn_code,--palette--;;1;;,zn_name_en'
		)
	),
	'palettes'	=> array(
		'1' => array(
			'showitem' => 'zn_country_uid,zn_country_iso_nr,zn_country_iso_2,zn_country_iso_3', 'canNotCollapse' => '1'
		)
	)
);