<?php
namespace SJBR\StaticInfoTables\Utility;

/***************************************************************
*  Copyright notice
*
*  (c) 2014 Tim Lochmüller <tim.lochmueller@hdnet.de>
*  (c) 2014-2018 Stanislas Rolland <typo3(arobas)sjbr.ca>
*
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
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Utility functions used by the domain models
 */
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class ModelUtility
{
    /**
     * Internal Extbase configuration
     *
     * @var array
     */
    private static $extbaseConfiguration = null;

    /**
     * Mapping for the table name
     */
    const MAPPING_TABLENAME = 'tableName';

    /**
     * Mapping for the columns
     */
    const MAPPING_COLUMNS = 'columns';

    /**
     * Get the table mapping from the Extbase configuration
     *
     * @param string $modelName
     * @param string $configurationType
     * @param mixed $mappingType
     *
     * @return string
     */
    public static function getModelMapping($modelName, $mappingType)
    {
        if (self::$extbaseConfiguration === null) {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            $configurationManager = $objectManager->get(ConfigurationManagerInterface::class);
            self::$extbaseConfiguration = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        }
        return self::$extbaseConfiguration['persistence']['classes'][$modelName]['mapping'][$mappingType] ?: null;
    }
}
