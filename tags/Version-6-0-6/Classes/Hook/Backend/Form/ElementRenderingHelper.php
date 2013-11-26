<?php
namespace SJBR\StaticInfoTables\Hook\Backend\Form;
use SJBR\StaticInfoTables\Utility\TcaUtility;
use SJBR\StaticInfoTables\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
/***************************************************************
*  Copyright notice
*
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
 * Custom rendering of some backend forms elements
 *
 */
class ElementRenderingHelper {
	/**
	 * Translate selcted items before rendering
	 */
	public function getSingleField_beforeRender($table, $field, $row, &$PA) {
		if ($PA['fieldConf']['config']['form_type'] == 'select' && $PA['fieldConf']['config']['maxitems'] > 1) {
			switch ($PA['fieldConf']['config']['foreign_table']) {
				case 'static_countries':
				case 'static_currencies':
				case 'static_languages':
				case 'static_territories':
					$PA['itemFormElValue'] = $this->translateSelectedItems($PA['itemFormElValue'], $PA['fieldConf']['config']['foreign_table']);
					break;
			}
		}
	}

	/*
	 * Add ISO codes to the label of entities
	 */
	public function addIsoCodeToLabel (&$PA, &$fObj) {
		$PA['title'] = $PA['row'][$GLOBALS['TCA'][$PA['table']]['ctrl']['label']];
		if (TYPO3_MODE == 'BE') {
			switch ($PA['table']) {
				case 'static_territories':
					$isoCode = $PA['row']['tr_iso_nr'];
					if (!$isoCode) {
						$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
							'uid,tr_iso_nr',
							$PA['table'],
							'uid = ' . intval($PA['row']['uid']) . TcaUtility::getEnableFields($PA['table'])
						);
						$isoCode = $rows[0]['tr_iso_nr'];
					}
					if ($isoCode) {
						$PA['title'] = $PA['title'] . ' (' . $isoCode . ')';
					}
					break;
				case 'static_countries':
					$isoCode = $PA['row']['cn_iso_2'];
					if (!$isoCode) {
						$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
							'uid,cn_iso_2',
							$PA['table'],
							'uid = ' . intval($PA['row']['uid']) . TcaUtility::getEnableFields($PA['table'])
						);
						$isoCode = $rows[0]['cn_iso_2'];
					}
					if ($isoCode) {
						$PA['title'] = $PA['title'] . ' (' . $isoCode . ')';
					}
					break;
				case 'static_languages':
					$isoCodes = array($PA['row']['lg_iso_2']);
					if ($PA['row']['lg_country_iso_2']) {
						$isoCodes[] = $PA['row']['lg_country_iso_2'];
					}
					$isoCode = implode('_', $isoCodes);
					if (!$isoCode || !$PA['row']['lg_country_iso_2']) {
						$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
							'uid,lg_iso_2,lg_country_iso_2',
							$PA['table'],
							'uid = ' . intval($PA['row']['uid']) . TcaUtility::getEnableFields($PA['table'])
						);
						$isoCodes = array($rows[0]['lg_iso_2']);
						if ($rows[0]['lg_country_iso_2']) {
							$isoCodes[] = $rows[0]['lg_country_iso_2'];
						}
						$isoCode = implode('_', $isoCodes);	
					}
					if ($isoCode) {
						$PA['title'] = $PA['title'] . ' (' . $isoCode . ')';
					}
					break;
				case 'static_currencies':
					$isoCode = $PA['row']['cu_iso_3'];
					if (!$isoCode) {
						$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
							'uid,cu_iso_3',
							$PA['table'],
							'uid = ' . intval($PA['row']['uid']) . TcaUtility::getEnableFields($PA['table'])
						);
						$isoCode = $rows[0]['cu_iso_3'];
					}
					if ($isoCode) {
						$PA['title'] = $PA['title'] . ' (' . $isoCode . ')';
					}
					break;
				default:
					break;
			}
		}
	}

	/*
	 * Translate and sort the territories selector using the current locale
	 */
	public function translateTerritoriesSelector($PA, $fObj) {
		switch ($PA['table']) {
			case 'static_territories':
				// Avoid circular relation
				$row = $PA['row'];
				foreach ($PA['items'] as $index => $item) {
					if ($item[1] == $row['uid']) {
						unset($PA['items'][$index]);
					}
				}
				break;
		}
		$PA['items'] = $this->translateSelectorItems($PA['items'], 'static_territories');
	}

	/*
	 * Translate and sort the countries selector using the current locale
	 */
	public function translateCountriesSelector($PA, $fObj) {
		$PA['items'] = $this->translateSelectorItems($PA['items'], 'static_countries');
	}

	/*
	 * Translate and sort the currencies selector using the current locale
	 */
	public function translateCurrenciesSelector($PA, $fObj) {
		$PA['items'] = $this->translateSelectorItems($PA['items'], 'static_currencies');
	}

	/**
	 * Translate and sort the languages selector using the current locale
	 */
	public function translateLanguagesSelector($PA, $fObj) {
		$PA['items'] = $this->translateSelectorItems($PA['items'], 'static_languages');
	}

	/**
	 * Translate selector items array
	 *
	 * @param array $items: array of value/label pairs
	 * @param string $tableName: name of static info tables
	 * @return array array of value/translated label pairs
	 */
	protected function translateSelectorItems($items, $tableName) {
		$translatedItems = $items;
		if (isset($translatedItems) && is_array($translatedItems)) {
			foreach ($translatedItems as $key => $item) {
				if ($translatedItems[$key][1]) {
					//Get isocode if present
					$code = strstr($item[0], '(');
					$code2 = strstr(substr($code, 1), '(');
					$code = $code2 ? $code2 : $code;
					// Translate
					$translatedItems[$key][0] = LocalizationUtility::translate(array('uid' => $item[1]), $tableName);
					// Re-append isocode, if present
					$translatedItems[$key][0] = $translatedItems[$key][0] . ($code ? ' ' . $code : '');
				}
			}
			$currentLocale = setlocale(LC_COLLATE, '0');
			$locale = \SJBR\StaticInfoTables\Utility\LocalizationUtility::setCollatingLocale();
			if ($locale !== FALSE) {
				uasort($translatedItems, array($this, 'strcollOnLabels'));
			}
			setlocale(LC_COLLATE, $currentLocale);
		}
		return $translatedItems;
	}

	/**
	 * Translate selector items array
	 *
	 * @param string $itemFormElValue: value of the form element
	 * @param string $tableName: name of static info tables
	 * @return string value of the form element with translated labels
	 */
	protected function translateSelectedItems($itemFormElValue, $tableName) {
		// Get the array with selected items:
		$itemArray = GeneralUtility::trimExplode(',', $itemFormElValue, 1);
		// Perform modification of the selected items array:
		foreach ($itemArray as $tk => $tv) {
			$tvP = explode('|', $tv, 2);
			if ($tvP[0]) {
				//Get isocode if present
				$code = strstr($tvP[1], '%28');
				$code2 = strstr(substr($code, 1), '%28');
				$code = $code2 ? $code2 : $code;
				// Translate
				$tvP[1] = LocalizationUtility::translate(array('uid' => $tvP[0]), $tableName);
				// Re-append isocode, if present
				$tvP[1] = $tvP[1] . ($code ? '%20' . $code : '');
			}
			$itemArray[$tk] = implode('|', $tvP);
		}
		return implode(',', $itemArray);
	}

	/**
	 * Using strcoll comparison on labels
	 *
	 * @return integer see strcoll
	 */
	protected function strcollOnLabels($itemA, $itemB) {
		return strcoll($itemA[0], $itemB[0]);
	}
}
?>
