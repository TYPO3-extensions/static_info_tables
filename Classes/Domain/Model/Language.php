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
 * The Language model
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
use SJBR\StaticInfoTables\Utility\ModelUtility;

class Language extends AbstractEntity
{
    /**
     * @var string
     */
    protected $collatingLocale = '';

    /**
     * Country code as two digit string (i.e. AT)
     * Identifies this language as a variant of the language identified by the ISO 639-1 A2 Language code
     * See also RFC 4646
     * ISO 3166-1 A2 Country code
     *
     * @var string
     */
    protected $countryIsoCodeA2 = '';

    /**
     * @var bool
     */
    protected $constructedLanguage = false;

    /**
     * Deletion status of the object
     *
     * @var bool
     */
    protected $deleted = false;

    /**
     * ISO 639-1 A2 Language code
     *
     * @var string
     */
    protected $isoCodeA2 = '';

    /**
     * Local name: name of language in the language itself
     *
     * @var string
     */
    protected $localName = '';

    /**
     * English name
     *
     * @var string
     */
    protected $nameEn = '';

    /**
     * @var bool
     */
    protected $sacredLanguage = false;

    /**
     * @var string
     */
    protected $typo3Code = '';

    /**
     * On initialization, get the columns mapping configuration
     */
    public function initializeObject()
    {
        parent::initializeObject();
        $this->tableName = ModelUtility::getModelMapping('SJBR\\StaticInfoTables\\Domain\\Model\\Language', ModelUtility::MAPPING_TABLENAME);
        $this->columnsMapping = ModelUtility::getModelMapping('SJBR\\StaticInfoTables\\Domain\\Model\\Language', ModelUtility::MAPPING_COLUMNS);
    }

    /**
     * Sets the collating locale.
     *
     * @param string $collatingLocale
     *
     * @return void
     */
    public function setCollatingLocale($collatingLocale)
    {
        $this->collatingLocale = $collatingLocale;
    }

    /**
     * Gets the collating locale.
     *
     * @return string
     */
    public function getCollatingLocale()
    {
        return $this->collatingLocale;
    }

    /**
     * Sets the ISO 3166-1 A2 Country code
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
     * Gets the ISO 3166-1 A2 Country code
     *
     * @return string
     */
    public function getCountryIsoCodeA2()
    {
        return $this->countryIsoCodeA2;
    }

    /**
     * Sets whether this is a constructed language.
     *
     * @param bool $constructedLanguage
     *
     * @return void
     */
    public function setConstructedLanguage($constructedLanguage)
    {
        $this->constructedLanguage = $constructedLanguage;
    }

    /**
     * Gets whether this is a constructed language.
     *
     * @return bool
     */
    public function getConstructedLanguage()
    {
        return $this->constructedLanguage;
    }

    /**
     * Gets whether this is a constructed language.
     *
     * This method is a synonym for the getConstructedLanguage method.
     *
     * @return bool
     */
    public function isConstructedLanguage()
    {
        return $this->getConstructedLanguage();
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
     * Sets the ISO 639-1 A2 Language code
     *
     * @param string $isoCodeA2
     *
     * @return void
     */
    public function setIsoCodeA2($isoCodeA2)
    {
        $this->isoCodeA2 = $isoCodeA2;
    }

    /**
     * Gets the ISO 639-1 A2 Language code
     *
     * @return string
     */
    public function getIsoCodeA2()
    {
        return $this->isoCodeA2;
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
     * Sets the English name
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
     * Gets the English name
     *
     * @return string
     */
    public function getNameEn()
    {
        return $this->nameEn;
    }

    /**
     * Sets whether this is a sacred language.
     *
     * @param bool $sacredLanguage
     *
     * @return void
     */
    public function setSacredLanguage($sacredLanguage)
    {
        $this->sacredLanguage = $sacredLanguage;
    }

    /**
     * Gets whether this is a sacred language.
     *
     * @return bool
     */
    public function getSacredLanguage()
    {
        return $this->sacredLanguage;
    }

    /**
     * Sets whether this is a sacred language.
     *
     * This method is a synonym for the getSacredLanguage method.
     *
     * @return bool
     */
    public function isSacredLanguage()
    {
        return $this->getSacredLanguage();
    }

    /**
     * Sets the TYPO3 language code.
     *
     * @param string $typo3Code
     *
     * @return void
     */
    public function setTypo3Code($typo3Code)
    {
        $this->typo3Code = $typo3Code;
    }

    /**
     * Gets the TYPO3 language code.
     *
     * @return string
     */
    public function getTypo3Code()
    {
        return $this->typo3Code;
    }
}
