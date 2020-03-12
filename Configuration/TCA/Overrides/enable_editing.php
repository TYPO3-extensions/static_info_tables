<?php
defined('TYPO3_MODE') or die();

if (TYPO3_MODE == 'BE' && !(TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_INSTALL)) {
    if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['static_info_tables']['enableManager']) {
        // Enable editing Static Info Tables
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['static_info_tables']['tables'])) {
            $tableNames = array_keys($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['static_info_tables']['tables']);
            foreach ($tableNames as $tableName) {
                $GLOBALS['TCA'][$tableName]['ctrl']['readOnly'] = 0;
            }
        }
    }
}
