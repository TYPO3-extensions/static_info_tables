<?php
/*
 * Register necessary class names with autoloader
 */
// Create extended domain model classes based on installed language packs
$extensionClassesPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('static_info_tables') . 'Classes/';
require_once($extensionClassesPath . 'Cache/ClassCacheBuilder.php');
$classCacheBuilder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('SJBR\\StaticInfoTables\\Cache\\ClassCacheBuilder');
return $classCacheBuilder->build();

?>