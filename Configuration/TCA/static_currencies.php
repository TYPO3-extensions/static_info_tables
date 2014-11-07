<?php
// Currency reference data from ISO 4217
return array(
	'ctrl' => array(
		'label' => 'cu_name_en',
		'label_alt' => 'cu_iso_3',
		'label_alt_force' => 1,
		'label_userFunc' => 'SJBR\\StaticInfoTables\\Hook\\Backend\\Form\\ElementRenderingHelper->addIsoCodeToLabel',
		'adminOnly' => 1,
		'rootLevel' => 1,
		'is_static' => 1,
		'readOnly' => 1,
		'default_sortby' => 'ORDER BY cu_name_en',
		'delete' => 'deleted',
		'title' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_currencies.title',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('static_info_tables') . 'Resources/Public/Images/Icons/icon_static_currencies.gif',
		'searchFields' => 'cu_name_en'
	),
	'interface' => array(
		'showRecordFieldList' => 'cu_iso_3,cu_iso_nr,cu_name_en,cu_symbol_left,cu_symbol_right,cu_thousands_point,cu_decimal_point,cu_decimal_digits,cu_sub_name_en,cu_sub_divisor,cu_sub_symbol_left,cu_sub_symbol_right'
	),
	'columns' => array(
		'deleted' => array(
			'readonly' => 1,
			'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:deleted',
			'config' => array(
				'type' => 'check'
			)
		),
		'cu_iso_3' => array(
			'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_currencies_item.cu_iso_3',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '5',
				'max' => '3',
				'eval' => '',
				'default' => ''
			)
		),
		'cu_iso_nr' => array(
			'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_currencies_item.cu_iso_nr',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '7',
				'max' => '3',
				'eval' => '',
				'default' => '0'
			)
		),
		'cu_name_en' => array(
			'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_currencies_item.cu_name_en',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '18',
				'max' => '40',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		),
		'cu_sub_name_en' => array(
			'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_currencies_item.cu_sub_name_en',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '18',
				'max' => '20',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		),
		'cu_symbol_left' => array(
			'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_currencies_item.cu_symbol_left',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '8',
				'max' => '12',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		),
		'cu_symbol_right' => array(
			'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_currencies_item.cu_symbol_right',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '8',
				'max' => '12',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		),
		'cu_thousands_point' => array(
			'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_currencies_item.cu_thousands_point',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '3',
				'max' => '1',
				'eval' => '',
				'default' => ''
			)
		),
		'cu_decimal_point' => array(
			'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_currencies_item.cu_decimal_point',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '3',
				'max' => '1',
				'eval' => '',
				'default' => ''
			)
		),
		'cu_decimal_digits' => array(
			'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_currencies_item.cu_decimal_digits',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '5',
				'max' => '',
				'eval' => 'int',
				'default' => ''
			)
		),
		'cu_sub_divisor' => array(
			'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_currencies_item.cu_sub_divisor',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'int',
				'default' => '1'
			)
		),
		'cu_sub_symbol_left' => array(
			'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_currencies_item.cu_sub_symbol_left',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '8',
				'max' => '12',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		),
		'cu_sub_symbol_right' => array(
			'label' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:static_currencies_item.cu_sub_symbol_right',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '8',
				'max' => '12',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		)
	),
	'types' => array(
		'1' => array(
			'showitem' => 'cu_name_en,--palette--;;1;;,--palette--;;2;;,cu_sub_name_en,--palette--;;3;;'
		)
	),
	'palettes'	=> array(
		'1' => array(
			'showitem' => 'cu_iso_nr,cu_iso_3', 'canNotCollapse' => '1'
		),
		'2' => array(
			'showitem' => 'cu_symbol_left,cu_symbol_right,cu_thousands_point,cu_decimal_point', 'canNotCollapse' => '1'
		),
		'3' => array(
			'showitem' => 'cu_sub_symbol_left,cu_sub_symbol_right,cu_decimal_digits,cu_sub_divisor', 'canNotCollapse' => '1'
		)
	)
);