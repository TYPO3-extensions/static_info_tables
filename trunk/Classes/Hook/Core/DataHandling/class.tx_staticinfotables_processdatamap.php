<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Stanislas Rolland <typo3(arobas)sjbr.ca>
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
 * Hook on Core/DataHandling/DataHandler to manage redundancy of ISO codes in static info tables
 */
class tx_staticinfotables_processDataMap {
	/**
	 * Pre-process redundant ISO codes fields
	 *
	 * @param	object		$fobj TCEmain object reference
	 * @return	void
	 */
	public function processDatamap_preProcessFieldArray (&$incomingFieldArray, $table, $id, &$fObj) {
		switch ($table) {
			case 'static_territories':
				//Pre-process territory ISO number
				if ($incomingFieldArray['tr_parent_territory_uid']) {
					$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
						'uid,tr_iso_nr',
						'static_territories',
						'uid = ' . intval($incomingFieldArray['tr_parent_territory_uid']) . t3lib_befunc::deleteClause('static_territories')
					);
					$incomingFieldArray['tr_parent_iso_nr'] = $rows[0]['tr_iso_nr'];
				} else {
					$incomingFieldArray['tr_parent_iso_nr'] = NULL;
				}
				break;
			case 'static_countries':
				//Pre-process territory ISO number
				if ($incomingFieldArray['cn_parent_territory_uid']) {
					$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
						'uid,tr_iso_nr',
						'static_territories',
						'uid = ' . intval($incomingFieldArray['cn_parent_territory_uid']) . t3lib_befunc::deleteClause('static_territories')
					);
					$incomingFieldArray['cn_parent_tr_iso_nr'] = $rows[0]['tr_iso_nr'];
				} else {
					$incomingFieldArray['cn_parent_tr_iso_nr'] = NULL;
				}
				//Pre-process currency ISO numeric and A3 code
				if ($incomingFieldArray['cn_currency_uid']) {
					$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
						'uid,cu_iso_nr,cu_iso_3',
						'static_currencies',
						'uid = ' . intval($incomingFieldArray['cn_currency_uid']) . t3lib_befunc::deleteClause('static_currencies')
					);
					$incomingFieldArray['cn_currency_iso_nr'] = $rows[0]['cu_iso_nr'];
					$incomingFieldArray['cn_currency_iso_3'] = $rows[0]['cu_iso_3'];
				} else {
					$incomingFieldArray['cn_currency_iso_nr'] = NULL;
					$incomingFieldArray['cn_currency_iso_3'] = NULL;
				}
				break;
		}
	}
}
?>
