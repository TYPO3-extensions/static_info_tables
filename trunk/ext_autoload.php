<?php
/*
 * Register necessary class names with autoloader
 *
 */
$extensionClassesPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('static_info_tables') . 'Classes/';
return array(
	'SJBR\StaticInfoTables\PiBaseApi' => $extensionClassesPath . 'PiBaseApi.php',
	'SJBR\StaticInfoTables\Hook\Backend\Form\ElementRenderingHelper' => $extensionClassesPath . 'Hook/Backend/Form/ElementRenderingHelper.php',
	'SJBR\StaticInfoTables\Hook\Backend\Form\SuggestReceiver' => $extensionClassesPath . 'Hook/Backend/Form/SuggestReceiver.php',
	'SJBR\StaticInfoTables\Hook\Core\DataHandling\ProcessDataMap' => $extensionClassesPath . 'Hook/Core/DataHandling/ProcessDataMap.php',
	'SJBR\StaticInfoTables\Utility\LocalizationUtility' => $extensionClassesPath . 'Utility/LocalizationUtility.php',
	'SJBR\StaticInfoTables\Utility\TcaUtility' => $extensionClassesPath . 'Utility/TcaUtility.php',
);
unset($extensionClassesPath);
?>