<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005-2006 René Fritz (r.fritz@colorcube.de)
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
 * Class for updating the db
 *
 * @author	 René Fritz <r.fritz@colorcube.de>
 */
class ext_update  {

	/**
	 * Main function, returning the HTML content of the module
	 *
	 * @return	string		HTML
	 */
	function main()	{

		require_once ('class.tx_staticinfotables_encoding.php');

		$tables = array ('static_countries', 'static_country_zones', 'static_languages', 'static_currencies');

		$content = '';

		$content.= '<br />Convert character encoding of the static info tables.';
		$content.= '<br />The default encoding is UTF-8.';

		if(t3lib_div::_GP('convert') AND $destEncoding = t3lib_div::_GP('dest_encoding')) {
			foreach ($tables as $table) {
				$content .= '<p>'.htmlspecialchars($table.' > '.$destEncoding).'</p>';
				tx_staticinfotables_encoding::convertEncodingTable($table, 'utf-8', $destEncoding);
			}
			$content .= '<p>Done</p>';

		} else {

			$content .= '</form>';
			$content .= '<form action="'.htmlspecialchars(t3lib_div::linkThisScript()).'" method="post">';
			$content .= '<br /><br />';
			$content .= 'This conversion works only once. When you converted the tables and you want to do it again to another encoding you have to reinstall the tables with the Extension Manager.';
			$content .= '<br /><br />';
            $content .= 'Destination character encoding:';
            $content .= '<br />'.tx_staticinfotables_encoding::getEncodingSelect('dest_encoding', '', '', 'utf-8');
			$content .= '<br /><br />';
			$content .= '<input type="submit" name="convert" value="Convert" />';
			$content .= '</form>';
		}

		return $content;
	}


	function access() {
		return true;
	}


}

// Include extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/static_info_tables/class.ext_update.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/static_info_tables/class.ext_update.php']);
}


?>
