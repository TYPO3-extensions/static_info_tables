<?php

########################################################################
# Extension Manager/Repository config file for ext "static_info_tables".
#
# Auto generated 16-12-2011 20:46
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Static Info Tables',
	'description' => 'Data and API for countries, languages and currencies.',
	'category' => 'misc',
	'shy' => 0,
	'version' => '3.0.0',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'René Fritz',
	'author_email' => 'r.fritz@colorcube.de',
	'author_company' => 'Colorcube - digital media lab, www.colorcube.de',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'typo3' => '6.0.6-0.0.0',
			'php' => '5.3.7-0.0.0',
		),
		'conflicts' => array(
			'sr_static_info' => '',
			'cc_infotablesmgm' => '',
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => ''
);

?>