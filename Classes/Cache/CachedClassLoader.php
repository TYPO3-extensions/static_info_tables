<?php
namespace SJBR\StaticInfoTables\Cache;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Stanislas Rolland <typo3@sjbr.ca>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Cached classes autoloader
 */
class CachedClassLoader
{
    /**
     * Extension key
     *
     * @var string
     */
    protected static $extensionKey = 'static_info_tables';

    /**
     * Cached class loader class name
     *
     * @var string
     */
    protected static $className = __CLASS__;

    /**
     * Name space of the Domain Model of StaticInfoTables
     *
     * @var string
     */
    protected static $namespace = 'SJBR\\StaticInfoTables\\Domain\\Model\\';

    /**
     * The class loader is static, thus we do not allow instances of this class.
     */
    private function __construct()
    {
    }

    /**
     * Registers the cached class loader
     *
     * @return bool TRUE in case of success
     */
    public static function registerAutoloader()
    {
        return spl_autoload_register(static::$className . '::autoload', true, true);
    }

    /**
     * Autoload function for cached classes
     *
     * @param string $className Class name
     *
     * @return void
     */
    public static function autoload($className)
    {
        $className = ltrim($className, '\\');
        if (strpos($className, static::$namespace) !== false) {
            // Lookup the class in the array of static info entities and check its presence in the class cache
            $entities = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][static::$extensionKey]['entities'];
            $objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
            $cacheManager = $objectManager->get('TYPO3\\CMS\\Core\\Cache\\CacheManager');
            // ClassCacheManager instantiation creates the class cache if not already available
            $classCacheManager = $objectManager->get('SJBR\\StaticInfoTables\\Cache\\ClassCacheManager');
            $classCache = $cacheManager->getCache(static::$extensionKey);
            foreach ($entities as $entity) {
                if ($className === static::$namespace . $entity) {
                    $entryIdentifier = 'DomainModel' . $entity;
                    if (!$classCache->has($entryIdentifier)) {
                        // The class cache needs to be rebuilt
                        $classCacheManager->reBuild();
                    }
                    $classCache->requireOnce($entryIdentifier);
                    break;
                }
            }
        }
    }
}
