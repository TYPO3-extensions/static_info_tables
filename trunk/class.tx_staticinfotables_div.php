<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004-2006 René Fritz (r.fritz@colorcube.de)
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
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
 * Misc functions to access the static info tables
 *
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   56: class tx_staticinfotables_div
 *   66:     function getTCAlabelField($table, $loadTCA=true)
 *   95:     function getTCAsortField($table, $loadTCA=true)
 *  105:     function getCurrentLanguage()
 *  175:     function selectItemsTCA($params)
 *  267:     function updateHotlist ($table, $indexValue, $indexField='', $app='')
 *
 * TOTAL FUNCTIONS: 5
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

class tx_staticinfotables_div {

	/**
	 * Returns a label field for the current language
	 *
	 * @param	string		table name
	 * @param	boolean		If set (default) the TCA definition of the table should be loaded with t3lib_div::loadTCA(). It will be needed to set it to false if you call this function from inside of tca.php
	 * @param	boolean		If set, we are looking for the "local" title field
	 * @return	string		field name
	 */
	function getTCAlabelField($table, $loadTCA=TRUE, $lang='', $local=FALSE) {
		global $TYPO3_CONF_VARS, $TCA, $LANG, $TSFE;
		
		if (is_object($LANG)) {
			$csConvObj = $LANG->csConvObj;
		} elseif (is_object($TSFE)) {
			$csConvObj = $TSFE->csConvObj;
		}
		
		$labelFields = array();
		if($table && is_array($TYPO3_CONF_VARS['EXTCONF']['static_info_tables']['tables'][$table]['label_fields'])) {
			if ($loadTCA) {
				t3lib_div::loadTCA($table);
			}
			
			$lang = $lang ? $lang : tx_staticinfotables_div::getCurrentLanguage();
			
			foreach ($TYPO3_CONF_VARS['EXTCONF']['static_info_tables']['tables'][$table]['label_fields'] as $field) {
				if ($local) {
					$labelField = str_replace ('##', 'local', $field);
				} else {
					$labelField = str_replace ('##', $csConvObj->conv_case('utf-8',$lang,'toLower'), $field);
				}
				if (is_array($TCA[$table]['columns'][$labelField])) {
					$labelFields[] = $labelField;
				}
			}
		}
		return $labelFields;
	}

	/**
	 * Returns the type of an iso code: nr, 2, 3
	 *
	 * @param	string		iso code
	 * @return	string		iso code type
	 */
	function isoCodeType($isoCode) {
		$type = '';
		if (t3lib_div::testInt($isoCode)) {
			$type = 'nr';
		} elseif (strlen($isoCode) == 2) {
			$type = '2';
		} elseif (strlen($isoCode) == 3) {
			$type = '3';
		}
		return $type;
	}
	
	/**
	 * Returns a iso code field for the passed table and iso code
	 *
	 * @param	string		table name
	 * @param	string		iso code
	 * @param	boolean		If set (default) the TCA definition of the table should be loaded with t3lib_div::loadTCA(). It will be needed to set it to false if you call this function from inside of tca.php
	 * @return	string		field name
	 */
	function getIsoCodeField($table, $isoCode, $loadTCA=TRUE, $index=0) {
		global $TYPO3_CONF_VARS, $TCA;
		
		if ($isoCode && $table && ($isoCodeField = $TYPO3_CONF_VARS['EXTCONF']['static_info_tables']['tables'][$table]['isocode_field'][$index])) {
			if ($loadTCA) {
				t3lib_div::loadTCA($table);
			}
			
			$type = tx_staticinfotables_div::isoCodeType($isoCode);
			
			$isoCodeField = str_replace ('##', $type, $isoCodeField);
			if (is_array($TCA[$table]['columns'][$isoCodeField])) {
				return $isoCodeField;
			}
		}
		return FALSE;
	}
	
	/**
	 * Returns a sort field for the current language
	 *
	 * @param	string		table name
	 * @param	boolean		If set (default) the TCA definition of the table should be loaded
	 * @return	string		field name
	 */
	function getTCAsortField($table, $loadTCA=TRUE) {
		$labelFields = tx_staticinfotables_div::getTCAlabelField($table, $loadTCA);
		return $labelFields[0];
	}
	
	/**
	 * Returns the current language as iso-2-alpha code
	 *
	 * @return	string		'DE', 'EN', 'DK', ...
	 */
	function getCurrentLanguage() {
		global $LANG, $TSFE, $TYPO3_DB;
		
		if (is_object($LANG)) {
			$langCodeT3 = $LANG->lang;
			$csConvObj = $LANG->csConvObj;
		} elseif (is_object($TSFE)) {
			$langCodeT3 = $TSFE->lang;
			$csConvObj = $TSFE->csConvObj;
		} else {
			return 'EN';
		}
		
		$res = $TYPO3_DB->exec_SELECTquery(
			'lg_iso_2,lg_country_iso_2',
			'static_languages',
			'lg_typo3='.$TYPO3_DB->fullQuoteStr($langCodeT3,'static_languages')
			);
		while ($row = $TYPO3_DB->sql_fetch_assoc($res)) {
			$lang = $row['lg_iso_2'].($row['lg_country_iso_2']?'_'.$row['lg_country_iso_2']:'');
		}
		
		return $lang ? $lang : $csConvObj->conv_case('utf-8',$langCodeT3,'toUpper');
	}
	
	/*
	 *
	 * Returns the locale to used when sorting labels
	 *
	 * @return	string	locale
	 */
	function getCollateLocale() {
		global $LANG, $TSFE, $TYPO3_DB;
		
		if (is_object($LANG)) {
			$langCodeT3 = $LANG->lang;
		} elseif (is_object($TSFE)) {
			$langCodeT3 = $TSFE->lang;
		} else {
			return 'C';
		}
		
		$res = $TYPO3_DB->exec_SELECTquery(
			'lg_collate_locale',
			'static_languages',
			'lg_typo3='.$TYPO3_DB->fullQuoteStr($langCodeT3,'static_languages')
			);
		while ($row = $TYPO3_DB->sql_fetch_assoc($res)) {
			$locale = $row['lg_collate_locale'];
		}
		return $locale ? $locale : 'C';
	}
	
	/**
	 * Fetches short title from an iso code
	 *
	 * @param	string		table name
	 * @param	string		iso code
	 * @param	string		language code - if not set current default language is used
	 * @param	boolean		local name only - if set local title is returned
	 * @return	string		short title
	 */
	function getTitleFromIsoCode($table, $isoCode, $lang='', $local=FALSE) {
		global $TSFE, $TYPO3_DB;
		
		$title = '';
		$titleFields = tx_staticinfotables_div::getTCAlabelField($table, TRUE, $lang, $local);
		$prefixedTitleFields = array();
		foreach ($titleFields as $titleField) {
			$prefixedTitleFields[] = $table.'.'.$titleField;
		}
		$fields = implode(',', $prefixedTitleFields);
		$whereClause = '';
		if (!is_array($isoCode)) {
			$isoCode = array($isoCode);
		}
		$index = 0;
		foreach ($isoCode as $index => $code) {
			$whereClause .= ($index?' AND ':'').$table.'.'.tx_staticinfotables_div::getIsoCodeField($table, $code, TRUE, $index).'='.$TYPO3_DB->fullQuoteStr($code,$table);
		}
		
		if (is_object($TSFE)) {
			$enableFields = $TSFE->sys_page->enableFields($table);
		} else {
			$enableFields = t3lib_BEfunc::deleteClause($table);
		}
		
		$res = $TYPO3_DB->exec_SELECTquery(
			$fields,
			$table,
			$whereClause.$enableFields
			);
		if ($row = $TYPO3_DB->sql_fetch_assoc($res))	{
			foreach ($titleFields as $titleField) {
				if ($row[$titleField]) return $row[$titleField];
			}
		}
		
		return $title;
	}


	/**
	 * Function to use in own TCA definitions
	 * Adds additional select items
	 *
	 * @param	array		itemsProcFunc data array
	 * @return	void
	 */
	function selectItemsTCA($params) {
		global $TCA;
/*
		$params['items'] = &$items;
		$params['config'] = $config;
		$params['TSconfig'] = $iArray;
		$params['table'] = $table;
		$params['row'] = $row;
		$params['field'] = $field;
*/
		$table = $params['config']['itemsProcFunc_config']['table'];

		if ($table) {
			$indexField = $params['config']['itemsProcFunc_config']['indexField'];
			$indexField = $indexField ? $indexField : 'uid';

			$lang = strtolower(tx_staticinfotables_div::getCurrentLanguage());
			$titleFields = tx_staticinfotables_div::getTCAlabelField($table, TRUE, $lang);
			$prefixedTitleFields = array();
			foreach ($titleFields as $titleField) {
				$prefixedTitleFields[] = $table.'.'.$titleField;
			}
			$fields = $table.'.'.$indexField.','.implode(',', $prefixedTitleFields);

			if ($params['config']['itemsProcFunc_config']['prependHotlist']) {

				$limit = $params['config']['itemsProcFunc_config']['hotlistLimit'];
				$limit = $limit ? $limit : '8';

				$app = $params['config']['itemsProcFunc_config']['hotlistApp'];
				$app = $app ? $app : TYPO3_MODE;

				$res = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
						$fields,
						$table,
						'tx_staticinfotables_hotlist',
						'',	// $foreign_table
						'AND tx_staticinfotables_hotlist.application='.$GLOBALS['TYPO3_DB']->fullQuoteStr($app,'tx_staticinfotables_hotlist'),
						'',
						'tx_staticinfotables_hotlist.sorting DESC',	// $orderBy
						$limit
					);

				$cnt = 0;
				$rows = array();
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
					#$params['items'][] = array($row[$titleField], $row[$indexField], '');
					foreach ($titleFields as $titleField) {
						if ($row[$titleField]) {
							$rows[$row[$indexField]] = $row[$titleField];
							break;
						}
					}
					$cnt++;
				}

				if (!isset($params['config']['itemsProcFunc_config']['hotlistSort']) OR $params['config']['itemsProcFunc_config']['hotlistSort']) {
					asort ($rows);
				}

				foreach ($rows as $index => $title)	{
					$params['items'][] = array($title, $index, '');
					$cnt++;
				}
				if($cnt && !$params['config']['itemsProcFunc_config']['hotlistOnly']) {
					$params['items'][] = array('--------------', '', '');
				}
			}
			
				// Set ORDER BY:
			$orderBy = $titleFields[0];
			
			if(!$params['config']['itemsProcFunc_config']['hotlistOnly']) {
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $table, '1'.t3lib_BEfunc::deleteClause($table), '', $orderBy);
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					foreach ($titleFields as $titleField) {
						if ($row[$titleField]) {
							$params['items'][] = array($row[$titleField], $row[$indexField], '');
							break;
						}
					}
				}
			}
		}
	}

	/**
	 * Updates the hotlist table.
	 * This means that a hotlist entry will be created or the counter of an existing entry will be increased
	 *
	 * @param	string		table name: static_countries, ...
	 * @param	string		value of the following index field
	 * @param	string		the field which holds the value and is an index field: uid (default) or one of the iso code fields which are also unique
	 * @param	string		This indicates a counter group. Default is TYPO3_MOD (BE or FE). If you want a unique hotlist for your application you can provide here a name (e.g. extension key)
	 * @return	void
	 */
	function updateHotlist ($table, $indexValue, $indexField='', $app='') {

		if ($table && $indexValue) {
			$indexField = $indexField ? $indexField : 'uid';
			$app = $app ? $app : TYPO3_MODE;

			if ($indexField=='uid') {
				$uid = $indexValue;

			} else {
					// fetch original record
				$fields = array();
				$fields[$indexField] = $indexField;
				$fields['uid'] = 'uid';

				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(implode(',',$fields), $table, $indexField.'='.$GLOBALS['TYPO3_DB']->fullQuoteStr($indexValue,$table).t3lib_BEfunc::deleteClause($table));
				if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
					$uid = $row['uid'];
				}
			}

			if ($uid) {
					// update record from hotlist table
				$newRow = array('sorting' => 'sorting+1');
//				$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
//						'tx_staticinfotables_hotlist',
//						'uid_local='.$uid.
//							' AND application='.$GLOBALS['TYPO3_DB']->fullQuoteStr($app,'tx_staticinfotables_hotlist').
//							' AND tablenames='.$GLOBALS['TYPO3_DB']->fullQuoteStr($table,'tx_staticinfotables_hotlist').
//							t3lib_BEfunc::deleteClause('tx_staticinfotables_hotlist'),
//						$newRow
//					);

				// the dumb update function does not allow to use sorting+1 - that's why this trick is necessary

				$GLOBALS['TYPO3_DB']->sql_query(str_replace('"sorting+1"', 'sorting+1', $GLOBALS['TYPO3_DB']->UPDATEquery(
						'tx_staticinfotables_hotlist',
						'uid_local='.$uid.
							' AND application='.$GLOBALS['TYPO3_DB']->fullQuoteStr($app,'tx_staticinfotables_hotlist').
							' AND tablenames='.$GLOBALS['TYPO3_DB']->fullQuoteStr($table,'tx_staticinfotables_hotlist').
							t3lib_BEfunc::deleteClause('tx_staticinfotables_hotlist'),
						$newRow)));

				if (!$GLOBALS['TYPO3_DB']->sql_affected_rows())	{
						// insert new hotlist entry
					$row = array(
						'uid_local' => $uid,
						'tablenames' => $table,
						'application' => $app,
						'sorting' => 1,
					);
					$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_staticinfotables_hotlist', $row);
				}
			}
		}
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/static_info_tables/class.tx_staticinfotables_div.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/static_info_tables/class.tx_staticinfotables_div.php']);
}
?>