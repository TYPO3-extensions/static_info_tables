<?php
/*
 * Register necessary class names with autoloader
 */
$extensionClassesPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('static_info_tables') . 'Classes/';
require_once($extensionClassesPath . 'Cache/ClassCacheBuilder.php');

$default = array(
	'SJBR\StaticInfoTables\PiBaseApi' => $extensionClassesPath . 'PiBaseApi.php',
	'SJBR\StaticInfoTables\Configuration\TypoScript\ConfigurationHelper' => $extensionClassesPath . 'Configuration/TypoScript/ConfigurationHelper.php',
	'SJBR\StaticInfoTables\Controller\AbstractController' => $extensionClassesPath . 'Controller/AbstractController.php',
	'SJBR\StaticInfoTables\Controller\ManagerController' => $extensionClassesPath . 'Controller/ManagerController.php',
	'SJBR\StaticInfoTables\Domain\Model\AbstractEntity' => $extensionClassesPath . 'Domain/Model/AbstractEntity.php',	
	'SJBR\StaticInfoTables\Domain\Model\Country' => $extensionClassesPath . 'Domain/Model/Country.php',
	'SJBR\StaticInfoTables\Domain\Model\CountryZone' => $extensionClassesPath . 'Domain/Model/CountryZone.php',
	'SJBR\StaticInfoTables\Domain\Model\Currency' => $extensionClassesPath . 'Domain/Model/Currency.php',
	'SJBR\StaticInfoTables\Domain\Model\Language' => $extensionClassesPath . 'Domain/Model/Language.php',
	'SJBR\StaticInfoTables\Domain\Model\LanguagePack' => $extensionClassesPath . 'Domain/Model/LanguagePack.php',
	'SJBR\StaticInfoTables\Domain\Model\Territory' => $extensionClassesPath . 'Domain/Model/Territory.php',
	'SJBR\StaticInfoTables\Domain\Repository\Country' => $extensionClassesPath . 'Domain/Repository/Country.php',
	'SJBR\StaticInfoTables\Domain\Repository\CountryZone' => $extensionClassesPath . 'Domain/Repository/CountryZone.php',
	'SJBR\StaticInfoTables\Domain\Repository\Currency' => $extensionClassesPath . 'Domain/Repository/Currency.php',
	'SJBR\StaticInfoTables\Domain\Repository\Language' => $extensionClassesPath . 'Domain/Repository/Language.php',
	'SJBR\StaticInfoTables\Domain\Repository\LanguagePack' => $extensionClassesPath . 'Domain/Repository/LanguagePack.php',
	'SJBR\StaticInfoTables\Domain\Repository\Territory' => $extensionClassesPath . 'Domain/Repository/Territory.php',
	'SJBR\StaticInfoTables\Hook\Backend\Form\ElementRenderingHelper' => $extensionClassesPath . 'Hook/Backend/Form/ElementRenderingHelper.php',
	'SJBR\StaticInfoTables\Hook\Backend\Form\SuggestReceiver' => $extensionClassesPath . 'Hook/Backend/Form/SuggestReceiver.php',
	'SJBR\StaticInfoTables\Hook\Core\DataHandling\ProcessDataMap' => $extensionClassesPath . 'Hook/Core/DataHandling/ProcessDataMap.php',
	'SJBR\StaticInfoTables\Utility\DatabaseUpdateUtility' => $extensionClassesPath . 'Utility/DatabaseUpdateUtility.php',
	'SJBR\StaticInfoTables\Utility\DatabaseUtility' => $extensionClassesPath . 'Utility/DatabaseUtility.php',
	'SJBR\StaticInfoTables\Utility\HtmlElementUtility' => $extensionClassesPath . 'Utility/HtmlElementUtility.php',	
	'SJBR\StaticInfoTables\Utility\LocaleUtility' => $extensionClassesPath . 'Utility/LocaleUtility.php',	
	'SJBR\StaticInfoTables\Utility\LocalizationUtility' => $extensionClassesPath . 'Utility/LocalizationUtility.php',
	'SJBR\StaticInfoTables\Utility\TcaUtility' => $extensionClassesPath . 'Utility/TcaUtility.php',
);

// Create extended domain model classes based on installed language packs
$classCacheBuilder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('SJBR\\StaticInfoTables\\Cache\\ClassCacheBuilder');
$mergedClasses = array_merge($default, $classCacheBuilder->build());

unset($extensionClassesPath);
unset($default);
unset($classCacheBuilder);
return $mergedClasses;
?>