<?php
namespace SJBR\StaticInfoTables\Hook\Backend\Form\FormDataProvider;

/*
 *  Copyright notice
 *
 *  (c) 2013-2016 Stanislas Rolland <typo3(arobas)sjbr.ca>
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

use SJBR\StaticInfoTables\Domain\Model\Country;
use SJBR\StaticInfoTables\Domain\Model\Currency;
use SJBR\StaticInfoTables\Domain\Model\Language;
use SJBR\StaticInfoTables\Domain\Model\Territory;
use SJBR\StaticInfoTables\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Processor for TCA select items
 */
class TcaLabelProcessor
{
    /**
     * Add ISO codes to the label of entities
     *
     * @param array $PA: parameters: items, config, TSconfig, table, row, field
     * @param DataPreprocessor $fObj
     *
     * @return void
     */
    public function addIsoCodeToLabel(&$PA)
    {
        if (\TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(\TYPO3\CMS\Core\Utility\VersionNumberUtility::getNumericTypo3Version()) < 8000000) {
            $PA['title'] = $PA['row'][$GLOBALS['TCA'][$PA['table']]['ctrl']['label']];
        } else {
            $PA['title'] = LocalizationUtility::translate(['uid' => $PA['row']['uid']], $PA['table']);
        }
        if (TYPO3_MODE == 'BE') {
            /** @var $objectManager \TYPO3\CMS\Extbase\Object\ObjectManager */
            $objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
            switch ($PA['table']) {
                case 'static_territories':
                    $isoCode = $PA['row']['tr_iso_nr'];
                    if (!$isoCode) {
                        /** @var $territoryRepository SJBR\StaticInfoTables\Domain\Repository\TerritoryRepository */
                        $territoryRepository = $objectManager->get('SJBR\\StaticInfoTables\\Domain\\Repository\\TerritoryRepository');
                        /** @var $territory SJBR\StaticInfoTables\Domain\Model\Territory */
                        $territory = $territoryRepository->findByUid($PA['row']['uid']);
                        if ($territory instanceof Territory) {
                            $isoCode = $territory->getUnCodeNumber();
                        }
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
                        if ($country instanceof Country) {
                            $isoCode = $country->getIsoCodeA2();
                        }
                    }
                    if ($isoCode) {
                        $PA['title'] = $PA['title'] . ' (' . $isoCode . ')';
                    }
                    break;
                case 'static_languages':
                    $isoCodes = [$PA['row']['lg_iso_2']];
                    if ($PA['row']['lg_country_iso_2']) {
                        $isoCodes[] = $PA['row']['lg_country_iso_2'];
                    }
                    $isoCode = implode('_', $isoCodes);
                    if (!$isoCode || !$PA['row']['lg_country_iso_2']) {
                        /** @var $languageRepository SJBR\StaticInfoTables\Domain\Repository\LanguageRepository */
                        $languageRepository = $objectManager->get('SJBR\\StaticInfoTables\\Domain\\Repository\\LanguageRepository');
                        /** @var $language SJBR\StaticInfoTables\Domain\Model\Language */
                        $language = $languageRepository->findByUid($PA['row']['uid']);
                        if ($language instanceof Language) {
                            $isoCodes = [$language->getIsoCodeA2()];
                            if ($language->getCountryIsoCodeA2()) {
                                $isoCodes[] = $language->getCountryIsoCodeA2();
                            }
                            $isoCode = implode('_', $isoCodes);
                        }
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
                        if ($currency instanceof Currency) {
                            $isoCode = $currency->getIsoCodeA3();
                        }
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
}
