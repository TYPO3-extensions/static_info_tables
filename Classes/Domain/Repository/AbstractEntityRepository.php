<?php
namespace SJBR\StaticInfoTables\Domain\Repository;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011-2012 Armin Rüdiger Vieweg <info@professorweb.de>
 *  (c) 2013-2018 Stanislas Rolland <typo3(arobas)sjbr.ca>
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

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\DBAL\Types\Type;
use SJBR\StaticInfoTables\Service\SqlSchemaMigrationService;
use SJBR\StaticInfoTables\Utility\DatabaseUtility;
use SJBR\StaticInfoTables\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Abstract Repository for static entities
 *
 * @author Armin Rüdiger Vieweg <info@professorweb.de>
 * @author Stanislas Rolland <typo3(arobas)sjbr.ca>
 * @author Oliver Klee <typo3-coding@oliverklee.de>
 */
abstract class AbstractEntityRepository extends Repository
{
    /**
     * @var string Name of the extension this class belongs to
     */
    protected $extensionName = 'StaticInfoTables';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper
     */
    protected $dataMapper;

    /**
     * @var array ISO keys for this static table
     */
    protected $isoKeys = [];

    /**
     * Injects the DataMapper to map nodes to objects
     *
     * @param DataMapper $dataMapper
     *
     * @return void
     */
    public function injectDataMapper(DataMapper $dataMapper)
    {
        $this->dataMapper = $dataMapper;
    }

    /**
     * Initializes the repository.
     *
     * @return void
     */
    public function initializeObject()
    {
        $querySettings = $this->objectManager->get(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * Find all with deleted included
     *
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array all entries
     */
    public function findAllDeletedIncluded()
    {
        $querySettings = $this->objectManager->get(QuerySettingsInterface::class);
        $querySettings->setStoragePageIds([0]);
        $querySettings->setIncludeDeleted(true);
        $this->setDefaultQuerySettings($querySettings);
        return parent::findAll();
    }

    /**
     * Find all objects with uid in list
     *
     * @param string $list: list of uid's
     *
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array all entries
     */
    public function findAllByUidInList($list = '')
    {
        if (empty($list)) {
            return [];
        }
        $query = $this->createQuery();
        $list = GeneralUtility::trimExplode(',', $list, true);
        $query->matching($query->in('uid', $list));
        return $query->execute();
    }

    /**
     * Find all ordered by the localized name
     *
     * @param string $orderDirection may be "asc" or "desc". Default is "asc".
     *
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array all entries ordered by localized name
     */
    protected function findAllOrderedByLocalizedName($orderDirection = 'asc')
    {
        $entities = parent::findAll();
        return $this->localizedSort($entities, $orderDirection);
    }

    /**
     * Sort entities by the localized name
     *
     * @param QueryResultInterface $entities to be sorted
     * @param string $orderDirection may be "asc" or "desc". Default is "asc".
     *
     * @return array entities ordered by localized name
     */
    public function localizedSort(QueryResultInterface $entities, $orderDirection = 'asc')
    {
        $result = $entities->toArray();
        $locale = LocalizationUtility::setCollatingLocale();
        if ($locale !== false) {
            if ($orderDirection === 'asc') {
                uasort($result, [$this, 'strcollOnLocalizedName']);
            } else {
                uasort($result, [$this, 'strcollOnLocalizedNameDesc']);
            }
        }
        return $result;
    }

    /**
     * Using strcoll comparison on localized names
     *
     * @return int see strcoll
     *
     * @param mixed $entityA
     * @param mixed $entityB
     */
    protected function strcollOnLocalizedName($entityA, $entityB)
    {
        return strcoll($entityA->getNameLocalized(), $entityB->getNameLocalized());
    }

    /**
     * Using strcoll comparison on localized names - descending order
     *
     * @return int see strcoll
     *
     * @param mixed $entityA
     * @param mixed $entityB
     */
    protected function strcollOnLocalizedNameDesc($entityA, $entityB)
    {
        return strcoll($entityB->getNameLocalized(), $entityA->getNameLocalized());
    }

    /**
     * Find all ordered by given property name
     *
     * @param string $propertyName property name to order by
     * @param string $orderDirection may be "asc" or "desc". Default is "asc".
     *
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array all entries ordered by $propertyName
     */
    public function findAllOrderedBy($propertyName, $orderDirection = 'asc')
    {
        $queryResult = [];

        if ($orderDirection !== 'asc' && $orderDirection !== 'desc') {
            throw new \InvalidArgumentException('Order direction must be "asc" or "desc".', 1316607580);
        }

        if ($propertyName == 'nameLocalized') {
            $queryResult = $this->findAllOrderedByLocalizedName($orderDirection);
        } else {
            $query = $this->createQuery();

            $object = $this->objectManager->get($this->objectType);
            if (!array_key_exists($propertyName, $object->_getProperties())) {
                throw new \InvalidArgumentException('The model "' . $this->objectType . '" has no property "' . $propertyName . '" to order by.', 1316607579);
            }

            if ($orderDirection === 'asc') {
                $orderDirection = QueryInterface::ORDER_ASCENDING;
            } else {
                $orderDirection = QueryInterface::ORDER_DESCENDING;
            }
            $query->setOrderings([$propertyName => $orderDirection]);

            return $query->execute();
        }
        return $queryResult;
    }

    /**
     * Adds localization columns, if needed
     *
     * @param string $locale: the locale for which localization columns should be added
     *
     * @return AbstractEntityRepository $this
     */
    public function addLocalizationColumns($locale)
    {
        $dataMap = $this->dataMapper->getDataMap($this->objectType);
        $tableName = $dataMap->getTableName();
        $fieldsInfo = $this->getFieldsInfo();
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        foreach ($fieldsInfo as $field => $fieldInfo) {
            if ($field != 'cn_official_name_en') {
                $matches = [];
                if (preg_match('#_en$#', $field, $matches)) {
                    // Make localization field name
                    $localizationField = preg_replace('#_en$#', '_' . $locale, $field);
                    // Add the field if it does not yet exist
                    if (!$fieldsInfo[$localizationField]) {
                        // Get field length
                        $matches = [];
                        if (preg_match('#\\(([0-9]+)\\)#', $fieldInfo['Type'], $matches)) {
                            $localizationFieldLength = (int)($matches[1]);
                            // Add the localization field
                            $connection = $connectionPool->getConnectionForTable($tableName);
                            $column = new Column($localizationField, Type::getType(Type::STRING));
                            $column->setLength($localizationFieldLength)
                                ->setNotnull(true)
                                ->setDefault('');
                            $tableDiff = new TableDiff($tableName, [$column]);
                            $query = $connection->getDatabasePlatform()->getAlterTableSQL($tableDiff);
                            $connection->executeUpdate($query[0]);
                        }
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Get the information on the table fields
     *
     * @return array table fields information array
     */
    protected function getFieldsInfo()
    {
        $fieldsInfo = [];
        $dataMap = $this->dataMapper->getDataMap($this->objectType);
        $tableName = $dataMap->getTableName();
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $connection = $connectionPool->getConnectionForTable($tableName);
        $query = $connection->getDatabasePlatform()->getListTableColumnsSQL($tableName, $connection->getDatabase());
        $columnsInfo = $connection->executeQuery($query);
        foreach ($columnsInfo as $fieldRow) {
            $fieldsInfo[$fieldRow['Field']] = $fieldRow;
        }
        return $fieldsInfo;
    }

    /**
     * Get update queries for the localization columns for a given locale
     *
     * @return array Update queries
     *
     * @param mixed $locale
     */
    public function getUpdateQueries($locale)
    {
        // Get the information of the table and its fields
        $dataMap = $this->dataMapper->getDataMap($this->objectType);
        $tableName = $dataMap->getTableName();
        $tableFields = array_keys($this->getFieldsInfo());
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $connection = $connectionPool->getConnectionForTable($tableName);
        $updateQueries = [];
        // If the language pack is not yet created or not yet installed, the localization columns are not yet part of the domain model
        $exportFields = [];
        foreach ($tableFields as $field) {
            $matches = [];
            if (preg_match('#_' . strtolower($locale) . '$#', $field, $matches)) {
                $exportFields[] = $field;
            }
        }
        if (count($exportFields)) {
            $updateQueries[] = '## ' . $tableName;
            $exportFields = array_merge($exportFields, $this->isoKeys);
            $queryBuilder = $connectionPool->getQueryBuilderForTable($tableName);
            $queryBuilder->getRestrictions()->removeAll();
            $queryBuilder->select($exportFields[0]);
            array_shift($exportFields);
            foreach ($exportFields as $exportField) {
                $queryBuilder->addSelect($exportField);
            }
            $rows = $queryBuilder
                ->from($tableName)
                ->execute()
                ->fetchAll();
            foreach ($rows as $row) {
                $set = [];
                foreach ($row as $field => $value) {
                    if (!in_array($field, $this->isoKeys)) {
                        $set[] = $field . '=' . $connection->quote($value);
                    }
                }
                $whereClause = '';
                foreach ($this->isoKeys as $field) {
                    $whereClause .= ($whereClause ? ' AND ' : ' WHERE ') . $field . '=' . $connection->quote($row[$field]);
                }
                $updateQueries[] = 'UPDATE ' . $tableName . ' SET ' . implode(',', $set) . $whereClause . ';';
            }
        }
        return $updateQueries;
    }

    /**
     * Dump non-localized contents of the repository
     */
    public function sqlDumpNonLocalizedData()
    {
        // Get the information of the table and its fields
        $dataMap = $this->dataMapper->getDataMap($this->objectType);
        $tableName = $dataMap->getTableName();

        $sqlSchemaMigrationService = $this->objectManager->get(SqlSchemaMigrationService::class);
        $dbFieldDefinitions = $sqlSchemaMigrationService->getFieldDefinitions_database();
        $dbFields = [];
        $dbFields[$tableName] = $dbFieldDefinitions[$tableName];

        $extensionKey = GeneralUtility::camelCaseToLowerCaseUnderscored($this->extensionName);
        $extensionPath = ExtensionManagementUtility::extPath($extensionKey);
        $ext_tables = GeneralUtility::getUrl($extensionPath . 'ext_tables.sql');

        $tableFields = array_keys($dbFields[$tableName]['fields']);
        foreach ($tableFields as $field) {
            // This is a very simple check if the field is from static_info_tables and not from a language pack
            $match = [];
            if (!preg_match('#' . preg_quote($field) . '#m', $ext_tables, $match)) {
                unset($dbFields[$tableName]['fields'][$field]);
            }
        }

        $databaseUtility = GeneralUtility::makeInstance(DatabaseUtility::class);
        return $databaseUtility->dumpStaticTables($dbFields);
    }

    /**
     * Adds an object to this repository.
     *
     * @param object $object The object to add
     *
     * @return void
     *
     * @throws \BadMethodCallException(
     */
    public function add($object)
    {
        throw new \BadMethodCallException(
            'This is a read-only repository in which the add method must not be called.',
            1420485488
        );
    }

    /**
     * Removes an object from this repository.
     *
     * @param object $object The object to remove
     *
     * @return void
     *
     * @throws \BadMethodCallException(
     */
    public function remove($object)
    {
        throw new \BadMethodCallException(
            'This is a read-only repository in which the remove method must not be called.',
            1420485646
        );
    }

    /**
     * Replaces an existing object with the same identifier by the given object.
     *
     * @param object $modifiedObject The modified object
     *
     * @return void
     *
     * @throws \BadMethodCallException(
     */
    public function update($modifiedObject)
    {
        throw new \BadMethodCallException(
            'This is a read-only repository in which the update method must not be called.',
            1420485660
        );
    }

    /**
     * Removes all objects of this repository as if remove() was called for all of them.
     *
     * @return void
     *
     * @throws \BadMethodCallException(
     */
    public function removeAll()
    {
        throw new \BadMethodCallException(
            'This is a read-only repository in which the removeAll method must not be called.',
            1420485668
        );
    }
}
