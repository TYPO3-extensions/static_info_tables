<?php
namespace SJBR\StaticInfoTables\Service;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Verify TYPO3 DB table structure. Mainly used in install tool
 * compare wizard and extension manager.
 * Removed form Install extension in TYPO3 9 LTS
 */
class SqlSchemaMigrationService
{
    /**
     * @constant Maximum field width of MySQL
     */
    const MYSQL_MAXIMUM_FIELD_WIDTH = 64;

    /**
     * @var string Prefix of deleted tables
     */
    protected $deletedPrefixKey = 'zzz_deleted_';

    /**
     * @var array Caching output "SHOW CHARACTER SET"
     */
    protected $character_sets = [];

    /**
     * Reads the field definitions for the current database
     *
     * @return array Array with information about table.
     */
    public function getFieldDefinitions_database()
    {
        $total = [];
        $tempKeys = [];
        $tempKeysPrefix = [];
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionByName('Default');
        $statement = $connection->query('SHOW TABLE STATUS FROM `' . $connection->getDatabase() . '`');
        $tables = [];
        while ($theTable = $statement->fetch()) {
            $tables[$theTable['Name']] = $theTable;
        }
        foreach ($tables as $tableName => $tableStatus) {
            // Fields
            $statement = $connection->query('SHOW FULL COLUMNS FROM `' . $tableName . '`');
            $fieldInformation = [];
            while ($fieldRow = $statement->fetch()) {
                $fieldInformation[$fieldRow['Field']] = $fieldRow;
            }
            foreach ($fieldInformation as $fN => $fieldRow) {
                $total[$tableName]['fields'][$fN] = $this->assembleFieldDefinition($fieldRow);
            }
            // Keys
            $statement = $connection->query('SHOW KEYS FROM `' . $tableName . '`');
            $keyInformation = [];
            while ($keyRow = $statement->fetch()) {
                $keyInformation[] = $keyRow;
            }
            foreach ($keyInformation as $keyRow) {
                $keyName = $keyRow['Key_name'];
                $colName = $keyRow['Column_name'];
                if ($keyRow['Sub_part'] && $keyRow['Index_type'] !== 'SPATIAL') {
                    $colName .= '(' . $keyRow['Sub_part'] . ')';
                }
                $tempKeys[$tableName][$keyName][$keyRow['Seq_in_index']] = $colName;
                if ($keyName === 'PRIMARY') {
                    $prefix = 'PRIMARY KEY';
                } else {
                    if ($keyRow['Index_type'] === 'FULLTEXT') {
                        $prefix = 'FULLTEXT';
                    } elseif ($keyRow['Index_type'] === 'SPATIAL') {
                        $prefix = 'SPATIAL';
                    } elseif ($keyRow['Non_unique']) {
                        $prefix = 'KEY';
                    } else {
                        $prefix = 'UNIQUE';
                    }
                    $prefix .= ' ' . $keyName;
                }
                $tempKeysPrefix[$tableName][$keyName] = $prefix;
            }
            // Table status (storage engine, collaction, etc.)
            if (is_array($tableStatus)) {
                $tableExtraFields = [
                    'Engine' => 'ENGINE',
                    'Collation' => 'COLLATE',
                ];
                foreach ($tableExtraFields as $mysqlKey => $internalKey) {
                    if (isset($tableStatus[$mysqlKey])) {
                        $total[$tableName]['extra'][$internalKey] = $tableStatus[$mysqlKey];
                    }
                }
            }
        }
        // Compile key information:
        if (!empty($tempKeys)) {
            foreach ($tempKeys as $table => $keyInf) {
                foreach ($keyInf as $kName => $index) {
                    ksort($index);
                    $total[$table]['keys'][$kName] = $tempKeysPrefix[$table][$kName] . ' (' . implode(',', $index) . ')';
                }
            }
        }
        return $total;
    }

    /**
     * Converts a result row with field information into the SQL field definition string
     *
     * @param array $row MySQL result row
     *
     * @return string Field definition
     */
    public function assembleFieldDefinition($row)
    {
        $field = [$row['Type']];
        if ($row['Null'] === 'NO') {
            $field[] = 'NOT NULL';
        }
        if (!strstr($row['Type'], 'blob') && !strstr($row['Type'], 'text')) {
            // Add a default value if the field is not auto-incremented (these fields never have a default definition)
            if (!stristr($row['Extra'], 'auto_increment')) {
                if ($row['Default'] === null) {
                    $field[] = 'default NULL';
                } else {
                    $field[] = 'default \'' . addslashes($row['Default']) . '\'';
                }
            }
        }
        if ($row['Extra']) {
            $field[] = $row['Extra'];
        }
        if (trim($row['Comment']) !== '') {
            $field[] = "COMMENT '" . $row['Comment'] . "'";
        }
        return implode(' ', $field);
    }
}
