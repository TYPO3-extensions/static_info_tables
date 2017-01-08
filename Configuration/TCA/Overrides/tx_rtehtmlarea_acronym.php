<?php
defined('TYPO3_MODE') or die();

if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('rtehtmlarea')) {
	$additionalColumns = array(
		'static_lang_isocode' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:rtehtmlarea/Resources/Private/Language/locallang_db.xlf:tx_rtehtmlarea_acronym.static_lang_isocode',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => array(
					array('', 0)
				),
				'foreign_table' => 'static_languages',
				'foreign_table_where' => 'ORDER BY static_languages.lg_name_en',
				'itemsProcFunc' => 'SJBR\\StaticInfoTables\\Hook\\Backend\\Form\\FormDataProvider\\TcaSelectItemsProcessor->translateLanguagesSelector',
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
			)
		)
	);

	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tx_rtehtmlarea_acronym', $additionalColumns);
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tx_rtehtmlarea_acronym', 'static_lang_isocode');
}