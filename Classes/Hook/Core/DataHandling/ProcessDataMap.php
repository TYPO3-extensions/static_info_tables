<?php
namespace SJBR\StaticInfoTables\Hook\Core\DataHandling;

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

use SJBR\StaticInfoTables\Domain\Repository\CountryRepository;
use SJBR\StaticInfoTables\Domain\Repository\CurrencyRepository;
use SJBR\StaticInfoTables\Domain\Repository\TerritoryRepository;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Hook on Core/DataHandling/DataHandler to manage redundancy of ISO codes in static info tables
 */
class ProcessDataMap
{
    /**
     * Post-process redundant ISO codes fields
     *
     * @param object $fobj TCEmain object reference
     * @param mixed $status
     * @param mixed $table
     * @param mixed $id
     *
     * @return void
     */
    public function processDatamap_postProcessFieldArray($status, $table, $id, &$incomingFieldArray, &$fObj)
    {
        switch ($table) {
            case 'static_territories':
                $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
                //Post-process containing territory ISO numeric code
                if ($incomingFieldArray['tr_parent_territory_uid']) {
                    $territoryRepository = $objectManager->get(TerritoryRepository::class);
                    $territory = $territoryRepository->findOneByUid((int)$incomingFieldArray['tr_parent_territory_uid']);
                    if (is_object($territory)) {
                        $incomingFieldArray['tr_parent_iso_nr'] = $territory->getUnCodeNumber();
                    }
                } elseif (isset($incomingFieldArray['tr_parent_territory_uid'])) {
                    $incomingFieldArray['tr_parent_iso_nr'] = 0;
                }
                break;
            case 'static_countries':
                $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
                //Post-process containing territory ISO numeric code
                if ($incomingFieldArray['cn_parent_territory_uid']) {
                    $territoryRepository = $objectManager->get(TerritoryRepository::class);
                    $territory = $territoryRepository->findOneByUid((int)$incomingFieldArray['cn_parent_territory_uid']);
                    if (is_object($territory)) {
                        $incomingFieldArray['cn_parent_tr_iso_nr'] = $territory->getUnCodeNumber();
                    }
                } elseif (isset($incomingFieldArray['cn_parent_territory_uid'])) {
                    $incomingFieldArray['cn_parent_tr_iso_nr'] = 0;
                }
                //Post-process currency ISO numeric and A3 codes
                if ($incomingFieldArray['cn_currency_uid']) {
                    $currencyRepository = $objectManager->get(CurrencyRepository::class);
                    $currency = $currencyRepository->findOneByUid((int)$incomingFieldArray['cn_currency_uid']);
                    if (is_object($currency)) {
                        $incomingFieldArray['cn_currency_iso_nr'] = $currency->getIsoCodeNumber();
                        $incomingFieldArray['cn_currency_iso_3'] = $currency->getIsoCodeA3();
                    }
                } elseif (isset($incomingFieldArray['cn_currency_uid'])) {
                    $incomingFieldArray['cn_currency_iso_nr'] = 0;
                    $incomingFieldArray['cn_currency_iso_3'] = '';
                }
                break;
        }
    }

    /**
     * Post-process redundant ISO codes fields of IRRE child
     *
     * @param object $fobj TCEmain object reference
     * @param mixed $status
     * @param mixed $table
     * @param mixed $id
     *
     * @return void
     */
    public function processDatamap_afterDatabaseOperations($status, $table, $id, &$fieldArray, &$fObj)
    {
        switch ($table) {
            case 'static_countries':
                //Post-process country ISO numeric, A2 and A3 codes on country zones
                // Get the country record uid
                if ($status === 'new') {
                    $id = $fObj->substNEWwithIDs[$id];
                }
                // Get the country
                $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
                $countryRepository = $objectManager->get(CountryRepository::class);
                $country = $countryRepository->findOneByUid((int)$id);
                // Get the country zones
                $countryZones = $country->getCountryZones()->toArray();
                if (count($countryZones)) {
                    $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('static_country_zones');
                    foreach ($countryZones as $countryZone) {
                        $connection->update(
                            'static_country_zones',
                            [
                                'zn_country_iso_nr' => (int)$country->getIsoCodeNumber(),
                                'zn_country_iso_2' => $country->getIsoCodeA2(),
                                'zn_country_iso_3' => $country->getIsoCodeA3(),
                            ],
                            ['uid' => (int)$countryZone->getUid()]
                        );
                    }
                }
                break;
        }
    }
}
