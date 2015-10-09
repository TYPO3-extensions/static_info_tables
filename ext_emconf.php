<?php
/***************************************************************
 * Extension Manager/Repository config file for ext "static_info_tables".
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Static Info Tables',
	'description' => 'Data and API for countries, languages and currencies.',
	'category' => 'misc',
	'version' => '6.3.1',
	'state' => 'stable',
	'uploadfolder' => FALSE,
	'createDirs' => '',
	'clearcacheonload' => FALSE,
	'author' => 'Stanislas Rolland/René Fritz',
	'author_email' => 'typo3@sjbr.ca',
	'author_company' => 'SJBR',
	'constraints' => array(
		'depends' => array(
			'typo3' => '6.2.0-7.99.99',
			'php' => '5.3.7-0.0.0'
		),
		'conflicts' => array(
			'sr_static_info' => '0.0.0-99.99.99',
			'cc_infotablesmgm' => '0.0.0-99.99.99',
			'uncache' => '0.0.0-99.99.99'
		),
		'suggests' => array(
		)
	)
);