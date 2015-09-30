<?php
defined('TYPO3_MODE') or die();
// Compatibility with 6.2
if (\TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(\TYPO3\CMS\Core\Utility\VersionNumberUtility::getNumericTypo3Version()) < 7000000) {
	$GLOBALS['TCA']['static_countries']['ctrl']['label_userFunc'] = 'SJBR\\StaticInfoTables\\Hook\\Backend\\Form\\ElementRenderingHelper->addIsoCodeToLabel';
	$GLOBALS['TCA']['static_countries']['columns']['cn_parent_territory_uid']['config']['itemsProcFunc'] = 'SJBR\\StaticInfoTables\\Hook\\Backend\\Form\\ElementRenderingHelper->translateTerritoriesSelector';
	$GLOBALS['TCA']['static_countries']['columns']['cn_currency_uid']['config']['itemsProcFunc'] = 'SJBR\\StaticInfoTables\\Hook\\Backend\\Form\\ElementRenderingHelper->translateCurrenciesSelector';
	$GLOBALS['TCA']['static_countries']['columns']['cn_currency_uid']['config']['wizards']['suggest']['default'] = array('receiverClass' => 'SJBR\\StaticInfoTables\\Hook\\Backend\\Form\\SuggestReceiver');
}