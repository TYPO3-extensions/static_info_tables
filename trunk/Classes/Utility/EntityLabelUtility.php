<?php
namespace SJBR\StaticInfoTables\Utility;
/***************************************************************
*  Copyright notice
*
*  (c) 2004-2010 René Fritz (r.fritz@colorcube.de)
*  (c) 2013 Stanislas Rolland <typo3(arobas)sjbr.ca>
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
 * Functions to get appropriate labels for entities
 */
class EntityLabelUtility {

	/**
	 * Replaces any dynamic markers in a SQL statement.
	 *
	 * @param	string		The SQL statement with dynamic markers.
	 * @param	string		Name of the table.
	 * @param	array		row from table.
	 * @return	string		SQL query with dynamic markers subsituted.
	 */
	protected function replaceMarkersInSQL ($sql, $table, $row) {

		$TSconfig = \TYPO3\CMS\Backend\Utility\BackendUtility::getTCEFORM_TSconfig($table, $row);

		/* Replace references to specific fields with value of that field */
		if (strstr($sql,'###REC_FIELD_'))	{
			$sql_parts = explode('###REC_FIELD_',$sql);
			while(list($kk,$vv)=each($sql_parts))	{
				if ($kk)	{
					$sql_subpart = explode('###',$vv,2);
					$sql_parts[$kk]=$TSconfig['_THIS_ROW'][$sql_subpart[0]].$sql_subpart[1];
				}
			}
			$sql = implode('',$sql_parts);
		}

		/* Replace markers with TSConfig values */
		$sql = str_replace('###THIS_UID###',intval($TSconfig['_THIS_UID']),$sql);
		$sql = str_replace('###THIS_CID###',intval($TSconfig['_THIS_CID']),$sql);
		$sql = str_replace('###SITEROOT###',intval($TSconfig['_SITEROOT']),$sql);
		$sql = str_replace('###PAGE_TSCONFIG_ID###',intval($TSconfig[$field]['PAGE_TSCONFIG_ID']),$sql);
		$sql = str_replace('###PAGE_TSCONFIG_IDLIST###',$GLOBALS['TYPO3_DB']->cleanIntList($TSconfig[$field]['PAGE_TSCONFIG_IDLIST']),$sql);
		$sql = str_replace('###PAGE_TSCONFIG_STR###',$GLOBALS['TYPO3_DB']->quoteStr($TSconfig[$field]['PAGE_TSCONFIG_STR'], $table),$sql);

		return $sql;
	}


	/**
	 * Function to use in own TCA definitions
	 * Adds additional select items
	 *
	 * 			items		reference to the array of items (label,value,icon)
	 * 			config		The config array for the field.
	 * 			TSconfig	The "itemsProcFunc." from fieldTSconfig of the field.
	 * 			table		Table name
	 * 			row		Record row
	 * 			field		Field name
	 *
	 * @param	array		itemsProcFunc data array:
	 * @return	void		The $items array may have been modified
	 */
	public function selectItemsTCA ($params) {
		global $TCA;

		$where = '';
		$config = &$params['config'];
		$table = $config['itemsProcFunc_config']['table'];
		$tcaWhere = $config['itemsProcFunc_config']['where'];
		if ($tcaWhere)	{
			$where = self::replaceMarkersInSQL($tcaWhere, $params['table'], $params['row']);
		}

		if ($table) {
			$indexField = $config['itemsProcFunc_config']['indexField'];
			$indexField = $indexField ? $indexField : 'uid';

			$lang = \SJBR\StaticInfoTables\Utility\LocalizationUtility::getCurrentLanguage();
			$lang = strtolower(\SJBR\StaticInfoTables\Utility\LocalizationUtility::getIsoLanguageKey($lang));
			$titleFields = \SJBR\StaticInfoTables\Utility\LocalizationUtility::getLabelFields($table, $lang);
			$prefixedTitleFields = array();
			foreach ($titleFields as $titleField) {
				$prefixedTitleFields[] = $table.'.'.$titleField;
			}
			$fields = $table.'.'.$indexField.','.implode(',', $prefixedTitleFields);

			if ($config['itemsProcFunc_config']['prependHotlist']) {

				$limit = $config['itemsProcFunc_config']['hotlistLimit'];
				$limit = $limit ? $limit : '8';
				$app = $config['itemsProcFunc_config']['hotlistApp'];
				$app = $app ? $app : TYPO3_MODE;

				$res = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
						$fields,
						$table,
						'tx_staticinfotables_hotlist',
						'',	// $foreign_table
						'AND tx_staticinfotables_hotlist.tablenames='.$GLOBALS['TYPO3_DB']->fullQuoteStr($table,'tx_staticinfotables_hotlist').' AND tx_staticinfotables_hotlist.application='.$GLOBALS['TYPO3_DB']->fullQuoteStr($app,'tx_staticinfotables_hotlist'),
						'',
						'tx_staticinfotables_hotlist.sorting DESC',	// $orderBy
						$limit
					);

				$cnt = 0;
				$rows = array();
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{

					foreach ($titleFields as $titleField) {
						if ($row[$titleField]) {
							$rows[$row[$indexField]] = $row[$titleField];
							break;
						}
					}
					$cnt++;
				}
				$GLOBALS['TYPO3_DB']->sql_free_result($res);

				if (!isset($config['itemsProcFunc_config']['hotlistSort']) || $config['itemsProcFunc_config']['hotlistSort']) {
					asort ($rows);
				}

				foreach ($rows as $index => $title)	{
					$params['items'][] = array($title, $index, '');
					$cnt++;
				}
				if($cnt && !$config['itemsProcFunc_config']['hotlistOnly']) {
					$params['items'][] = array('--------------', '', '');
				}
			}

				// Set ORDER BY:
			$orderBy = $titleFields[0];

			if(!$config['itemsProcFunc_config']['hotlistOnly']) {
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $table, '1=1'.$where . \SJBR\StaticInfoTables\Utility\TcaUtility::getEnableFields($table), '', $orderBy);
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					foreach ($titleFields as $titleField) {
						if ($row[$titleField]) {
							$params['items'][] = array($row[$titleField], $row[$indexField], '');
							break;
						}
					}
				}
				$GLOBALS['TYPO3_DB']->sql_free_result($res);
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
	public function updateHotlist ($table, $indexValue, $indexField='', $app='') {

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

				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(implode(',',$fields), $table, $indexField.'='.$GLOBALS['TYPO3_DB']->fullQuoteStr($indexValue,$table) . \SJBR\StaticInfoTables\Utility\TcaUtility::getEnableFields($table));
				if ($res !== FALSE)	{
					if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
						$uid = $row['uid'];
					}
					$GLOBALS['TYPO3_DB']->sql_free_result($res);
				}
			}

			if ($uid) {
					// update record from hotlist table
				$newRow = array('sorting' => 'sorting+1');
				// the dumb update function does not allow to use sorting+1 - that's why this trick is necessary

				$GLOBALS['TYPO3_DB']->sql_query(str_replace('"sorting+1"', 'sorting+1', $GLOBALS['TYPO3_DB']->UPDATEquery(
						'tx_staticinfotables_hotlist',
						'uid_local='.$uid.
							' AND application='.$GLOBALS['TYPO3_DB']->fullQuoteStr($app,'tx_staticinfotables_hotlist').
							' AND tablenames='.$GLOBALS['TYPO3_DB']->fullQuoteStr($table,'tx_staticinfotables_hotlist').
							\SJBR\StaticInfoTables\Utility\TcaUtility::getEnableFields('tx_staticinfotables_hotlist'),
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
?>