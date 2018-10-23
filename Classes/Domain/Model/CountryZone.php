<?php
namespace SJBR\StaticInfoTables\Domain\Model;

/***************************************************************
*  Copyright notice
*
*  (c) 2011-2012 Armin Rüdiger Vieweg <info@professorweb.de>
*  (c) 2013-2014 Stanislas Rolland <typo3(arobas)sjbr.ca>
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
***************************************************************/
/**
 * The Country Zone model
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
use SJBR\StaticInfoTables\Utility\ModelUtility;

class CountryZone extends AbstractEntity
{
    /**
     * Country code as two digit string (i.e. AT)
     * ISO 3166-1 A2 Country code
     *
     * @var string
     */
    protected $countryIsoCodeA2 = '';

    /**
     * Country code as three digit string (i.e. AUT)
     * ISO 3166-1 A3 Country code
     *
     * @var string
     */
    protected $countryIsoCodeA3 = '';

    /**
     * Country code as number (i.e. 40)
     * ISO 3166-1 Nr Country code
     *
     * @var int
     */
    protected $countryIsoCodeNumber = 0;

    /**
     * Deletion status of the object
     *
     * @var bool
     */
    protected $deleted = false;

    /**
     * Country zone code as string
     * ISO 3166-2 Country Zone code
     *
     * @var string
     */
    protected $isoCode = '';

    /**
     * Local name of the country zone
     *
     * @var string
     */
    protected $localName = '';

    /**
     * English name of the country zone
     *
     * @var string
     */
    protected $nameEn = '';

    /**
     * On initialization, get the columns mapping configuration
     */
    public function initializeObject()
    {
        parent::initializeObject();
        $this->tableName = ModelUtility::getModelMapping('SJBR\\StaticInfoTables\\Domain\\Model\\CountryZone', ModelUtility::MAPPING_TABLENAME);
        $this->columnsMapping = ModelUtility::getModelMapping('SJBR\\StaticInfoTables\\Domain\\Model\\CountryZone', ModelUtility::MAPPING_COLUMNS);
    }

    /**
     * Sets the country ISO alpha-2 code.
     *
     * @param string $countryIsoCodeA2
     *
     * @return void
     */
    public function setCountryIsoCodeA2($countryIsoCodeA2)
    {
        $this->countryIsoCodeA2 = $countryIsoCodeA2;
    }

    /**
     * Gets the country ISO alpha-2 code.
     *
     * @return string
     */
    public function getCountryIsoCodeA2()
    {
        return $this->countryIsoCodeA2;
    }

    /**
     * Sets the country ISO alpha-3 code.
     *
     * @param string $countryIsoCodeA3
     *
     * @return void
     */
    public function setCountryIsoCodeA3($countryIsoCodeA3)
    {
        $this->countryIsoCodeA3 = $countryIsoCodeA3;
    }

    /**
     * Gets the country ISO alpha-3 code.
     *
     * @return string
     */
    public function getCountryIsoCodeA3()
    {
        return $this->countryIsoCodeA3;
    }

    /**
     * Sets the country numeric ISO code
     *
     * @param int $countryIsoCodeNumber
     *
     * @return void
     */
    public function setCountryIsoCodeNumber($countryIsoCodeNumber)
    {
        $this->countryIsoCodeNumber = $countryIsoCodeNumber;
    }

    /**
     * Gets the country numeric ISO code
     *
     * @return int
     */
    public function getCountryIsoCodeNumber()
    {
        return $this->countryIsoCodeNumber;
    }

    /**
     * Gets the deletion status of the entity
     *
     * @return bool
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Sets the deletion status of the entity
     *
     * @param bool $deleted
     *
     * @return void
     */
    public function setDeleted($deleted)
    {
        return $this->deleted = $deleted;
    }

    /**
     * Sets the country zone ISO code.
     *
     * @param string $isoCode
     *
     * @return void
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = $isoCode;
    }

    /**
     * Gets the country zone ISO code.
     *
     * @return string
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * Sets the local name.
     *
     * @param string $localName
     *
     * @return void
     */
    public function setLocalName($localName)
    {
        $this->localName = $localName;
    }

    /**
     * Gets the local name.
     *
     * @return string
     */
    public function getLocalName()
    {
        return $this->localName;
    }

    /**
     * Sets the English name.
     *
     * @param string $nameEn
     *
     * @return void
     */
    public function setNameEn($nameEn)
    {
        $this->nameEn = $nameEn;
    }

    /**
     * Returns English name. If empty returns the localName.
     *
     * @return string
     */
    public function getNameEn()
    {
        if ($this->nameEn === '') {
            return $this->getLocalName();
        }
        return $this->nameEn;
    }
}
