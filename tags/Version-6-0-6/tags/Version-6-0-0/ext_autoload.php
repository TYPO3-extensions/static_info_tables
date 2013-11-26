<?php
/*
 * Register necessary class names with autoloader
 */
// Create extended domain model classes based on installed language packs
$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('static_info_tables');
require_once($extensionPath . 'Classes/Cache/ClassCacheManager.php');
$classCacheManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('SJBR\\StaticInfoTables\\Cache\\ClassCacheManager');
return array_merge(
		array(
			'tx_staticinfotables_div' => $extensionPath . 'class.tx_staticinfotables_div.php',
			'SJBR\StaticInfoTables\Domain\Model\Country' => $extensionPath . 'Classes/Domain/Model/CountryExtensible.php',
			'SJBR\StaticInfoTables\Domain\Model\CountryZone' => $extensionPath . 'Classes/Domain/Model/CountryZoneExtensible.php',
			'SJBR\StaticInfoTables\Domain\Model\Currency' => $extensionPath . 'Classes/Domain/Model/CurrencyExtensible.php',
			'SJBR\StaticInfoTables\Domain\Model\Language' => $extensionPath . 'Classes/Domain/Model/LanguageExtensible.php',
			'SJBR\StaticInfoTables\Domain\Model\Territory' => $extensionPath . 'Classes/Domain/Model/TerritoryExtensible.php'
		),
		$classCacheManager->build()
	);
?>