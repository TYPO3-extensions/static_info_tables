<?php
namespace SJBR\StaticInfoTables\ViewHelpers\Form;
/***************************************************************
*  Copyright notice
*
*  (c) 2014 Carsten Biebricher <carsten.biebricher@hdnet.de>
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
 * StaticInfoTables SelectViewHelper
 *
 * @category   Extension
 * @package    SJBR
 * @subpackage ViewHelpers\Form
 * @author     Carsten Biebricher <carsten.biebricher@hdnet.de>
 */

use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * Display the Values of the selected StaticInfoTable.
 *
 * Default usage:
 * <code>
 * <s:form.select name="staticInfoTablesTestCountry" staticInfoTable="country" options="{}"/>
 * <s:form.select name="staticInfoTablesTestLanguage" staticInfoTable="language" options="{}"/>
 * <s:form.select name="staticInfoTablesTestTerritory" staticInfoTable="territory" options="{}"/>
 * <s:form.select name="staticInfoTablesTestCurrency" staticInfoTable="currency" options="{}"/>
 * <s:form.select name="staticInfoTablesTestCountryZones" staticInfoTable="countryZone" options="{}"/>
 * </code>
 *
 * Optional Usage:
 * <code>
 * <s:form.select name="staticInfoTablesTestCountry" id="staticInfoTablesTestCountry" staticInfoTable="country" options="{}" optionLabelField="isoCodeA2"/>
 * <s:form.select name="staticInfoTablesTestCountry" id="staticInfoTablesTestCountry" staticInfoTable="country" options="{}" optionLabelField="capitalCity"/>
 * </code>
 *
 * Subselect Usage: (only CountryZones of Germany)
 * <s:form.select name="staticInfoTablesTestCountryZones" id="staticInfoTablesTestCountryZones" staticInfoTable="countryZone" options="{}" staticInfoTableSubselect="{country: 54}"/>
 *
 * if you specify the Label-Field for the table use the Variable-Name from the StaticInfoTable-Model. (@see \SJBR\StaticInfoTables\Domain\Model\Country, ...)
 *
 * use name or property!
 *
 * Available Tables:
 * country
 * language
 * territory
 * currency
 * countryZone
 *
 * Available Slots:
 * getItems
 * getItemsWithSubselect
 *
 */
class SelectViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Form\SelectViewHelper {

	/**
	 * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
	 * @inject
	 */
	protected $signalSlotDispatcher;

	/**
	 * Country repository
	 *
	 * @var \SJBR\StaticInfoTables\Domain\Repository\CountryRepository
	 * @inject
	 */
	protected $countryRepository;

	/**
	 * Language repository
	 *
	 * @var \SJBR\StaticInfoTables\Domain\Repository\LanguageRepository
	 * @inject
	 */
	protected $languageRepository;

	/**
	 * Territory repository
	 *
	 * @var \SJBR\StaticInfoTables\Domain\Repository\TerritoryRepository
	 * @inject
	 */
	protected $territoryRepository;

	/**
	 * Currency repository
	 *
	 * @var \SJBR\StaticInfoTables\Domain\Repository\CurrencyRepository
	 * @inject
	 */
	protected $currencyRepository;

	/**
	 * Country Zone repository
	 *
	 * @var \SJBR\StaticInfoTables\Domain\Repository\CountryZoneRepository
	 * @inject
	 */
	protected $countryZoneRepository;

	/**
	 * Initialize arguments.
	 *
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('staticInfoTable', 'string', 'set the tablename of the StaticInfoTable to build the Select-Tag.');
		$this->registerArgument('staticInfoTableSubselect', 'array', '{fieldname: fieldvalue}');
		$this->registerArgument('defaultOptionLabel', 'string', 'if set, add default option with given label');
		$this->registerArgument('defaultOptionValue', 'string', 'if set, add default option with given label');
	}

	/**
	 * Render the Options.
	 *
	 * @throws Exception
	 * @return string
	 * @api
	 */
	public function getOptions() {
		if (!$this->hasArgument('staticInfoTable') || $this->arguments['staticInfoTable'] == '') {
			throw new \Exception('Please configure the "staticInfoTable"-Argument for this ViewHelper.', 1378136534);
		}

		/** @var \SJBR\StaticInfoTables\Domain\Repository\AbstractEntityRepository $repository */
		$repository = $this->arguments['staticInfoTable'] . 'Repository';

		if (!in_array($repository, get_object_vars($this))) {
			throw new \Exception('Please configure the right table in the "staticInfoTable"-Argument for this ViewHelper.', 1378136533);
		}

		/** @var array $items */
		$items = $this->emitGetItems($repository);

		/** @var string $valueFunction */
		$valueFunction = $this->getMethodnameFromArgumentsAndUnset('optionValueField', 'uid');

		/** @var string $labelFunction */
		$labelFunction = $this->getMethodnameFromArgumentsAndUnset('optionLabelField', 'nameLocalized');

		if (!$this->hasArgument('sortByOptionLabel') || $this->arguments['sortByOptionLabel'] == '') {
			$this->arguments['sortByOptionLabel'] = TRUE;
		}

		/** @var bool $test Test only the first item if they have the needed functions */
		$test = TRUE;
		$options = array();
		/** @var \SJBR\StaticInfoTables\Domain\Model\AbstractEntity $item */
		foreach ($items as $item) {
			if ($test && !method_exists($item, $valueFunction)) {
				throw new \Exception('Wrong optionValueField.', 1378136535);
			}

			if ($test && !method_exists($item, $labelFunction)) {
				throw new \Exception('Wrong optionLabelField.', 1378136536);

			}
			$test = FALSE;

			$value = $item->{$valueFunction}();
			$label = $item->{$labelFunction}();
			if ($value != '' && $label != '') {
				$options[$value] = $label;
			}
		}
		$this->arguments['options'] = $options;

		$sortedOptions = parent::getOptions();
		// Put default option after sorting to get it to the top of the items
		if ($this->hasArgument('defaultOptionLabel')) {
			$defaultOptionLabel = $this->arguments['defaultOptionLabel'];
			$defaultOptionValue = $this->hasArgument('defaultOptionValue') ? $this->arguments['defaultOptionValue'] : 0;
			$sortedOptions = array($defaultOptionValue => $defaultOptionLabel) + $sortedOptions;
		}
		return $sortedOptions;
	}

	/**
	 * Get Items and emit a signal to the dispatcher.
	 * Signal: getItems
	 *
	 * @param string $repository
	 *
	 * @return array
	 */
	protected function emitGetItems($repository) {
		/** @var array $items */
		if ($this->hasArgument('staticInfoTableSubselect')) {
			$items = $this->emitGetItemsWithSubselect($repository);
		} else if ($repository === 'languageRepository') {
			$items = $this->{$repository}->findAllNonConstructedNonSacred()
				->toArray();
		} else {
			$items = $this->{$repository}->findAll()
				->toArray();
		}

		$list = $this->signalSlotDispatcher->dispatch(__CLASS__, 'getItems', array(
			'arguments' => $this->arguments,
			'items'     => $items
		));
		if ($list !== NULL) {
			$this->arguments = $list['arguments'];
			$items = $list['items'];
		}

		return $items;
	}

	/**
	 * Get items with custom sub select.
	 * Signal: getItemsWithSubselect
	 *
	 * @param string $repository
	 *
	 * @return array
	 */
	protected function emitGetItemsWithSubselect($repository) {
		/** @var \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array $items */
		$items = array();
		$subselects = $this->arguments['staticInfoTableSubselect'];
		foreach ($subselects as $fieldname => $fieldvalue) {
			// default implemented Subselect
			if (strtolower($fieldname) === 'country' && MathUtility::canBeInterpretedAsInteger($fieldvalue)) {
				$findby = 'findBy' . ucfirst($fieldname);
				$fieldvalue = $this->countryRepository->findByUid((int)$fieldvalue);

				$items = call_user_func_array(array(
					$this->{$repository},
					$findby
				), array($fieldvalue));
			}

			/** @var array $list */
			$list = $this->signalSlotDispatcher->dispatch(__CLASS__, 'getItemsWithSubselect', array(
				'arguments'  => $this->arguments,
				'items'      => $items,
				'fieldname'  => $fieldname,
				'fieldvalue' => $fieldvalue
			));

			$this->arguments = $list['arguments'];
			if ($list['items']) {
				$items = $list['items']->toArray();
			}
		}

		return $items;
	}

	/**
	 * Return the in the arguments defined field, prepend 'get' and return it.
	 * If the field is in the arguments not set it return the in the default defined value.
	 *
	 * @param string $field   fieldname like 'optionLabelField'
	 * @param string $default default value like 'nameLocalized'
	 *
	 * @return string
	 */
	protected function getMethodnameFromArgumentsAndUnset($field, $default) {
		if (!$this->hasArgument($field) || $this->arguments[$field] == '') {
			$this->arguments[$field] = $default;
		}

		$methodName = 'get' . ucfirst($this->arguments[$field]);
		unset($this->arguments[$field]);

		return $methodName;
	}
}