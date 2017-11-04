<?php
defined('TYPO3_MODE') or die();

// Get the extensions's configuration
$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['static_info_tables']);

// Register cache static_info_tables
if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['static_info_tables'])) {
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['static_info_tables'] = [];
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['static_info_tables']['groups'] = ['all'];
}
if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['static_info_tables']['frontend'])) {
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['static_info_tables']['frontend'] = \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class;
}
if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['static_info_tables']['backend'])) {
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['static_info_tables']['backend'] = \TYPO3\CMS\Core\Cache\Backend\FileBackend::class;
}

// Configure clear cache post processing for extended domain model
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc']['static_info_tables'] = \SJBR\StaticInfoTables\Cache\ClassCacheManager::class . '->reBuild';

// Names of static entities
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['static_info_tables']['entities'] = ['Country', 'CountryZone', 'Currency', 'Language', 'Territory'];

// Register cached domain model classes autoloader
require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('static_info_tables') . 'Classes/Cache/CachedClassLoader.php');
\SJBR\StaticInfoTables\Cache\CachedClassLoader::registerAutoloader();

// Possible label fields for different languages. Default as last.
$labelTable = [
	'static_territories' => [
		'label_fields' => [
			'tr_name_##', 'tr_name_en',
		],
		'isocode_field' => [
			'tr_iso_##',
		]
	],
	'static_countries' => [
		'label_fields' => [
			'cn_short_##', 'cn_short_en',
		],
		'isocode_field' => [
			'cn_iso_##',
		]
	],
	'static_country_zones' => [
		'label_fields' => [
			'zn_name_##', 'zn_name_local',
		],
		'isocode_field' => [
			'zn_code', 'zn_country_iso_##',
		]
	],
	'static_languages' => [
		'label_fields' => [
			'lg_name_##', 'lg_name_en',
		],
		'isocode_field' => [
			'lg_iso_##', 'lg_country_iso_##',
		]
	],
	'static_currencies' => [
		'label_fields' => [
			'cu_name_##', 'cu_name_en',
		],
		'isocode_field' => [
			'cu_iso_##',
		]
	]
];

if (isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['static_info_tables']['tables']) && is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['static_info_tables']['tables'])) {
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['static_info_tables']['tables'] = array_merge($labelTable, $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['static_info_tables']['tables']);
} else {
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['static_info_tables']['tables'] = $labelTable;
}

// Add data handling hook to manage ISO codes redundancies on records
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = \SJBR\StaticInfoTables\Hook\Core\DataHandling\ProcessDataMap::class;

// Register slot for AfterExtensionInstall signal
$dispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);
$dispatcher->connect(\TYPO3\CMS\Extensionmanager\Utility\InstallUtility::class, 'afterExtensionInstall', \SJBR\StaticInfoTables\Slot\Extensionmanager\AfterExtensionInstall::class, 'executeUpdateScript');

// Enabling the Static Info Tables Manager module
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['static_info_tables']['enableManager'] = isset($extConf['enableManager']) ? $extConf['enableManager'] : '0';

// Make the extension version and constraints available when creating language packs and to other extensions
$emConfUtility = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extensionmanager\Utility\EmConfUtility::class);
$emConf = $emConfUtility->includeEmConf(['key' => 'static_info_tables', 'siteRelPath' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('static_info_tables')]);
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['static_info_tables']['version'] = $emConf['version'];
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['static_info_tables']['constraints'] = $emConf['constraints'];

unset($labelTable);
unset($extConf);
unset($dispatcher);
unset($emConfUtility);
unset($emConf);
