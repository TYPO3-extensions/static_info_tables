<?php
namespace SJBR\StaticInfoTables\Hook\Backend\Form;
/***************************************************************
*  Copyright notice
*
*  (c) 2013-2014 Stanislas Rolland <typo3(arobas)sjbr.ca>
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

use SJBR\StaticInfoTables\Utility\ModelUtility;
use SJBR\StaticInfoTables\Utility\TcaUtility;
use SJBR\StaticInfoTables\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ElementRenderingHelper {
	/**
	 * Translate selected items before rendering
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

	/**
	 * Add ISO codes to the label of entities
	 */
	public function addIsoCodeToLabel (&$PA, &$fObj) {
		$PA['title'] = $PA['row'][$GLOBALS['TCA'][$PA['table']]['ctrl']['label']];
		if (TYPO3_MODE == 'BE') {
			/** @var $objectManager \TYPO3\CMS\Extbase\Object\ObjectManager */
			$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
			switch ($PA['table']) {
				case 'static_territories':
					$isoCode = $PA['row']['tr_iso_nr'];
					if (!$isoCode) {
						/** @var $territoryRepository SJBR\StaticInfoTables\Domain\Repository\TerritoryRepository */
						$territoryRepository = $objectManager->get('SJBR\\StaticInfoTables\\Domain\\Repository\\TerritoryRepository');
						/** @var $territory SJBR\StaticInfoTables\Domain\Model\Territory */
						$territory = $territoryRepository->findByUid($PA['row']['uid']);
						$isoCode = $territory->getUnCodeNumber();
					}
					if ($isoCode) {
						$PA['title'] = $PA['title'] . ' (' . $isoCode . ')';
					}
					break;
				case 'static_countries':
					$isoCode = $PA['row']['cn_iso_2'];
					if (!$isoCode) {
						/** @var $countryRepository SJBR\StaticInfoTables\Domain\Repository\CountryRepository */
						$countryRepository = $objectManager->get('SJBR\\StaticInfoTables\\Domain\\Repository\\CountryRepository');
						/** @var $country SJBR\StaticInfoTables\Domain\Model\Country */
						$country = $countryRepository->findByUid($PA['row']['uid']);
						$isoCode = $country->getIsoCodeA2();
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
						/** @var $languageRepository SJBR\StaticInfoTables\Domain\Repository\LanguageRepository */
						$languageRepository = $objectManager->get('SJBR\\StaticInfoTables\\Domain\\Repository\\LanguageRepository');
						/** @var $language SJBR\StaticInfoTables\Domain\Model\Language */
						$language = $languageRepository->findByUid($PA['row']['uid']);
						$isoCodes = array($language->getIsoCodeA2());
						if ($language->getCountryIsoCodeA2()) {
							$isoCodes[] = $language->getCountryIsoCodeA2();
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
						/** @var $currencyRepository SJBR\StaticInfoTables\Domain\Repository\CurrencyRepository */
						$currencyRepository = $objectManager->get('SJBR\\StaticInfoTables\\Domain\\Repository\\CurrencyRepository');
						/** @var $currency SJBR\StaticInfoTables\Domain\Model\Currency */
						$currency = $currencyRepository->findByUid($PA['row']['uid']);
						$isoCode = $currency->getIsoCodeA3();
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

	/**
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
		$PA['items'] = $this->replaceSelectorIndexField($PA);
	}

	/**
	 * Translate and sort the countries selector using the current locale
	 */
	public function translateCountriesSelector($PA, $fObj) {
		$PA['items'] = $this->translateSelectorItems($PA['items'], 'static_countries');
		$PA['items'] = $this->replaceSelectorIndexField($PA);
	}

	/**
	 * Translate and sort the country zones selector using the current locale
	 */
	public function translateCountryZonesSelector($PA, $fObj) {
		$PA['items'] = $this->translateSelectorItems($PA['items'], 'static_country_zones');
		$PA['items'] = $this->replaceSelectorIndexField($PA);
	}

	/**
	 * Translate and sort the currencies selector using the current locale
	 */
	public function translateCurrenciesSelector($PA, $fObj) {
		$PA['items'] = $this->translateSelectorItems($PA['items'], 'static_currencies');
		$PA['items'] = $this->replaceSelectorIndexField($PA);
	}

	/**
	 * Translate and sort the languages selector using the current locale
	 */
	public function translateLanguagesSelector($PA, $fObj) {
		$PA['items'] = $this->translateSelectorItems($PA['items'], 'static_languages');
		$PA['items'] = $this->replaceSelectorIndexField($PA);
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
			$locale = LocalizationUtility::setCollatingLocale();
			if ($locale !== FALSE) {
				uasort($translatedItems, array($this, 'strcollOnLabels'));
			}
			setlocale(LC_COLLATE, $currentLocale);
		}
		$items = $translatedItems;
		return $items;
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

	/**
	 * Replace the selector's uid index with configured indexField
	 *
	 * @param array	 $PA: TCA select field parameters array
	 * @return array The new $items array
	 */
	protected function replaceSelectorIndexField($PA) {
		$items = $PA['items'];
		$indexFields = GeneralUtility::trimExplode(',', $PA['config']['itemsProcFunc_config']['indexField']);
		if (!empty($indexFields)) {
			$rows = array();
			// Collect items uid's
			$uids = array();
			foreach ($items as $key => $item) {
				if ($items[$key][1]) {
					$uids[] = $item[1];
				}
			}
			$uidList = implode(',', $uids);
			if (!empty($uidList)) {
				/** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
				$objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
				switch ($PA['config']['foreign_table']) {
					case 'static_territories':
						/** @var $territoryRepository SJBR\StaticInfoTables\Domain\Repository\TerritoryRepository */
						$territoryRepository = $objectManager->get('SJBR\\StaticInfoTables\\Domain\\Repository\\TerritoryRepository');
						$objects = $territoryRepository->findAllByUidInList($uidList)->toArray();
						$columnsMapping = ModelUtility::getModelMapping('SJBR\\StaticInfoTables\\Domain\\Model\\Territory', ModelUtility::MAPPING_COLUMNS);
						break;
					case 'static_countries':
						/** @var $countryRepository SJBR\StaticInfoTables\Domain\Repository\CountryRepository */
						$countryRepository = $objectManager->get('SJBR\\StaticInfoTables\\Domain\\Repository\\CountryRepository');
						$objects = $countryRepository->findAllByUidInList($uidList)->toArray();
						$columnsMapping = ModelUtility::getModelMapping('SJBR\\StaticInfoTables\\Domain\\Model\\Country', ModelUtility::MAPPING_COLUMNS);
						break;
					case 'static_country_zones':
						/** @var $countryZoneRepository SJBR\StaticInfoTables\Domain\Repository\CountryZoneRepository */
						$countryZoneRepository = $objectManager->get('SJBR\\StaticInfoTables\\Domain\\Repository\\CountryZoneRepository');
						$objects = $countryZoneRepository->findAllByUidInList($uidList)->toArray();
						$columnsMapping = ModelUtility::getModelMapping('SJBR\\StaticInfoTables\\Domain\\Model\\CountryZone', ModelUtility::MAPPING_COLUMNS);
						break;					
					case 'static_languages':
						/** @var $languageRepository SJBR\StaticInfoTables\Domain\Repository\LanguageRepository */
						$languageRepository = $objectManager->get('SJBR\\StaticInfoTables\\Domain\\Repository\\LanguageRepository');
						$objects = $languageRepository->findAllByUidInList($uidList)->toArray();
						$columnsMapping = ModelUtility::getModelMapping('SJBR\\StaticInfoTables\\Domain\\Model\\Language', ModelUtility::MAPPING_COLUMNS);
						break;
					case 'static_currencies':
						/** @var $currencyRepository SJBR\StaticInfoTables\Domain\Repository\CurrencyRepository */
						$currencyRepository = $objectManager->get('SJBR\\StaticInfoTables\\Domain\\Repository\\CurrencyRepository');
						$objects = $currencyRepository->findAllByUidInList($uidList)->toArray();
						$columnsMapping = ModelUtility::getModelMapping('SJBR\\StaticInfoTables\\Domain\\Model\\Currency', ModelUtility::MAPPING_COLUMNS);
						break;
					default:
						break;
				}
				if (!empty($objects)) {
					// Map table column to object property
					$indexProperties = array();
					foreach ($indexFields as $indexField) {
						if ($columnsMapping[$indexField]['mapOnProperty']) {
							$indexProperties[] = $columnsMapping[$indexField]['mapOnProperty'];
						} else {
							$indexProperties[] = GeneralUtility::underscoredToUpperCamelCase($indexField);
						}
					}
					// Index rows by uid
					$uidIndexedRows = array();
					foreach ($objects as $object) {
						$uidIndexedObjects[$object->getUid()] = $object;
					}
					// Replace the items index field
					foreach ($items as $key => $item) {
						if ($items[$key][1]) {
							$object = $uidIndexedObjects[$items[$key][1]];
							$items[$key][1] = $object->_getProperty($indexProperties[0]);
							if ($indexFields[1] && $object->_getProperty($indexProperties[1])) {
								$items[$key][1] .=  '_' . $object->_getProperty($indexProperties[1]);
							}
						}
					}						
				}
			}
		}
		return $items;
	}
}
