<?php
defined('TYPO3_MODE') or die();

\SJBR\StaticInfoTables\Configuration\Tca\Provider::addTcaColumnConfiguration(
	'static_info_tables_' . '###LANG_ISO_LOWER###',
	'static_countries',
	[
		'cn_short_en' => 'cn_short_' . '###LANG_ISO_LOWER###'
	]
);