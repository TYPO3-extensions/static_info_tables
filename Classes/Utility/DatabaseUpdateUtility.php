<?php
namespace SJBR\StaticInfoTables\Utility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2017 StanislasRolland <typo3@sjbr.ca>
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

use SJBR\StaticInfoTables\Database\SqlParser;
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
	 * @return void
	 */
	public function doUpdate($extensionKey)
	{
		$extPath = ExtensionManagementUtility::extPath($extensionKey);
		$fileContent = explode(LF, GeneralUtility::getUrl($extPath . 'ext_tables_static+adt.sql'));
		$sqlParser = GeneralUtility::makeInstance(SqlParser::class);
		foreach ($fileContent as $line) {
			$line = trim($line);
			if ($line && preg_match('#^UPDATE#i', $line)) {
				$parsedResult = $sqlParser->parseSQL($line);
				// WHERE clause
				$whereClause = $sqlParser->compileWhereClause($parsedResult['WHERE']);
				// Fields
				$fields = array();
				foreach ($parsedResult['FIELDS'] as $fN => $fV) {
					$fields[$fN] = $fV[0];
				}
				if (count($fields)) {
					if (class_exists('TYPO3\\CMS\\Core\\Database\\ConnectionPool')) {
						$queryBuilder = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)->getQueryBuilderForTable($parsedResult['TABLE']);
						$queryBuilder->getRestrictions()->removeAll();
						$queryBuilder->update($parsedResult['TABLE']);
						// We expect only a few of conditions combined by AND
						$whereExpressions = [];
						foreach ($parsedResult['WHERE'] as $k => $v) {
							$whereExpressions[] = $queryBuilder->expr()->eq($v['field'], $queryBuilder->createNamedParameter($v['value'][0]));
						}
						if (count($whereExpressions)) {
							$queryBuilder->where($whereExpressions[0]);
							array_shift($whereExpressions);
							foreach ($whereExpressions as $whereExpression) {
								$queryBuilder->andWhere($whereExpression);
							}
						}
						foreach ($fields as $fN => $fV) {
						   $queryBuilder->set($fN, $fV);
						}
						$queryBuilder->execute();
				   	} else {
				   		// WHERE clause
				   		$whereClause = $sqlParser->compileWhereClause($parsedResult['WHERE']);
				   		$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery($parsedResult['TABLE'], $whereClause, $fields);
				   	}
				}
			}
		}
	}
}