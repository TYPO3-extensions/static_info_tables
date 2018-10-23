<?php
namespace SJBR\StaticInfoTables\Hook\Backend\Recordlist;

/*
 *  Copyright notice
 *
 *  (c) 2017 Stanislas Rolland <typo3(arobas)sjbr.ca>
 *  All rights reserved
 *
 *  This script is part of the Typo3 project. The Typo3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 */

use TYPO3\CMS\Core\Database\Query\QueryHelper;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Recordlist\RecordList\AbstractDatabaseRecordList;

/**
 * Order records according to language field of current language
 */
class BuildQueryParameters
{
    /**
     * Specify records order
     *
     * @param array $parameters
     * @param string $table
     * @param int $pageId
     * @param string $additionalConstraints
     * @param string $fieldList
     * @param AbstractDatabaseRecordList $parentObj
     *
     * @return void
     */
    public function buildQueryParametersPostProcess(&$parameters, $table, $pageId, $additionalConstraints, $fieldList, AbstractDatabaseRecordList $parentObj)
    {
        if (in_array($table, array_keys($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['static_info_tables']['tables']))) {
            $lang = substr(strtolower($this->getLanguageService()->lang), 0, 2);
            if (ExtensionManagementUtility::isLoaded('static_info_tables_' . $lang)) {
                $orderBy = str_replace('##', $lang, $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['static_info_tables']['tables'][$table]['label_fields'][0]);
                $parameters['orderBy'] = QueryHelper::parseOrderBy((string)$orderBy);
            }
        }
    }

    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }
}
