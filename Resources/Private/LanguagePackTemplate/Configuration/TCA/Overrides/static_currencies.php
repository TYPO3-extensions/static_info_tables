<?php
defined('TYPO3_MODE') or die();

\SJBR\StaticInfoTables\Configuration\Tca\Provider::addTcaColumnConfiguration(
	'static_info_tables_' . '###LANG_ISO_LOWER###',
	'static_currencies',
	[
		'cu_name_en' => 'cu_name_' . '###LANG_ISO_LOWER###',
		'cu_sub_name_en' => 'cu_sub_name_' . '###LANG_ISO_LOWER###'
	]
);