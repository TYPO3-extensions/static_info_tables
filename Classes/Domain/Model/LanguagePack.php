<?php
namespace SJBR\StaticInfoTables\Domain\Model;

/*
 *  Copyright notice
 *
 *  (c) 2013-2020 Stanislas Rolland <typo32020(arobas)sjbr.ca>
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
 */

use SJBR\StaticInfoTables\Domain\Repository\CountryRepository;
use SJBR\StaticInfoTables\Domain\Repository\CountryZoneRepository;
use SJBR\StaticInfoTables\Domain\Repository\CurrencyRepository;
use SJBR\StaticInfoTables\Domain\Repository\LanguageRepository;
use SJBR\StaticInfoTables\Domain\Repository\TerritoryRepository;
use TYPO3\CMS\Core\Localization\Parser\XliffParser;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Annotation\Validate;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

/**
 * Language Pack object
 */
class LanguagePack extends AbstractEntity
{
    /**
     * Name of the extension this class belongs to
     *
     * @var string
     */
    protected $extensionName = 'StaticInfoTables';

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var string
     * @Validate("TYPO3\CMS\Extbase\Validation\Validator\NotEmptyValidator")
     */
    protected $author;

    /**
     * @var string
     */
    protected $authorCompany;

    /**
     * @var string
     * @Validate("EmailAddress")
     */
    protected $authorEmail;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $language;

    /**
     * @var string
     */
    protected $typo3VersionRange;

    /**
     * @var string
     * @Validate("TYPO3\CMS\Extbase\Validation\Validator\NotEmptyValidator")
     */
    protected $version;

    /**
     * Injects the object manager
     *
     * @param ObjectManagerInterface $objectManager
     * @return void
     */
    public function injectObjectManager(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @var CountryRepository
     */
    protected $countryRepository;

    /**
     * Dependency injection of the Country Repository
     *
     * @param CountryRepository $countryRepository
     * @return void
     */
    public function injectCountryRepository(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    /**
     * @var CountryZoneRepository
     */
    protected $countryZoneRepository;

    /**
     * Dependency injection of the Country Zone Repository
     *
     * @param CountryZoneRepository $countryZoneRepository
     * @return void
     */
    public function injectCountryZoneRepository(CountryZoneRepository $countryZoneRepository)
    {
        $this->countryZoneRepository = $countryZoneRepository;
    }

    /**
     * @var CurrencyRepository
     */
    protected $currencyRepository;

    /**
     * Dependency injection of the Currency Repository
     *
     * @param CurrencyRepository $currencyRepository
     * @return void
     */
    public function injectCurrencyRepository(CurrencyRepository $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * @var LanguageRepository
     */
    protected $languageRepository;

    /**
     * Dependency injection of the Language Repository
     *
     * @param LanguageRepository $languageRepository
     * @return void
     */
    public function injectLanguageRepository(LanguageRepository $languageRepository)
    {
        $this->languageRepository = $languageRepository;
    }

    /**
     * @var TerritoryRepository
     */
    protected $territoryRepository;

    /**
     * Dependency injection of the Territory Repository
     *
     * @param TerritoryRepository $territoryRepository
     * @return void
     */
    public function injectTerritoryRepository(TerritoryRepository $territoryRepository)
    {
        $this->territoryRepository = $territoryRepository;
    }

    public function __construct(
            $author = '',
            $authorCompany = '',
            $authorEmail = '',
            $locale = '',
            $language = ''
        ) {
        $this->setAuthor($author);
        $this->setAuthorCompany($authorCompany);
        $this->setAuthorEmail($authorEmail);
        $this->setLocale($locale);
        $this->setLanguage($language);
    }

    public function setAuthor($author)
    {
        $this->author = $author;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setAuthorCompany($authorCompany)
    {
        $this->authorCompany = $authorCompany;
    }

    public function getAuthorCompany()
    {
        return $this->authorCompany;
    }

    public function setAuthorEmail($authorEmail)
    {
        $this->authorEmail = $authorEmail;
    }

    public function getAuthorEmail()
    {
        return $this->authorEmail;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setLanguage($language)
    {
        $this->language = $language;
    }

    public function getLanguage()
    {
        return $this->language;
    }

    public function setTypo3VersionRange($typo3VersionRange)
    {
        $this->typo3VersionRange = $typo3VersionRange;
    }

    public function getTypo3VersionRange()
    {
        return $this->typo3VersionRange;
    }

    public function setVersion($version)
    {
        $this->version = $version;
    }

    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Gets the localization labels for this language pack
     *
     * @return string localization labels in xliff format
     */
    public function getLocalizationLabels()
    {
        // Build the localization labels of the language pack
        $XliffParser = $this->objectManager->get(XliffParser::class);
        $extensionKey = GeneralUtility::camelCaseToLowerCaseUnderscored($this->extensionName);
        $extensionPath = ExtensionManagementUtility::extPath($extensionKey);
        $sourceXliffFilePath = $extensionPath . 'Resources/Private/Language/locallang_db.xlf';
        $parsedData = $XliffParser->getParsedData($sourceXliffFilePath, 'default');
        $localizationLabels = [];
        $localeLowerCase = strtolower($this->getLocale());
        $localeUpperCase = strtoupper($this->getLocale());
        foreach ($parsedData['default'] as $translationElementId => $translationElement) {
            if (substr($translationElementId, -3) == '_en') {
                $localizationLabels[] = chr(9) . chr(9) . chr(9) . '<trans-unit id="' . substr($translationElementId, 0, -2) . $localeLowerCase . '" xml:space="preserve">';
                $localizationLabels[] = chr(9) . chr(9) . chr(9) . chr(9) . '<source>' . str_replace('(EN)', '(' . $localeUpperCase . ')', $translationElement[0]['source']) . '</source>';
                if ($translationElement[0]['target']) {
                    $localizationLabels[] = chr(9) . chr(9) . chr(9) . chr(9) . '<target>' . str_replace('(EN)', '(' . $localeUpperCase . ')', $translationElement[0]['target']) . '</target>';
                }
                $localizationLabels[] = chr(9) . chr(9) . chr(9) . '</trans-unit>';
            }
        }
        return implode(LF, $localizationLabels);
    }

    /**
     * Gets the update queries for this language pack
     *
     * @return string update queries in sql format
     */
    public function getUpdateQueries()
    {
        $updateQueries = [];
        $locale = $this->getLocale();
        $updateQueries = array_merge($updateQueries, $this->countryRepository->getUpdateQueries($locale));
        $updateQueries = array_merge($updateQueries, $this->countryZoneRepository->getUpdateQueries($locale));
        $updateQueries = array_merge($updateQueries, $this->currencyRepository->getUpdateQueries($locale));
        $updateQueries = array_merge($updateQueries, $this->languageRepository->getUpdateQueries($locale));
        $updateQueries = array_merge($updateQueries, $this->territoryRepository->getUpdateQueries($locale));
        return implode(LF, $updateQueries);
    }
}
