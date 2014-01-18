<?php
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use \TYPO3\CMS\Extbase\Utility\LocalizationUtility;
/***************************************************************
*  Copyright notice
*
*  (c) 2013-2014 Stanislas Rolland <typo3(arobas)sjbr.ca>
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
/**
 * Class for updating the db
 */
class ext_update {
	/**
	 * @var string Name of the extension this controller belongs to
	 */
	protected $extensionName = 'StaticInfoTables';

	/**
	 * @var TYPO3\CMS\Extbase\Object\ObjectManager Extbase Object Manager
	 */
	protected $objectManager;

	/**
	 * Main function, returning the HTML content
	 *
	 * @return string HTML
	 */
	function main()	{
		$content = '';

		$this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		$databaseUpdateUtility = $this->objectManager->get('SJBR\\StaticInfoTables\\Utility\\DatabaseUpdateUtility');
		
		// Clear the class cache
		$classCacheManager = $this->objectManager->get('SJBR\\StaticInfoTables\\Cache\\ClassCacheManager');
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
	 * Imports a static tables SQL File (ext_tables_static+adt)
	 *
	 * @param string $extensionSitePath
	 * @return void
	 */
	protected function importStaticSqlFile($extensionSitePath) {
		$extTablesStaticSqlFile = $extensionSitePath . 'ext_tables_static+adt.sql';
		if (file_exists($extTablesStaticSqlFile)) {
			$extTablesStaticSqlContent = GeneralUtility::getUrl($extTablesStaticSqlFile);
			$installTool = $this->objectManager->get('TYPO3\\CMS\\ExtensionManager\\Utility\\InstallUtility');
			$installTool->importStaticSql($extTablesStaticSqlContent);
		}
	}

	function access() {
		return TRUE;
	}
}
?>