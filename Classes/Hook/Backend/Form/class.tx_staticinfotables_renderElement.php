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
 * Custom rendering of some backend forms elements
 *
 */
class tx_staticinfotables_renderElement {

	/*
	 * Add ISO codes to the label of entities
	 */
	public function addIsoCodeToLabel ($PA, $fObj) {
		$PA['title'] = $PA['row'][$GLOBALS['TCA'][$PA['table']]['ctrl']['label']];
		if (TYPO3_MODE == 'BE') {
			switch ($PA['table']) {
				case 'static_territories':
					$isoCode = $PA['row']['tr_iso_nr']; 
					if (!$isoCode) {
						$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
							'uid,tr_iso_nr',
							$PA['table'],
							'uid = ' . $PA['row']['uid'] . t3lib_befunc::deleteClause($PA['table'])
						);
						$isoCode = $rows[0]['tr_iso_nr'];
					}
					if ($isoCode) {
						$PA['title'] = tx_staticinfotables_div::getTitleFromIsoCode($PA['table'], $isoCode) . ' (' . $isoCode . ')';
					}
					break;
				case 'static_countries':
					$isoCode = $PA['row']['cn_iso_2']; 
					if (!$isoCode) {
						$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
							'uid,cn_iso_2',
							$PA['table'],
							'uid = ' . $PA['row']['uid'] . t3lib_befunc::deleteClause($PA['table'])
						);
						$isoCode = $rows[0]['cn_iso_2'];
					}
					if ($isoCode) {
						$PA['title'] = tx_staticinfotables_div::getTitleFromIsoCode($PA['table'], $isoCode) . ' (' . $isoCode . ')';
					}
					break;
				default:
					break;
			}
		}
	}

	/*
	 * Sort the territories selector using the current locale
	 */
	public function sortTerritoriesSelector ($PA, $fObj) {
		switch ($PA['table']) {
			case 'static_territories':
				// Avoid circular relation
				$row = $PA['row'];
				foreach ($PA['items'] as $index => $item) {
					if ($item[1] == $row['uid']) {
						unset($PA['items'][$index]);
					}
				}
			case 'static_countries':
				asort($PA['items']);
				break;
		}
	}
}
?>
