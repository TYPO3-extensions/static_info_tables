<?php
namespace SJBR\StaticInfoTables\Utility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2019 StanislasRolland <typo3@sjbr.ca>
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

use Doctrine\DBAL\DBALException;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Utility used by the update script of the base extension and of the language packs
 */
class DatabaseUpdateUtility
{
    /**
     * @var string Name of the extension this class belongs to
     */
    protected $extensionName = 'StaticInfoTables';

    /**
     * Do the language pack update
     *
     * @param string $extensionKey: extension key of the language pack
     *
     * @return void
     */
    public function doUpdate($extensionKey)
    {
        $result = [];
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $insertStatements = [];
        $updateStatements = [];
        $extPath = ExtensionManagementUtility::extPath($extensionKey);
        $statements = explode(LF, GeneralUtility::getUrl($extPath . 'ext_tables_static+adt.sql'));

        foreach ($statements as $statement) {
            $statement = trim($statement);
            // Only handle update statements and extract the table at the same time. Extracting
            // the table name is required to perform the inserts on the right connection.
            if (preg_match('/^UPDATE\\s+`?(\\w+)`?(.*)/i', $statement, $matches)) {
                list(, $tableName, $sqlFragment) = $matches;
                $updateStatements[$tableName][] = sprintf(
                    'UPDATE %s %s',
                    $connectionPool->getConnectionForTable($tableName)->quoteIdentifier($tableName),
                    rtrim($sqlFragment, ';')
                );
            }
        }
        foreach ($updateStatements as $tableName => $perTableStatements) {
            $connection = $connectionPool->getConnectionForTable($tableName);
            foreach ((array)$perTableStatements as $statement) {
                try {
                    $connection->executeUpdate($statement);
                    $result[$statement] = '';
                } catch (DBALException $e) {
                    $result[$statement] = $e->getPrevious()->getMessage();
                }
            }
        }
    }
}
