<?php
defined('TYPO3_MODE') or die();

\SJBR\StaticInfoTables\Configuration\Tca\Provider::addTcaColumnConfiguration(
	'static_info_tables_' . '###LANG_ISO_LOWER###',
	'static_territories',
	[
		'tr_name_en' => 'tr_name_' . '###LANG_ISO_LOWER###'
	]
);