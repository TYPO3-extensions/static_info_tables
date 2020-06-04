<?php
defined('TYPO3_MODE') or die();

\SJBR\StaticInfoTables\Configuration\Tca\Provider::addTcaColumnConfiguration(
	'static_info_tables_' . '###LANG_ISO_LOWER###',
	'static_languages',
	[
		'lg_name_en' => 'lg_name_' . '###LANG_ISO_LOWER###'
	]
);
