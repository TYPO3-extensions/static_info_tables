<?php
// Use pre-8 LTS suggest options
if (\TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(\TYPO3\CMS\Core\Utility\VersionNumberUtility::getNumericTypo3Version()) < 8000000) {
	$GLOBALS['TCA']['static_countries']['columns']['cn_currency_uid']['config'] = [
		'type' => 'select',
		'renderType' => 'selectSingle',
		'items' => array(
			array('', 0),
		),
		'foreign_table' => 'static_currencies',
		'foreign_table_where' => 'ORDER BY static_currencies.cu_name_en',
		'itemsProcFunc' => 'SJBR\\StaticInfoTables\\Hook\\Backend\\Form\\FormDataProvider\\TcaSelectItemsProcessor->translateCurrenciesSelector',
		'size' => 1,
		'minitems' => 0,
		'maxitems' => 1,
		'wizards' => array(
			'suggest' => array(
				'type' => 'suggest',
				'default' => array(
					'receiverClass' => 'SJBR\\StaticInfoTables\\Hook\\Backend\\Form\\Wizard\\SuggestReceiver'
				)
			)
		)
	];
}
