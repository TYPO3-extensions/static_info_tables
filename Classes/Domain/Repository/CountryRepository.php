<?php
namespace SJBR\StaticInfoTables\Domain\Repository;

/*
 *  Copyright notice
 *
 *  (c) 2011-2012 Armin Rädiger Vieweg <info@professorweb.de>
 *  (c) 2013-2016 Stanislas Rolland <typo3(arobas)sjbr.ca>
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
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Repository for \SJBR\StaticInfoTables\Domain\Model\Country
 */
class CountryRepository extends AbstractEntityRepository
{
    /**
     * ISO keys for this static table
     *
     * @var array
     */
    protected $isoKeys = ['cn_iso_2'];

    /**
     * @var \SJBR\StaticInfoTables\Domain\Repository\TerritoryRepository
     */
    protected $territoryRepository;

    /**
     * Dependency injection of the Territory Repository
     *
     * @param \SJBR\StaticInfoTables\Domain\Repository\TerritoryRepository $territoryRepository
     *
     * @return void
     */
    public function injectTerritoryRepository(\SJBR\StaticInfoTables\Domain\Repository\TerritoryRepository $territoryRepository)
    {
        $this->territoryRepository = $territoryRepository;
    }

    /**
     * Finds countries by territory
     *
     * @param \SJBR\StaticInfoTables\Domain\Model\Territory $territory
     *
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array
     */
    public function findByTerritory(\SJBR\StaticInfoTables\Domain\Model\Territory $territory)
    {
        $unCodeNumbers = [$territory->getUnCodeNumber()];
        // Get UN code numbers of subterritories (recursively)
        $subterritories = $this->territoryRepository->findWithinTerritory($territory);
        foreach ($subterritories as $subterritory) {
            $unCodeNumbers[] = $subterritory->getUnCodeNumber();
        }
        $query = $this->createQuery();
        $query->matching(
            $query->in('parentTerritoryUnCodeNumber', $unCodeNumbers)
        );
        return $query->execute();
    }

    /**
     * Finds countries by territory ordered by localized name
     *
     * @param \SJBR\StaticInfoTables\Domain\Model\Territory $territory
     *
     * @return array Countries of the territory sorted by localized name
     */
    public function findByTerritoryOrderedByLocalizedName(\SJBR\StaticInfoTables\Domain\Model\Territory $territory)
    {
        $entities = $this->findByTerritory($territory);
        return $this->localizedSort($entities);
    }

    /**
     * Finds a set of allowed countries
     *
     * @param string $allowedCountries: list of alpha-3 country codes
     *
     * @return array the selected countries
     */
    public function findAllowedByIsoCodeA3($allowedCountries = '')
    {
        $query = $this->createQuery();
        $countries = GeneralUtility::trimExplode(',', $allowedCountries, true);
        $query->matching(
            $query->in('isoCodeA3', $countries)
        );
        $entities = $query->execute();
        $orderedCountries = [];
        foreach ($countries as $isoCodeA3) {
            foreach ($entities as $entity) {
                if ($entity->getIsoCodeA3() === $isoCodeA3) {
                    $orderedCountries[] = $entity;
                    break;
                }
            }
        }
        return $orderedCountries;
    }
}
