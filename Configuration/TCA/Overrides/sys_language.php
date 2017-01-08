<?php
defined('TYPO3_MODE') or die();

// Configure static_lang_isocode field in TCA
$GLOBALS['TCA']['sys_language']['columns']['static_lang_isocode'] = array(
	'exclude' => 1,
	'label' => 'LLL:EXT:lang/locallang_tca.xlf:sys_language.isocode',
	'config' => array(
		'type' => 'select',
		'renderType' => 'selectSingle',
		'items' => array(
			array('', 0)
		),
		'foreign_table' => 'static_languages',
		'foreign_table_where' => 'AND static_languages.pid=0 ORDER BY static_languages.lg_name_en',
		'itemsProcFunc' => \SJBR\StaticInfoTables\Hook\Backend\Form\FormDataProvider\TcaSelectItemsProcessor::class . '->translateLanguagesSelector',
		'size' => 1,
		'minitems' => 0,
		'maxitems' => 1,
		'wizards' => array(
			'suggest' => array(
				'type' => 'suggest',
				'default' => array(
					'receiverClass' => \SJBR\StaticInfoTables\Hook\Backend\Form\Wizard\SuggestReceiver::class
				)
			)
		)
	)
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('sys_language', 'static_lang_isocode', '', 'after:language_isocode');