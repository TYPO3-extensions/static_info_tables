<?php
namespace SJBR\StaticInfoTables\Cache;

/***************************************************************
 *  Copyright notice
 *  (c) 2012 Georg Ringer <typo3@ringerge.org>
 *  (c) 2013-2018 Stanislas Rolland <typo3(arobas)sjbr.ca>
 *  All rights reserved
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class Cache Manager
 */
class ClassCacheManager implements SingletonInterface
{
    /**
     * Extension key
     *
     * @var string
     */
    protected $extensionKey = 'static_info_tables';

    /**
     * @var array Cache configurations
     */
    protected $cacheConfiguration = [
        'static_info_tables' => [
            'frontend' => 'TYPO3\\CMS\\Core\\Cache\\Frontend\\PhpFrontend',
            'backend' => 'TYPO3\\CMS\\Core\\Cache\\Backend\\FileBackend',
            'options' => [],
            'groups' => ['all'],
        ],
    ];

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \TYPO3\CMS\Core\Cache\CacheManager
     */
    protected $cacheManager;

    /**
     * @var \TYPO3\CMS\Core\Cache\Frontend\FrontendInterface
     */
    protected $cacheInstance;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->initializeCache();
    }

    /**
     * Initialize cache instance to be ready to use
     *
     * @return void
     */
    protected function initializeCache()
    {
        $this->cacheManager = $this->objectManager->get(CacheManager::class);
        if (!$this->cacheManager->hasCache($this->extensionKey)) {
            if (is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$this->extensionKey])) {
                ArrayUtility::mergeRecursiveWithOverrule($this->cacheConfiguration[$this->extensionKey], $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$this->extensionKey]);
            }
            $this->cacheManager->setCacheConfigurations($this->cacheConfiguration);
        }
        $this->cacheInstance = $this->cacheManager->getCache($this->extensionKey);
    }

    /**
     * Builds and caches the proxy files
     *
     * @return void
     *
     * @throws \Exception
     */
    public function build()
    {
        $extensibleExtensions = $this->getExtensibleExtensions();
        $entities = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extensionKey]['entities'];
        foreach ($entities as $entity) {
            $key = 'Domain/Model/' . $entity;

            // Get the file from static_info_tables itself, this needs to be loaded as first
            $path = ExtensionManagementUtility::extPath($this->extensionKey) . 'Classes/' . $key . '.php';
            if (!is_file($path)) {
                throw new \Exception('given file "' . $path . '" does not exist');
            }
            $code = $this->parseSingleFile($path, false);

            // Get the files from all other extensions that are extending this domain model class
            if (isset($extensibleExtensions[$key]) && is_array($extensibleExtensions[$key]) && count($extensibleExtensions[$key]) > 0) {
                $extensionsWithThisClass = array_keys($extensibleExtensions[$key]);
                foreach ($extensionsWithThisClass as $extension) {
                    $path = ExtensionManagementUtility::extPath($extension) . 'Classes/' . $key . '.php';
                    if (is_file($path)) {
                        $code .= $this->parseSingleFile($path);
                    }
                }
            }

            // Close the class definition and the php tag
            $code =  $this->closeClassDefinition($code);

            // The file is added to the class cache
            $entryIdentifier = str_replace('/', '', $key);
            try {
                $this->cacheInstance->set($entryIdentifier, $code);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }
    }

    /**
     * Get all loaded extensions which try to extend EXT:static_info_tables
     *
     * @return array
     */
    protected function getExtensibleExtensions()
    {
        $loadedExtensions = array_unique(ExtensionManagementUtility::getLoadedExtensionListArray());

        // Get the extensions which want to extend static_info_tables
        $extensibleExtensions = [];
        foreach ($loadedExtensions as $extensionKey) {
            $extensionInfoFile = ExtensionManagementUtility::extPath($extensionKey) . 'Configuration/DomainModelExtension/StaticInfoTables.txt';
            if (file_exists($extensionInfoFile)) {
                $info = GeneralUtility::getUrl($extensionInfoFile);
                $classes = GeneralUtility::trimExplode(LF, $info, true);
                foreach ($classes as $class) {
                    $extensibleExtensions[$class][$extensionKey] = 1;
                }
            }
        }
        return $extensibleExtensions;
    }

    /**
     * Parse a single file and does some magic
     * - Remove the php tags
     * - Remove the class definition (if set)
     *
     * @param string $filePath path of the file
     * @param bool $removeClassDefinition If class definition should be removed
     *
     * @return string path of the saved file
     *
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    public function parseSingleFile($filePath, $removeClassDefinition = true)
    {
        if (!is_file($filePath)) {
            throw new \InvalidArgumentException(sprintf('File "%s" could not be found', $filePath));
        }
        $code = GeneralUtility::getUrl($filePath);
        return $this->changeCode($code, $filePath, $removeClassDefinition);
    }

    /**
     * @param string $code
     * @param string $filePath
     * @param bool $removeClassDefinition
     * @param bool $renderPartialInfo
     *
     * @return string
     *
     * @throws \Exception
     */
    protected function changeCode($code, $filePath, $removeClassDefinition = true, $renderPartialInfo = true)
    {
        if (empty($code)) {
            throw new \InvalidArgumentException(sprintf('File "%s" could not be fetched or is empty', $filePath));
        }
        $code = trim($code);
        $code = str_replace(['<?php', '?>'], '', $code);
        $code = trim($code);

        // Remove everything before 'class ', including namespaces,
        // comments and require-statements.
        if ($removeClassDefinition) {
            $pos = strpos($code, 'class ');
            $pos2 = strpos($code, '{', $pos);

            $code = substr($code, $pos2 + 1);
        }

        $code = trim($code);

        // Add some information for each partial
        if ($renderPartialInfo) {
            $code = $this->getPartialInfo($filePath) . $code;
        }

        // Remove last }
        $pos = strrpos($code, '}');
        $code = substr($code, 0, $pos);
        $code = trim($code);
        return $code . LF . LF;
    }

    protected function getPartialInfo($filePath)
    {
        return '/*' . str_repeat('*', 70) . LF .
            ' * this is partial from: ' . $filePath . LF . str_repeat('*', 70) . '*/' . LF . chr(9);
    }

    protected function closeClassDefinition($code)
    {
        return $code . LF . '}';
    }

    /**
     * Clear the class cache
     *
     * @return void
     */
    public function clear()
    {
        $this->cacheInstance->flush();
        if (isset($GLOBALS['BE_USER'])) {
            $GLOBALS['BE_USER']->writelog(3, 1, 0, 0, '[StaticInfoTables]: User %s has cleared the class cache', [$GLOBALS['BE_USER']->user['username']]);
        }
    }

    /**
     * Rebuild the class cache
     *
     * @param array $parameters
     * @return void
     * @throws \Exception
     */
    public function reBuild(array $parameters = [])
    {
        $isValidCall = (
            empty($parameters)
            || (
                !empty($parameters['cacheCmd'])
                && GeneralUtility::inList('all,temp_cached', $parameters['cacheCmd'])
                && isset($GLOBALS['BE_USER'])
            )
        );
        if ($isValidCall) {
            $this->clear();
            $this->clearReflectionCache();
            $this->build();
        }
    }

    /**
     * Drop the reflection cache
     */
    protected function clearReflectionCache()
    {
        if ($this->cacheManager->hasCache('extbase_reflection')) {
            $this->cacheManager->getCache('extbase_reflection')->flush();
        }
        if ($this->cacheManager->hasCache('extbase_datamapfactory_datamap')) {
            $this->cacheManager->getCache('extbase_datamapfactory_datamap')->flush();
        }
    }
}
