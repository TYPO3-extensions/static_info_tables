<?php
namespace SJBR\StaticInfoTables\Configuration\TypoScript;
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Stanislas Rolland <typo3(arobas)sjbr.ca>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
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
 * Class providing TypoScript configuration help for Static Info Tables
 *
 */
class ConfigurationHelper {

	/**
	 * Renders a select element to select an entity
	 *
	 * @param array $params: Field information to be rendered
	 * @param \TYPO3\CMS\Core\TypoScript\ExtendedTemplateService $pObj: The calling parent object.
	 * @return string The HTML input field
	 */
	public function buildEntitySelector(array $params, \TYPO3\CMS\Core\TypoScript\ExtendedTemplateService $pObj, $arg = '') {
		$field = '';
		$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		switch ($params['fieldName']) {
			case 'data[plugin.tx_staticinfotables_pi1.countryCode]':
			case 'data[plugin.tx_staticinfotables_pi1.countriesAllowed]':
				$repository = $objectManager->get('SJBR\\StaticInfoTables\\Domain\\Repository\\CountryRepository');
				break;
			case 'data[plugin.tx_staticinfotables_pi1.countryZoneCode]':
				$repository = $objectManager->get('SJBR\\StaticInfoTables\\Domain\\Repository\\CountryZoneRepository');
				break;
			case 'data[plugin.tx_staticinfotables_pi1.currencyCode]':
				$repository = $objectManager->get('SJBR\\StaticInfoTables\\Domain\\Repository\\CurrencyRepository');
				break;
			case 'data[plugin.tx_staticinfotables_pi1.languageCode]':
				$repository = $objectManager->get('SJBR\\StaticInfoTables\\Domain\\Repository\\LanguageRepository');
				break;
		}
		if (is_object($repository)) {
			$entities = $repository->findAllOrderedByLocalizedName();
			$options = array();
			foreach ($entities as $entity) {
				switch ($params['fieldName']) {
					case 'data[plugin.tx_staticinfotables_pi1.countryZoneCode]':
						$options[] = array('name' => $entity->getNameLocalized(), 'value' => $entity->getIsoCode());
						break;
					case 'data[plugin.tx_staticinfotables_pi1.countryCode]':
					case 'data[plugin.tx_staticinfotables_pi1.countriesAllowed]':
					case 'data[plugin.tx_staticinfotables_pi1.currencyCode]':
						$options[] = array('name' => $entity->getNameLocalized(), 'value' => $entity->getIsoCodeA3());
						break;
					case 'data[plugin.tx_staticinfotables_pi1.languageCode]':
						$countryCode = $entity->getCountryIsoCodeA2();
						$options[] = array('name' => $entity->getNameLocalized(), 'value' => $entity->getIsoCodeA2() . ($countryCode ? '_' . $countryCode : ''));
						break;
				}
			}
			$outSelected = array();
			$size = $params['fieldName'] == 'data[plugin.tx_staticinfotables_pi1.countriesAllowed]' ? 5 : 1;
			$field = \SJBR\StaticInfoTables\Utility\HtmlElementUtility::selectConstructor($options, array($params['fieldValue']), $outSelected, $params['fieldName'], '', '', '', '', $size);
		}
		return $field;
	}
}
?>