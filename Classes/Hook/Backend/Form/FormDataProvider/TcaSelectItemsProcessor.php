<?php
namespace SJBR\StaticInfoTables\Hook\Backend\Form\FormDataProvider;

/***************************************************************
*  Copyright notice
*
*  (c) 2013-2018 Stanislas Rolland <typo3(arobas)sjbr.ca>
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
 * Processor for TCA select items
 */
use SJBR\StaticInfoTables\Utility\LocalizationUtility;
use SJBR\StaticInfoTables\Utility\ModelUtility;
use TYPO3\CMS\Backend\Form\FormDataProvider\TcaSelectItems;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TcaSelectItemsProcessor
{
    /**
     * Translate and sort the territories selector using the current locale
     *
     * @param array $PA: parameters: items, config, TSconfig, table, row, field
     * @param DataPreprocessor $fObj
     *
     * @return void
     */
    public function translateTerritoriesSelector($PA, TcaSelectItems $fObj)
    {
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
     *
     * @param array $PA: parameters: items, config, TSconfig, table, row, field
     * @param DataPreprocessor $fObj
     *
     * @return void
     */
    public function translateCountriesSelector($PA, TcaSelectItems $fObj)
    {
        $PA['items'] = $this->translateSelectorItems($PA['items'], 'static_countries');
        $PA['items'] = $this->replaceSelectorIndexField($PA);
    }

    /**
     * Translate and sort the country zones selector using the current locale
     *
     * @param array $PA: parameters: items, config, TSconfig, table, row, field
     * @param DataPreprocessor $fObj
     *
     * @return void
     */
    public function translateCountryZonesSelector($PA, TcaSelectItems $fObj)
    {
        $PA['items'] = $this->translateSelectorItems($PA['items'], 'static_country_zones');
        $PA['items'] = $this->replaceSelectorIndexField($PA);
    }

    /**
     * Translate and sort the currencies selector using the current locale
     *
     * @param array $PA: parameters: items, config, TSconfig, table, row, field
     * @param DataPreprocessor $fObj
     *
     * @return void
     */
    public function translateCurrenciesSelector($PA, TcaSelectItems $fObj)
    {
        $PA['items'] = $this->translateSelectorItems($PA['items'], 'static_currencies');
        $PA['items'] = $this->replaceSelectorIndexField($PA);
    }

    /**
     * Translate and sort the languages selector using the current locale
     *
     * @param array $PA: parameters: items, config, TSconfig, table, row, field
     * @param DataPreprocessor $fObj
     *
     * @return void
     */
    public function translateLanguagesSelector($PA, TcaSelectItems $fObj)
    {
        $PA['items'] = $this->translateSelectorItems($PA['items'], 'static_languages');
        $PA['items'] = $this->replaceSelectorIndexField($PA);
    }

    /**
     * Translate selector items array
     *
     * @param array $items: array of value/label pairs
     * @param string $tableName: name of static info tables
     *
     * @return array array of value/translated label pairs
     */
    protected function translateSelectorItems($items, $tableName)
    {
        $translatedItems = $items;
        if (isset($translatedItems) && is_array($translatedItems)) {
            foreach ($translatedItems as $key => $item) {
                if ($translatedItems[$key][1]) {
                    //Get isocode if present
                    $code = strstr($item[0], '(');
                    $code2 = strstr(substr($code, 1), '(');
                    $code = $code2 ? $code2 : $code;
                    // Translate
                    $translatedItems[$key][0] = LocalizationUtility::translate(['uid' => $item[1]], $tableName);
                    // Re-append isocode, if present
                    $translatedItems[$key][0] = $translatedItems[$key][0] . ($code ? ' ' . $code : '');
                }
            }
            $currentLocale = setlocale(LC_COLLATE, '0');
            $locale = LocalizationUtility::setCollatingLocale();
            if ($locale !== false) {
                uasort($translatedItems, [$this, 'strcollOnLabels']);
            }
            setlocale(LC_COLLATE, $currentLocale);
        }
        $items = $translatedItems;
        return $items;
    }

    /**
     * Using strcoll comparison on labels
     *
     * @return int see strcoll
     *
     * @param mixed $itemA
     * @param mixed $itemB
     */
    protected function strcollOnLabels($itemA, $itemB)
    {
        return strcoll($itemA[0], $itemB[0]);
    }

    /**
     * Replace the selector's uid index with configured indexField
     *
     * @param array	 $PA: TCA select field parameters array
     *
     * @return array The new $items array
     */
    protected function replaceSelectorIndexField($PA)
    {
        $items = $PA['items'];
        $indexFields = GeneralUtility::trimExplode(',', $PA['config']['itemsProcFunc_config']['indexField'], true);
        if (!empty($indexFields)) {
            $rows = [];
            // Collect items uid's
            $uids = [];
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
                    $indexProperties = [];
                    foreach ($indexFields as $indexField) {
                        if ($columnsMapping[$indexField]['mapOnProperty']) {
                            $indexProperties[] = $columnsMapping[$indexField]['mapOnProperty'];
                        } else {
                            $indexProperties[] = GeneralUtility::underscoredToLowerCamelCase($indexField);
                        }
                    }
                    // Index rows by uid
                    $uidIndexedRows = [];
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
