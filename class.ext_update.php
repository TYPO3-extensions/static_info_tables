<?php
namespace SJBR\StaticInfoTables;

/*
 *  Copyright notice
 *
 *  (c) 2013-2017 Stanislas Rolland <typo3(arobas)sjbr.ca>
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
 */

use SJBR\StaticInfoTables\Cache\ClassCacheManager;
use SJBR\StaticInfoTables\Utility\DatabaseUpdateUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extensionmanager\Utility\InstallUtility;
use TYPO3\CMS\Install\Service\SqlSchemaMigrationService;

/**
 * Class for updating the db
 */
class ext_update
{
	/**
	 * @var string Name of the extension this controller belongs to
	 */
	protected $extensionName = 'StaticInfoTables';

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager Extbase Object Manager
	 */
	protected $objectManager;
	
	/**
	 * @var \TYPO3\CMS\Extensionmanager\Utility\InstallUtility Extension Manager Install Tool
	 */
	protected $installTool;

	/**
	 * Main function, returning the HTML content
	 *
	 * @return string HTML
	 */
	public function main()
	{
		$content = '';

		$this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
		$this->installTool = $this->objectManager->get(InstallUtility::class);
		$installToolSqlParser = GeneralUtility::makeInstance(SqlSchemaMigrationService::class);
		$this->installTool->injectInstallToolSqlParser($installToolSqlParser);
		$databaseUpdateUtility = GeneralUtility::makeInstance(DatabaseUpdateUtility::class);

		// Clear the class cache
		$classCacheManager = GeneralUtility::makeInstance(ClassCacheManager::class);
		$classCacheManager->reBuild();

		// Process the database updates of this base extension (we want to re-process these updates every time the update script is invoked)
		$extensionSitePath = ExtensionManagementUtility::extPath(GeneralUtility::camelCaseToLowerCaseUnderscored($this->extensionName));
		$content .= '<p>' . nl2br(LocalizationUtility::translate('updateTables', $this->extensionName)) . '</p>';
		$this->importStaticSqlFile($extensionSitePath);

		// Get the extensions which want to extend static_info_tables
		$loadedExtensions = array_unique(ExtensionManagementUtility::getLoadedExtensionListArray());
		foreach ($loadedExtensions as $extensionKey) {
			$extensionInfoFile = ExtensionManagementUtility::extPath($extensionKey) . 'Configuration/DomainModelExtension/StaticInfoTables.txt';
			if (file_exists($extensionInfoFile)) {
				// We need to reprocess the database structure update sql statements (ext_tables)
				$this->processDatabaseUpdates($extensionKey);
				// Now we process the static data updates (ext_tables_static+adt)
				// Note: The Install Tool Utility does not handle sql update statements
				$databaseUpdateUtility->doUpdate($extensionKey);
				$content .= '<p>' . nl2br(LocalizationUtility::translate('updateLanguageLabels', $this->extensionName)) . ' ' . $extensionKey . '</p>';
			}
		}
		if (!$content) {
			// Nothing to do
			$content .= '<p>' . nl2br(LocalizationUtility::translate('nothingToDo', $this->extensionName)) . '</p>';
		}
		// Notice for old language packs
		$content .= '<p>' . nl2br(LocalizationUtility::translate('update.oldLanguagePacks', $this->extensionName)) . '</p>';
		return $content;
	}

	/**
	 * Processes the tables SQL File (ext_tables)
	 *
	 * @param string $extensionKey
	 * @return void
	 */
	protected function processDatabaseUpdates($extensionKey)
	{
		$extensionSitePath = ExtensionManagementUtility::extPath($extensionKey);
		$extTablesSqlFile = $extensionSitePath . 'ext_tables.sql';
		$extTablesSqlContent = '';
		if (file_exists($extTablesSqlFile)) {
			$extTablesSqlContent .= GeneralUtility::getUrl($extTablesSqlFile);
		}
		if ($extTablesSqlContent !== '') {
			$this->installTool->updateDbWithExtTablesSql($extTablesSqlContent);
		}
	}

	/**
	 * Imports a static tables SQL File (ext_tables_static+adt)
	 *
	 * @param string $extensionSitePath
	 * @return void
	 */
	protected function importStaticSqlFile($extensionSitePath)
	{
		$extTablesStaticSqlFile = $extensionSitePath . 'ext_tables_static+adt.sql';
		$extTablesStaticSqlContent = '';
		if (file_exists($extTablesStaticSqlFile)) {
			$extTablesStaticSqlContent .= GeneralUtility::getUrl($extTablesStaticSqlFile);
		}
		if ($extTablesStaticSqlContent !== '') {
			if (class_exists('TYPO3\\CMS\\Core\\Database\\ConnectionPool')) {
				// TYPO3 CMS 8+ LTS
				$connectionPool = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class);
				// Drop all tables
				foreach (array_keys($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['static_info_tables']['tables']) as $tableName) {
					$connection = $connectionPool->getConnectionForTable($tableName);
					try {
						$connection->executeUpdate($connection->getDatabasePlatform()->getDropTableSQL($connection->quoteIdentifier($tableName)));
					} catch (\Doctrine\DBAL\Exception\TableNotFoundException $e) {
						// Ignore table not found exception
					}
				}
				// Re-create all tables
				$this->processDatabaseUpdates(GeneralUtility::camelCaseToLowerCaseUnderscored($this->extensionName));
			}
			$this->installTool->importStaticSql($extTablesStaticSqlContent);
		}
	}

	public function access()
	{
		return true;
	}
}