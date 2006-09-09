<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004-2006 Stanislas Rolland <stanislas.rolland(arobas)fructifor.ca>
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
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 *
 * Class for handling static info tables: countries, and subdivisions, currencies, languages and taxes
 *
 * @author	Stanislas Rolland <stanislas.rolland(arobas)fructifor.ca>
 */
 
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   64: class tx_srstaticinfo_pi1 extends tslib_pibase
 *   82:     function init()
 *  114:     function getStaticInfoName($type='COUNTRIES', $code, $country='', $countrySubdivision='')
 *  200:     function buildStaticInfoSelector($type='COUNTRIES', $name='', $class='', $selected='', $country='', $submit=0)
 *  261:     function initCountries()
 *  285:     function initCountrySubdivisions($country)
 *  308:     function initCurrencies()
 *  331:     function initLanguages()
 *  354:     function optionsConstructor($names, $selected='')
 *  375:     function loadCurrencyInfo($currencyCode)
 *  414:     function formatAmount($amount, $displayCurrencyCode='')
 *  438:     function formatAddress($delim, $streetAddress, $city, $zip, $subdivisionCode='', $countryCode='')
 *  481:     function applyConsumerTaxes($amount, $taxClass=0, $shopCountryCode, $shopCountrySubdivisionCode, $buyerCountryCode, $buyerCountrySubdivisionCode, $EUThreshold=0)
 *  592:     function enableFields($table,$show_hidden=0)
 *
 * TOTAL FUNCTIONS: 13
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('static_info_tables').'class.tx_staticinfotables_div.php');

class tx_staticinfotables_pi1 extends tslib_pibase {

	var $cObj;		// The backReference to the mother cObj object set at call time
	var $prefixId = 'tx_staticinfotables_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_staticinfotables_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey = 'static_info_tables';		 // The extension key.
	var $conf = array();
	var $currency;		// default currency
	var $currencyInfo = array();
	var $defaultCountry;
	var $defaultCountryZone;
	var $defaultLanguage;
	var $types = array('TERRITORIES', 'COUNTRIES', 'SUBDIVISIONS', 'CURRENCIES', 'LANGUAGES', 'TAXES', 'SUBTAXES');
	var $tables = array(
		'TERRITORIES' 	=> 'static_territories',
		'COUNTRIES' 	=> 'static_countries',
		'SUBDIVISIONS' 	=> 'static_country_zones',
		'CURRENCIES' 	=> 'static_currencies',
		'LANGUAGES' 	=> 'static_languages',
		'TAXES' 	=> 'static_taxes',
		'SUBTAXES' 	=> 'static_taxes'
		);
	
	/**
	 * Initializing the class: sets the language based on the TS configuration language property
	 *
	 * @return	boolean		Always returns true
	 */
	function init()	{
		global $TSFE;
		
		$this->tslib_pibase();
		$this->conf = $TSFE->tmpl->setup['plugin.'][$this->prefixId.'.'];
		
			//Get the default currency and make sure it does exist in table static_currencies
		$this->currency = (trim($this->conf['currencyCode'])) ? trim($this->conf['currencyCode']) : 'EUR';
			//If not set, we use the Euro
		if (!$this->getStaticInfoName('CURRENCIES', $this->currency)) {
			$this->currency = 'EUR';
		}
		$this->currencyInfo = $this->loadCurrencyInfo($this->currency);
		
		$this->defaultCountry = trim($this->conf['countryCode']);
		if (!$this->getStaticInfoName('COUNTRIES', $this->defaultCountry)) {
			$this->defaultCountry = '';
		}
		$this->defaultCountryZone = trim($this->conf['countryZoneCode']);
		if (!$this->getStaticInfoName('SUBDIVISIONS', $this->defaultCountryZone, $this->defaultCountry)) {
			$this->defaultCountryZone = '';
		}
		$this->defaultLanguage = trim($this->conf['languageCode']);
		if (!$this->getStaticInfoName('LANGUAGES', $this->defaultLanguage)) {
			$this->defaultLanguage = '';
		}
		return true;
	}

	/**
	 * Getting the name of a country, country subdivision, currency, language, tax
	 *
	 * @param	string		Defines the type of entry of the requested name: 'TERRIRORIES', 'COUNTRIES', 'SUBDIVISIONS', 'CURRENCIES', 'LANGUAGES', 'TAXES', 'SUBTAXES'
	 * @param	string		The ISO alpha-3 code of a territory, country or currency, or the ISO alpha-2 code of a language or the code of a country subdivision
	 * @param	string		The value of the country code (cn_iso_3) for which a name of type 'SUBDIVISIONS', 'TAXES' or 'SUBTAXES' is requested (meaningful only in these cases)
	 * @param	string		The value of the country subdivision code for which a name of type 'SUB_TAXES' is requested (meaningful only in this case)
	 * @return	string		The name of the object in the current language
	 */
	function getStaticInfoName($type='COUNTRIES', $code, $country='', $countrySubdivision='', $self=0) {
		global $TYPO3_DB, $TSFE, $TYPO3_CONF_VARS;
		
		if (in_array($type, $this->types) && trim($code)) {
			$table = $this->tables[$type];
			$lang = $this->getCurrentLanguage();
			$isoCode = array();
			$isoCode[] = trim($code);
			switch($type)	{
				case 'TERRITORIES':
				case 'COUNTRIES':
				case 'CURRENCIES':
					$name = tx_staticinfotables_div::getTitleFromIsoCode($table, $isoCode, $lang);
					break;
				case 'SUBDIVISIONS':
				case 'TAXES':
					$isoCode[] = trim($country) ? trim($country) : $this->defaultCountry;
					$name = tx_staticinfotables_div::getTitleFromIsoCode($table, $isoCode, $lang);
					break;
				case 'SUBTAXES':
					$isoCode[] = trim($country) ? trim($country) : $this->defaultCountry;
					$isoCode[] = trim($countrySubdivision) ? trim($countrySubdivision) : $this->defaultCountryZone;
					$name = tx_staticinfotables_div::getTitleFromIsoCode($table, $isoCode, $lang);
					break;
				case 'LANGUAGES':
					$isoCode = t3lib_div::trimExplode( '_', $code, 1);
					$name = tx_staticinfotables_div::getTitleFromIsoCode($table, $isoCode, $lang, $self);
					break;
			}
			return $name = $TSFE->csConv($name, $TYPO3_CONF_VARS['EXTCONF'][$this->extKey]['charset']);
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Buils a HTML drop-down selector of countries, country subdivisions, currencies or languages
	 *
	 * @param	string		Defines the type of entries to be presented in the drop-down selector: 'COUNTRIES', 'SUBDIVISIONS', 'CURRENCIES' or 'LANGUAGES'
	 * @param	string		A value for the name attribute of the <select> tag
	 * @param	string		A value for the class attribute of the <select> tag
	 * @param	string		The value of the code of the entry to be pre-selected in the drop-down selector: value of cn_iso_3, zn_code, cu_iso_3 or lg_iso_2
	 * @param	string		The value of the country code (cn_iso_3) for which a drop-down selector of type 'SUBDIVISIONS' is requested (meaningful only in this case)
	 * @param	boolean/string		If set to 1, an onchange attribute will be added to the <select> tag for immediate submit of the changed value; if set to other than 1, overrides the onchange script
	 * @param	string		A value for the id attribute of the <select> tag
	 * @param	string		A value for the title attribute of the <select> tag
	 * @return	string		A set of HTML <select> and <option> tags
	 */
	function buildStaticInfoSelector($type='COUNTRIES', $name='', $class='', $selected='', $country='', $submit=0, $id='', $title='')	{

		$nameAttribute = (trim($name)) ? 'name="'.htmlspecialchars(trim($name)).'" ' : '';
		$classAttribute = (trim($class)) ? 'class="'.htmlspecialchars(trim($class)).'" ' : '';
		$idAttribute = (trim($id)) ? 'id="'.htmlspecialchars(trim($id)).'" ' : '';
		$titleAttribute = (trim($title)) ? 'title="'.htmlspecialchars(trim($title)).'" ' : '';
		$onchangeAttribute = '';
		if ($submit) {
			if ($submit == 1) {
				$onchangeAttribute = 'onchange='.t3lib_div::quoteJSvalue(str_replace('\'', '"', $this->conf['onChangeAttribute']));
			} else {
				$onchangeAttribute = 'onchange='.t3lib_div::quoteJSvalue(str_replace('\'', '"', $submit));
			}
		}
		$selector = '<select size="1" '.$idAttribute.$nameAttribute.$titleAttribute.$classAttribute.$onchangeAttribute.'>'.chr(10);

		switch($type)	{
			case 'COUNTRIES':
				$names = $this->initCountries();
				$selected = (trim($selected)) ? trim($selected) : $this->defaultCountry;
				reset($names);
				$selected = ($selected) ? $selected : key($names);
				break;
			case 'SUBDIVISIONS':
				$country = (trim($country)) ? trim($country) : $this->defaultCountry;
				$names = $this->initCountrySubdivisions($country);
				$selected = trim($selected);
				if( $country == $this->defaultCountry ) {
					$selected = ($selected) ? $selected : $this->defaultCountryZone;
				} else {
					reset($names);
					$selected = ($selected) ? $selected : key($names);
				}
				break;
			case 'CURRENCIES':
				$names = $this->initCurrencies();
				$selected = (trim($selected)) ? trim($selected) : $this->currency;
				reset($names);
				$selected = ($selected) ? $selected : key($names);
				break;
			case 'LANGUAGES':
				$names = $this->initLanguages();
				$selected = (trim($selected)) ? trim($selected) : $this->defaultLanguage;
				reset($names);
				$selected = ($selected) ? $selected : key($names);
				break;
		}
		if( count($names) > 0 )	{
			$selector .= $this->optionsConstructor($names, $selected);
			$selector .= '</select>'.chr(10);
		} else {
			$selector = '';
		}
		return $selector;
	}

	/**
	 * Getting all countries into an array
	 * 	where the key is the ISO alpha-3 code of the country
	 * 	and where the value is the name of the country in the current language
	 *
	 * @param	string		Defines a selection: 'ALL', 'UN', 'EU'
	 * @return	array		An array of names of countries
	 */
	function initCountries($select='UN') {
		global $TYPO3_DB, $TSFE, $TYPO3_CONF_VARS;
		
		$table = $this->tables['COUNTRIES'];
		$lang = $this->getCurrentLanguage();
		$names = array();
		$titleFields = tx_staticinfotables_div::getTCAlabelField($table, TRUE, $lang);
		$prefixedTitleFields = array();
		foreach ($titleFields as $titleField) {
			$prefixedTitleFields[] = $table.'.'.$titleField;
		}
		$labelFields = implode(',', $prefixedTitleFields);
		$where = '1=1';
		if ($select == 'UN') {
			$where = 'cn_uno_member=1';
		} elseif ($select == 'EU') {
			$where = 'cn_eu_member=1';
		}
		$res = $TYPO3_DB->exec_SELECTquery(
			$table.'.cn_iso_3,'.$labelFields,
			$table,
			$where.$TSFE->sys_page->enableFields($table)
			);
		while ($row = $TYPO3_DB->sql_fetch_assoc($res))	{
			foreach ($titleFields as $titleField) {
				if ($row[$titleField]) {
					$names[$row['cn_iso_3']] = $TSFE->csConv($row[$titleField], $TYPO3_CONF_VARS['EXTCONF'][$this->extKey]['charset']);
					break;
				}
			}
		}
		uasort($names, 'strcoll');
		return $names;
	}

	/**
	 * Getting all country subdivisions of a given country into an array
	 * 	where the key is the code of the subdivision
	 * 	and where the value is the name of the country subdivision in the current language
	 *
	 * @param	string		The ISO alpha-3 code of a country
	 * @return	array		An array of names of country subdivisions
	 */
	function initCountrySubdivisions($country)	{
		global $TYPO3_DB, $TSFE, $TYPO3_CONF_VARS;
		
		$table = $this->tables['SUBDIVISIONS'];
		$lang = $this->getCurrentLanguage();
		$names = array();
		$titleFields = tx_staticinfotables_div::getTCAlabelField($table, TRUE, $lang);
		$prefixedTitleFields = array();
		foreach ($titleFields as $titleField) {
			$prefixedTitleFields[] = $table.'.'.$titleField;
		}
		$labelFields = implode(',', $prefixedTitleFields);
		$res = $TYPO3_DB->exec_SELECTquery(
			$table.'.zn_code,'.$labelFields,
			$table,
			'zn_country_iso_3='.$TYPO3_DB->fullQuoteStr($country,$table).
				$TSFE->sys_page->enableFields($table)
			);
		while ($row = $TYPO3_DB->sql_fetch_assoc($res))	{
			foreach ($titleFields as $titleField) {
				if ($row[$titleField]) {
					$names[$row['zn_code']] = $TSFE->csConv($row[$titleField], $TYPO3_CONF_VARS['EXTCONF'][$this->extKey]['charset']);
					break;
				}
			}
		}
		uasort($names, 'strcoll');
		return $names;
	}

	/**
	 * Getting all currencies into an array
	 * 	where the key is the ISO alpha-3 code of the currency
	 * 	and where the value are the name of the currency in the current language
	 *
	 * @return	array		An array of names of currencies
	 */
	function initCurrencies() {
		global $TYPO3_DB, $TSFE, $TYPO3_CONF_VARS;
		
		$table = $this->tables['CURRENCIES'];
		$lang = $this->getCurrentLanguage();
		$names = array();
		$titleFields = tx_staticinfotables_div::getTCAlabelField($table, TRUE, $lang);
		$prefixedTitleFields = array();
		foreach ($titleFields as $titleField) {
			$prefixedTitleFields[] = $table.'.'.$titleField;
		}
		$labelFields = implode(',', $prefixedTitleFields);
		$res = $TYPO3_DB->exec_SELECTquery(
			$table.'.cu_iso_3,'.$labelFields,
			$table,
			'1=1'.$TSFE->sys_page->enableFields($table)
			);
		while ($row = $TYPO3_DB->sql_fetch_assoc($res))	{
			foreach ($titleFields as $titleField) {
				if ($row[$titleField]) {
					$names[$row['cu_iso_3']] = $TSFE->csConv($row[$titleField], $TYPO3_CONF_VARS['EXTCONF'][$this->extKey]['charset']);
					break;
				}
			}
		}
		uasort($names, 'strcoll');
		return $names;
	}

	/**
	 * Getting all languages into an array
	 * 	where the key is the ISO alpha-2 code of the language
	 * 	and where the value are the name of the language in the current language
	 *	Note: we exclude sacred and constructed languages.
	 *
	 * @return	array		An array of names of languages
	 */
	function initLanguages() {
		global $TYPO3_DB, $TSFE, $TYPO3_CONF_VARS;
		
		$table = $this->tables['LANGUAGES'];
		$lang = $this->getCurrentLanguage();
		$names = array();
		$titleFields = tx_staticinfotables_div::getTCAlabelField($table, TRUE, $lang);
		$prefixedTitleFields = array();
		foreach ($titleFields as $titleField) {
			$prefixedTitleFields[] = $table.'.'.$titleField;
		}
		$labelFields = implode(',', $prefixedTitleFields);
		$res = $TYPO3_DB->exec_SELECTquery(
			$table.'.lg_iso_2,'.$table.'.lg_country_iso_2,'.$labelFields,
			$table,
			'lg_sacred = 0 and lg_constructed = 0'.
				$TSFE->sys_page->enableFields($table)
			);
		while ($row = $TYPO3_DB->sql_fetch_assoc($res))	{
			$code = $row['lg_iso_2'].($row['lg_country_iso_2']?'_'.$row['lg_country_iso_2']:'');
			foreach ($titleFields as $titleField) {
				if ($row[$titleField]) {
					$names[$code] = $TSFE->csConv($row[$titleField], $TYPO3_CONF_VARS['EXTCONF'][$this->extKey]['charset']);
					break;
				}
			}
		}
		uasort($names, 'strcoll');
		return $names;
	}

	/**
	 * Builds a list of <option> tags
	 *
	 * @param	array		An array where the values will be the texts of an <option> tags and keys will be the values of the tags
	 * @param	string		A pre-selected value: if the value appears as a key, the <option> tag will bear a 'selected' attribute
	 * @return	string		A string of HTML <option> tags
	 */
	function optionsConstructor($names, $selected='') {

		$options = '';
		reset($names);
		while(list($value,$name)=each($names))	{
			$options  .= '<option value="'.htmlspecialchars($value).'"';
			if( $selected == $value) {
				$options  .= ' selected="selected"';
			}
			$options  .= '>'.htmlspecialchars($name).'</option>'.chr(10);
		}

		return $options;
	}

	/**
	 * Loading currency display parameters from Static Info Tables
	 *
	 * @param	string		An ISO alpha-3 currency code
	 * @return	array		An array of information regarding the currrency
	 */
	function loadCurrencyInfo($currencyCode)	{
		global $TYPO3_DB;
		
			// Fetching the currency record
	 	$this->currencyInfo['cu_iso_3'] = trim($currencyCode);
	 	$this->currencyInfo['cu_iso_3'] = ($this->currencyInfo['cu_iso_3']) ? $this->currencyInfo['cu_iso_3'] : $this->currency;
		$res = $TYPO3_DB->exec_SELECTquery(
			'*',
			'static_currencies',
			'cu_iso_3='.$TYPO3_DB->fullQuoteStr($this->currencyInfo['cu_iso_3'],'static_currencies')
		);
			// If not found we fetch the default currency!
		if (!$TYPO3_DB->sql_num_rows($res)) {
		 	$this->currencyInfo['cu_iso_3'] = $this->currency;
			$res = $TYPO3_DB->exec_SELECTquery(
				'*',
				'static_currencies',
				'cu_iso_3='.$TYPO3_DB->fullQuoteStr($this->currencyInfo['cu_iso_3'],'static_currencies')
			);
		}
		$row = $TYPO3_DB->sql_fetch_assoc($res);

		$this->currencyInfo['cu_name'] = $this->getStaticInfoName('CURRENCIES', $this->currencyInfo['cu_iso_3']);
		$this->currencyInfo['cu_symbol_left'] = $row['cu_symbol_left'];
		$this->currencyInfo['cu_symbol_right'] = $row['cu_symbol_right'];
		$this->currencyInfo['cu_decimal_digits'] = $row['cu_decimal_digits'];
		$this->currencyInfo['cu_decimal_point'] = $row['cu_decimal_point'];
		$this->currencyInfo['cu_thousands_point'] = $row['cu_thousands_point'];

		return $this->currencyInfo;
	}

	/**
	 * Formatting an amount in the currency loaded by loadCurrencyInfo($currencyCode)
	 *
	 * 								 '' - the currency code is not displayed
	 * 								 'RIGHT' - the code is displayed at the right of the amount
	 * 								 'LEFT' - the code is displayed at the left of the amount
	 *
	 * @param	float		An amount to be displayed in the loaded currency
	 * @param	string		A flag specifying if the the currency code should be displayed:
	 * @return	string		The formated amounted
	 */
	function formatAmount($amount, $displayCurrencyCode='')	{

		$formatedAmount = '';

		if( $displayCurrencyCode == 'LEFT' ) { $formatedAmount .= $this->currencyInfo['cu_iso_3'].chr(32); }
		$formatedAmount .= $this->currencyInfo['cu_symbol_left'];
		$formatedAmount .= number_format($amount, intval($this->currencyInfo['cu_decimal_digits']), $this->currencyInfo['cu_decimal_point'], (($this->currencyInfo['cu_thousands_point'])?$this->currencyInfo['cu_thousands_point']:chr(32)));
		$formatedAmount .= (($this->currencyInfo['cu_symbol_right'])?chr(32):'').$this->currencyInfo['cu_symbol_right'];
		if( $displayCurrencyCode == 'RIGHT' ) { $formatedAmount .= chr(32).$this->currencyInfo['cu_iso_3']; }

		return $formatedAmount;
	}

	/**
	 * Formatting an address in the format specified
	 *
	 * @param	string		A street address
	 * @param	string		A city
	 * @param	string		A country subdivision code (zn_code)
	 * @param	string		A ISO alpha-3 country code (cn_iso_3)
	 * @param	string		A zip code
	 * @param	[type]		$countryCode: ...
	 * @return	string		The formated address using the country address format (cn_address_format)
	 */
	function formatAddress($delim, $streetAddress, $city, $zip, $subdivisionCode='', $countryCode='')	{
		global $TYPO3_DB;
		
		$formatedAddress = '';

			// Get country name
		$countryName = $this->getStaticInfoName('COUNTRIES', (($countryCode)?$countryCode:$this->defaultCountry));
		if (!$countryName) {
			return $formatedAddress;
		}

			// Get address format
		$res = $TYPO3_DB->exec_SELECTquery(
			'cn_address_format',
			'static_countries',
			'cn_iso_3='.$TYPO3_DB->fullQuoteStr(trim((($countryCode)?$countryCode:$this->defaultCountry)),'static_countries')
		);
		$row = $TYPO3_DB->sql_fetch_assoc($res);
		$addressFormat = $row['cn_address_format'];

			// Get country subdivision name
		$countrySubdivisionName = $this->getStaticInfoName('SUBDIVISIONS', (($subdivisionCode)?$subdivisionCode:$this->defaultCountryZone), (($countryCode)?$countryCode:$this->defaultCountry));

			// Format the address
		$formatedAddress = $this->conf['addressFormat.'][$addressFormat];
		$formatedAddress = str_replace('%street', $streetAddress, $formatedAddress);
		$formatedAddress = str_replace('%city', $city, $formatedAddress);
		$formatedAddress = str_replace('%zip', $zip, $formatedAddress);
		$formatedAddress = str_replace('%countrySubdivisionCode', $subdivisionCode, $formatedAddress);
		$formatedAddress = str_replace('%countrySubdivisionName', $countrySubdivisionName, $formatedAddress);
		$formatedAddress = str_replace('%countryName', strtoupper($countryName), $formatedAddress);
		$formatedAddress = implode($delim, t3lib_div::trimExplode(';', $formatedAddress, 1));

		return $formatedAddress;
	}

	/**
	 * Applying taxes to a given amount
	 *
	 * @param	float		An amount to which taxes should be applied
	 * @param	integer		The class of taxation of the product
	 * @param	string		The ISO alpha-3 code of the country of the selling shop
	 * @param	string		The country subdivision code of the region of the selling shop
	 * @param	string		The ISO alpha-3 code of the country of the buying consumer
	 * @param	string		The country subdivision code of the region of the buying consumer
	 * @param	boolean		Should be set if the shop has sales of goods beyond the regulatory threshold in the buyer's country (when both shop and buyer in EU)
	 * @return	array		An array of 4-plets of applied taxes: ('tx_name','tx_rate','tx_amount','tx_priority')
	 */
	function applyConsumerTaxes($amount, $taxClass=0, $shopCountryCode, $shopCountrySubdivisionCode, $buyerCountryCode, $buyerCountrySubdivisionCode, $EUThreshold=0)	{
		global $TYPO3_DB;
		
		$appliedTaxesIndex = 0;
		$appliedTaxes = array();
		$shopCountryCode = ($shopCountryCode) ? $shopCountryCode : $this->defaultCountry;
		$buyerCountryCode = ($buyerCountryCode) ? $buyerCountryCode : $this->defaultCountry;

	 		// Not taxable!
		if (!$taxClass || !trim($shopCountryCode) || !trim($buyerCountryCode)) {
			return $appliedTaxes;
		}

	 		// Get national taxes
		if (trim($shopCountryCode) == trim($buyerCountryCode)) {
			$res = $TYPO3_DB->exec_SELECTquery(
				'*',
				'static_taxes',
				'tx_country_iso_3='.$TYPO3_DB->fullQuoteStr(trim($shopCountryCode),'static_taxes').
					' AND tx_scope="1"'.
					' AND (tx_class='.$TYPO3_DB->fullQuoteStr($taxClass,'static_taxes').' OR tx_class="3")'.
					$this->enableFields('static_taxes')
			);
			while($row = $TYPO3_DB->sql_fetch_assoc($res))	{
				$appliedTaxes[$appliedTaxesIndex] = array();
				$appliedTaxes[$appliedTaxesIndex]['tx_name'] =  $this->getStaticInfoName('TAXES', $row['tx_code'], trim($shopCountryCode));
				$appliedTaxes[$appliedTaxesIndex]['tx_rate'] = doubleval($row['tx_rate']);
				$appliedTaxes[$appliedTaxesIndex]['tx_priority'] = $row['tx_priority'];
				$appliedTaxesIndex++;
			}

	 			// Get state or provincial taxes
			if( trim($shopCountrySubdivisionCode) && trim($buyerCountrySubdivisionCode) &&  trim($shopCountrySubdivisionCode) == trim($buyerCountrySubdivisionCode) ) {
				$res = $TYPO3_DB->exec_SELECTquery(
					'*',
					'static_taxes',
					'tx_country_iso_3='.$TYPO3_DB->fullQuoteStr(trim($shopCountryCode),'static_taxes').
						' AND tx_zn_code='.$TYPO3_DB->fullQuoteStr(trim($shopCountrySubdivisionCode),'static_taxes').
						' AND tx_scope="2"'.
						' AND (tx_class='.$TYPO3_DB->fullQuoteStr($taxClass,'static_taxes').' OR tx_class="3")'.
						$this->enableFields('static_taxes')
				);
				while($row = $TYPO3_DB->sql_fetch_assoc($res))	{
					$appliedTaxes[$appliedTaxesIndex] = array();
					$appliedTaxes[$appliedTaxesIndex]['tx_name'] =  $this->getStaticInfoName('SUBTAXES', $row['tx_code'], trim($shopCountryCode), trim($shopCountrySubdivisionCode));
					$appliedTaxes[$appliedTaxesIndex]['tx_rate'] = doubleval($row['tx_rate']);
					$appliedTaxes[$appliedTaxesIndex]['tx_priority'] = $row['tx_priority'];
					$appliedTaxesIndex++;
				}
			}
		} else	{
	 			// Apply EU Internal Market rules for under threshold sales
			$res = $TYPO3_DB->exec_SELECTquery(
				'cn_eu_member',
				'static_countries',
				'cn_iso_3='.$TYPO3_DB->fullQuoteStr(trim($shopCountryCode),'static_countries')
			);
			$row = $TYPO3_DB->sql_fetch_assoc($res);
			$shop_cn_eu_member = $row['cn_eu_member'];
			if ($shop_cn_eu_member) {
				$res = $TYPO3_DB->exec_SELECTquery(
					'cn_eu_member',
					'static_countries',
					'cn_iso_3='.$TYPO3_DB->fullQuoteStr(trim($buyerCountryCode),'static_countries')
				);
				$row = $TYPO3_DB->sql_fetch_assoc($res);
				$buyer_cn_eu_member = $row['cn_eu_member'];
				if ($buyer_cn_eu_member) {
							// Here we apply the rules of the European Union Internal Market
					$taxCountryCode = trim($shopCountryCode);
					if ($taxClass == '1' && $EUThreshold)	{
						$taxCountryCode = trim($buyerCountryCode);
					}
					$res = $TYPO3_DB->exec_SELECTquery(
						'*',
						'static_taxes',
						'tx_country_iso_3='.$TYPO3_DB->fullQuoteStr($taxCountryCode,'static_taxes').
							' AND tx_scope="1"'.
							' AND (tx_class='.$TYPO3_DB->fullQuoteStr($taxClass,'static_taxes').' OR tx_class="3")'.
							$this->enableFields('static_taxes')
					);
					while($row = $TYPO3_DB->sql_fetch_assoc($res))	{
						$appliedTaxes[$appliedTaxesIndex] = array();
						$appliedTaxes[$appliedTaxesIndex]['tx_name'] =  $this->getStaticInfoName('TAXES', $row['tx_code'], trim($shopCountryCode));
						$appliedTaxes[$appliedTaxesIndex]['tx_rate'] = doubleval($row['tx_rate']);
						$appliedTaxes[$appliedTaxesIndex]['tx_priority'] = $row['tx_priority'];
						$appliedTaxesIndex++;
					}
				}
			}
		}

	 		// Apply rates
		if( count($appliedTaxes) )	{
			foreach ($appliedTaxes as $key => $row) {
				$priority[$key] = $row['tx_priority'];
			}
			array_multisort($priority, SORT_ASC, $appliedTaxes);
			$priority = $priority['0'];
			$appliedTaxesAmount = $amount;
			$baseAmount = $appliedTaxesAmount;
			foreach ($appliedTaxes as $key => $row) {
				if( $row['tx_priority'] > $priority ) {
					$baseAmount = $appliedTaxesAmount;
					$priority = $row['tx_priority'];
				}
				$taxedAmount = $row['tx_rate']*$baseAmount;
				$appliedTaxes[$key]['tx_amount'] = round($taxedAmount, ceil(0 - log10($taxedAmount)) + $this->currencyInfo['cu_decimal_digits']);
				$appliedTaxesAmount += $appliedTaxes[$key]['tx_amount'];
			}
		}
		return $appliedTaxes;
	}
	
	/**
	 * Returns the current language as iso-2-alpha code
	 *
	 * @return	string		'DE', 'EN', 'DK', ...
	 */
	function getCurrentLanguage() {
		global $TSFE, $TYPO3_DB;
		
		$langCodeT3 = $TSFE->lang;
		$csConvObj = $TSFE->csConvObj;
		
		$res = $TYPO3_DB->exec_SELECTquery(
			'lg_iso_2,lg_country_iso_2',
			'static_languages',
			'lg_typo3='.$TYPO3_DB->fullQuoteStr($langCodeT3,'static_languages')
			);
		while ($row = $TYPO3_DB->sql_fetch_assoc($res)) {
			$lang = $row['lg_iso_2'].($row['lg_country_iso_2']?'_'.$row['lg_country_iso_2']:'');
		}
		
		return $lang ? $lang : $csConvObj->conv_case('utf-8',$langCodeT3,'toUpper');
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/static_info_tables/pi1/class.tx_staticinfotables_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/static_info_tables/pi1/class.tx_staticinfotables_pi1.php']);
}

?>