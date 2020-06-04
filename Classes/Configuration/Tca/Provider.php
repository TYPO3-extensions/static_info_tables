<?php

namespace SJBR\StaticInfoTables\Configuration\Tca;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 Manuel Selbach <manuel_selbach@yahoo.de>
 *  (c) 2020 Stanislas Rolland <typo32020@sjbr.ca>
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
 *  A copy is found in the text file GPL.txt and important notices to the license
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

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class Provider
{
    /**
     * @var string Path to language file of labels in the backend
     */
    protected static $LL = 'LLL:EXT:%s/Resources/Private/Language/locallang_db.xlf:%s_item.%s';

    /**
     * @param $additionalFields
     * @param $tableName
     * @return void
     */
    public static function addTcaColumnConfiguration($extensionKey, $tableName, $additionalFields)
    {
        foreach ($additionalFields as $sourceField => $destField) {
            $additionalColumns = [];
            $additionalColumns[$destField] = $GLOBALS['TCA'][$tableName]['columns'][$sourceField];
            $additionalColumns[$destField]['label'] = sprintf(
                static::$LL,
                $extensionKey,
                $tableName,
                $destField
            );
            ExtensionManagementUtility::addTCAcolumns($tableName, $additionalColumns);
            ExtensionManagementUtility::addToAllTCAtypes(
                $tableName,
                $destField,
                '',
                'after:' . $sourceField
            );
            // Add as search field
            $GLOBALS['TCA'][$tableName]['ctrl']['searchFields'] .= ',' . $destField;
        }
    }
}