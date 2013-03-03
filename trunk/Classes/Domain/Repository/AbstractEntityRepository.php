<?php
namespace SJBR\StaticInfoTables\Domain\Repository;
/***************************************************************
*  Copyright notice
*
*  (c) 2011-2012 Armin Rüdiger Vieweg <info@professorweb.de>
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
 * Abstract Repository for static entities
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
abstract class AbstractEntityRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {
	/**
	 * Find all ordered by given field name
	 *
	 * @param string $fieldName field name to order by
	 * @param string $orderDirection may be "asc" or "desc". Default is "asc".
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array all entries ordered by $fieldName
	 */
	public function findAllOrderedBy($fieldName, $orderDirection = 'asc') {
		$query = $this->createQuery();

		$object = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($this->objectType);
		if (!array_key_exists($fieldName, $object->_getProperties())) {
			throw new InvalidArgumentException('The model "' . $this->objectType . '" has no attribute "' . $fieldName . '" to order by.', 1316607579);
		}
		if ($orderDirection !== 'asc' && $orderDirection !== 'desc') {
			throw new InvalidArgumentException('Order direction must be "asc" or "desc".', 1316607580);
		}

		if ($orderDirection === 'asc') {
			$orderDirection = \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING;
		} else {
			$orderDirection = \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING;
		}
		$query->setOrderings(array($fieldName => $orderDirection));

		return $query->execute();
	}
}
?>