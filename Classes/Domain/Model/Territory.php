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
 * The Territory model
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
use SJBR\StaticInfoTables\Utility\ModelUtility;

class Territory extends AbstractEntity
{
    /**
     * Deletion status of the object
     *
     * @var bool
     */
    protected $deleted = false;

    /**
     * UN numeric territory code
     *
     * @var int
     */
    protected $unCodeNumber = 0;

    /**
     * English name
     *
     * @var string
     */
    protected $nameEn = '';

    /**
     * UN numeric territory code of parent territory
     *
     * @var int
     */
    protected $parentTerritoryUnCodeNumber = 0;

    /**
     * On initialization, get the columns mapping configuration
     */
    public function initializeObject()
    {
        parent::initializeObject();
        $this->tableName = ModelUtility::getModelMapping('SJBR\\StaticInfoTables\\Domain\\Model\\Territory', ModelUtility::MAPPING_TABLENAME);
        $this->columnsMapping = ModelUtility::getModelMapping('SJBR\\StaticInfoTables\\Domain\\Model\\Territory', ModelUtility::MAPPING_COLUMNS);
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
     * Returns the English name
     *
     * @return string
     */
    public function getNameEn()
    {
        return $this->nameEn;
    }

    /**
     * Sets the UN territory numeric code
     *
     * @param int $unCodeNumber
     *
     * @return void
     */
    public function setUnCodeNumber($unCodeNumber)
    {
        $this->unCodeNumber = $unCodeNumber;
    }

    /**
     * Returns the UN territory numeric code
     *
     * @return int
     */
    public function getUnCodeNumber()
    {
        return $this->unCodeNumber;
    }

    /**
     * Sets the UN numeric territory code of the parent territory
     *
     * @param int $parentTerritoryUnCodeNumber
     *
     * @return void
     */
    public function setParentTerritoryUnCodeNumber($parentTerritoryUnCodeNumber)
    {
        $this->parentTerritoryUnCodeNumber = $parentTerritoryUnCodeNumber;
    }

    /**
     * Returns the UN numeric territory code of the parent territory
     *
     * @return int
     */
    public function getParentTerritoryUnCodeNumber()
    {
        return $this->parentTerritoryUnCodeNumber;
    }
}
