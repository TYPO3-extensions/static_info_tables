<?php

########################################################################
# Extension Manager/Repository config file for ext: "static_info_tables"
#
# Auto generated 19-12-2006 12:26
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Static Info Tables',
	'description' => 'Some database tables with usefull informations about countries, languages and currencies. Still lacks some data but the overall concept is stable. Install the extension div version 0.0.5 before you make an update!',
	'category' => 'misc',
	'shy' => 0,
	'version' => '2.0.1',
	'dependencies' => 'div',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'René Fritz',
	'author_email' => 'r.fritz@colorcube.de',
	'author_company' => 'Colorcube - digital media lab, www.colorcube.de',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.0-',
			'php' => '4.1.0-',
			'div' => '0.0.5-',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:20:{s:9:"ChangeLog";s:4:"3ef6";s:20:"class.ext_update.php";s:4:"b79e";s:33:"class.tx_staticinfotables_div.php";s:4:"365b";s:38:"class.tx_staticinfotables_encoding.php";s:4:"0361";s:41:"class.tx_staticinfotables_syslanguage.php";s:4:"4c23";s:12:"ext_icon.gif";s:4:"639f";s:17:"ext_localconf.php";s:4:"6e61";s:14:"ext_tables.php";s:4:"f1d8";s:14:"ext_tables.sql";s:4:"1c58";s:25:"ext_tables_static+adt.sql";s:4:"431d";s:25:"icon_static_countries.gif";s:4:"2a46";s:26:"icon_static_currencies.gif";s:4:"a1e2";s:25:"icon_static_languages.gif";s:4:"639f";s:27:"icon_static_territories.gif";s:4:"aab5";s:16:"locallang_db.xml";s:4:"0063";s:7:"tca.php";s:4:"7d88";s:14:"doc/manual.sxw";s:4:"4687";s:37:"pi1/class.tx_staticinfotables_pi1.php";s:4:"9ea3";s:39:"static/static_info_tables/constants.txt";s:4:"aaf6";s:35:"static/static_info_tables/setup.txt";s:4:"82b7";}',
	'suggests' => array(
	),
);

?>