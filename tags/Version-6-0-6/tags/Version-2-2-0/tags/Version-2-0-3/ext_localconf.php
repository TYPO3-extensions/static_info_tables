<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


if (!defined ('STATIC_INFO_TABLES_EXTkey')) {
	define('STATIC_INFO_TABLES_EXTkey',$_EXTKEY);
}

if (!defined ('PATH_BE_staticinfotables')) {
	define('PATH_BE_staticinfotables', t3lib_extMgm::extPath(STATIC_INFO_TABLES_EXTkey));
}

if (!defined ('PATH_BE_staticinfotables_rel')) {
	define('PATH_BE_staticinfotables_rel', t3lib_extMgm::extRelPath(STATIC_INFO_TABLES_EXTkey));
}

if (!defined ('DIV_EXTkey')) {
	define('DIV_EXTkey','div');
}

if (t3lib_extMgm::isLoaded(DIV_EXTkey)) {
	if (!defined ('PATH_BE_div')) {
		define('PATH_BE_div', t3lib_extMgm::extPath(DIV_EXTkey));
	}
}

$TYPO3_CONF_VARS['EXTCONF'][STATIC_INFO_TABLES_EXTkey]['charset'] = 'utf-8';

$TYPO3_CONF_VARS['EXTCONF'][STATIC_INFO_TABLES_EXTkey]['tables'] = array(
	'static_territories' => array(
		'label_fields' => array(	// possible label fields for different languages. Default as last.
			'tr_name_##', 'tr_name_en',
		),
		'isocode_field' => array(
			'tr_iso_##',
		),
	),
	'static_countries' => array(
		'label_fields' => array(
			'cn_short_##', 'cn_short_en',
		),
		'isocode_field' => array(
			'cn_iso_##',
		),
	),
	'static_country_zones' => array(
		'label_fields' => array(
			'zn_name_##', 'zn_name_local',
		),
		'isocode_field' => array(
			'zn_code', 'zn_country_iso_##',
		),
	),
	'static_languages' => array(
		'label_fields' => array(
			'lg_name_##', 'lg_name_en',
		),
		'isocode_field' => array(
			'lg_iso_##', 'lg_country_iso_##',
		),
	),
	'static_currencies' => array(
		'label_fields' => array(
			'cu_name_##', 'cu_name_en',
		),
		'isocode_field' => array(
			'cu_iso_##',
		),
	),
	'static_taxes' => array(
		'label_fields' => array(
			'tx_name_##', 'tx_name_en',
		),
		'isocode_field' => array(
			'tx_code', 'tx_country_iso_##', 'tx_zn_code',
		),
	),
	'static_markets' => array(
		'label_fields' => array(
			'institution_description',
		),
		'isocode_field' => array(
			'institution_description',
		),
	),
);

require_once(t3lib_extMgm::extPath(STATIC_INFO_TABLES_EXTkey).'class.tx_staticinfotables_div.php');

?>