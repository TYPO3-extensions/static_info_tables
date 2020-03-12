<?php
namespace SJBR\StaticInfoTables\Domain\Repository;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2017 Stanislas Rolland <typo3@sjbr.ca>
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
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * System language repository
 */
class SystemLanguageRepository extends Repository
{
    /**
     * Find all system language objects with uid in list
     * If no list is provided, find all system language objects
     *
     * @param string $list: list of uid's
     *
     * @return QueryResultInterface|array all entries
     */
    public function findAllByUidInList($list = '')
    {
        if (empty($list)) {
            return $this->findAll();
        }
        $query = $this->createQuery();
        $list = GeneralUtility::trimExplode(',', $list, true);
        $query->matching($query->in('uid', $list));
        return $query->execute();
    }
}
