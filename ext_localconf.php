<?php
defined('TYPO3_MODE') or die();

call_user_func(
    function ($extKey) {
    	// Get TYPO3 branch
    	$typo3Version = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class);
		$typo3Branch = $typo3Version->getBranch();
        // Get the extensions's configuration
        $extConf = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)->get($extKey);
        // Register cache static_info_tables
        if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$extKey])) {
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$extKey] = [];
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$extKey]['groups'] = ['all'];
        }
        if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['static_info_tables']['frontend'])) {
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$extKey]['frontend'] =
                \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class;
        }
        if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['static_info_tables']['backend'])) {
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$extKey]['backend'] =
                \TYPO3\CMS\Core\Cache\Backend\FileBackend::class;
        }
        // Configure clear cache post processing for extended domain model
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc']['static_info_tables'] =
            \SJBR\StaticInfoTables\Cache\ClassCacheManager::class . '->reBuild';
        // Names of static entities
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extKey]['entities'] =
            ['Country', 'CountryZone', 'Currency', 'Language', 'Territory'];
        // Register cached domain model classes autoloader
        require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extKey)
            . 'Classes/Cache/CachedClassLoader.php');
        \SJBR\StaticInfoTables\Cache\CachedClassLoader::registerAutoloader();
        // Possible label fields for different languages. Default as last.
        $labelTable = [
            'static_territories' => [
                'label_fields' => [
                    'tr_name_##',
                    'tr_name_en'
                ],
                'isocode_field' => [
                    'tr_iso_##'
                ]
            ],
            'static_countries' => [
                'label_fields' => [
                    'cn_short_##',
                    'cn_short_en'
                ],
                'isocode_field' => [
                    'cn_iso_##'
                ]
            ],
            'static_country_zones' => [
                'label_fields' => [
                    'zn_name_##',
                    'zn_name_local'
                ],
                'isocode_field' => [
                    'zn_code',
                    'zn_country_iso_##'
                ]
            ],
            'static_languages' => [
                'label_fields' => [
                    'lg_name_##',
                    'lg_name_en'
                ],
                'isocode_field' => [
                    'lg_iso_##',
                    'lg_country_iso_##'
                ]
            ],
            'static_currencies' => [
                'label_fields' => [
                    'cu_name_##',
                    'cu_name_en'
                ],
                'isocode_field' => [
                    'cu_iso_##'
                ]
            ]
        ];
        if (isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extKey]['tables'])
            && is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extKey]['tables'])) {
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extKey]['tables'] =
                array_merge($labelTable, $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extKey]['tables']);
        } else {
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extKey]['tables'] = $labelTable;
        }
        // Add data handling hook to manage ISO codes redundancies on records
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] =
            \SJBR\StaticInfoTables\Hook\Core\DataHandling\ProcessDataMap::class;
        // Register slot for AfterExtensionInstall signal
        $dispatcher =
            \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);
        $dispatcher->connect(
            \TYPO3\CMS\Extensionmanager\Utility\InstallUtility::class,
            'afterExtensionInstall',
            \SJBR\StaticInfoTables\Slot\Extensionmanager\AfterExtensionInstall::class,
            'executeUpdateScript'
        );
        // Enabling the Static Info Tables Manager module
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extKey]['enableManager'] =
            isset($extConf['enableManager']) ? $extConf['enableManager'] : '0';
        // Make the extension version and constraints available when creating language packs and to other extensions
        $emConfUtility = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extensionmanager\Utility\EmConfUtility::class);
        if (version_compare($typo3Branch, '10.4', '>=')) {
            $emConf =
                $emConfUtility->includeEmConf(
                    $extKey,
                    [
                        'packagePath' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extKey)
                    ]
                );
        } else {
            $emConf =
                $emConfUtility->includeEmConf([
                    'key' => $extKey,
                    'siteRelPath' => \TYPO3\CMS\Core\Utility\PathUtility::stripPathSitePrefix(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extKey))
                ]);
        }
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extKey]['version'] = $emConf['version'];
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extKey]['constraints'] = $emConf['constraints'];
        // Configure translation of suggestions labels
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:'
            . $extKey . '/Configuration/PageTSconfig/Suggest.tsconfig">');
        // In backend lists, order records according to language field of current language
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][\TYPO3\CMS\Recordlist\RecordList\DatabaseRecordList::class]['modifyQuery'][] =
                \SJBR\StaticInfoTables\Hook\Backend\Recordlist\ModifyQuery::class;
    },
    'static_info_tables'
);