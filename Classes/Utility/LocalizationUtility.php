<?php
namespace SJBR\StaticInfoTables\Utility;

/*
 *  Copyright notice
 *
 *  (c) 2009 Sebastian Kurfürst <sebastian@typo3.org>
 *  (c) 2013-2019 Stanislas Rolland <typo3@sjbr.ca>
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
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 */

use Psr\Http\Message\ServerRequestInterface;
use SJBR\StaticInfoTables\Domain\Model\Language;
use SJBR\StaticInfoTables\Domain\Repository\LanguageRepository;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Localization\Locales;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Localization helper which should be used to fetch localized labels for static info entities.
 */
class LocalizationUtility
{
    /**
     * Key of the language to use
     *
     * @var string
     */
    protected static $languageKey = 'default';

    /**
     * Pointer to alternative fall-back language to use
     *
     * @var array
     */
    protected static $alternativeLanguageKeys = [];

    /**
     * Collating locale for the language in use
     *
     * @var string
     */
    protected static $collatingLocale = '';

    /**
     * Returns the localized label for a static info entity
     *
     * @param array $identifiers An array with key 1- 'uid' containing a uid and/or 2- 'iso' containing one or two iso codes (i.e. country zone code and country code, or language code and country code)
     * @param string $tableName The name of the table
     * @param bool local name only - if set local labels are returned
     * @param mixed $local
     *
     * @return string The value from the label field of the table
     */
    public static function translate($identifiers, $tableName, $local = false)
    {
        $value = '';
        self::setLanguageKeys();
        $isoLanguage = self::getIsoLanguageKey(self::$languageKey);
        $value = self::getLabelFieldValue($identifiers, $tableName, $isoLanguage, $local);
        return $value;
    }

    /**
     * Get the localized value for the label field
     *
     * @param array $identifiers An array with key 1- 'uid' containing a uid and/or 2- 'iso' containing one or two iso codes (i.e. country zone code and country code, or language code and country code)
     * @param string $tableName The name of the table
     * @param string language ISO code
     * @param bool local name only - if set local labels are returned
     * @param mixed $language
     * @param mixed $local
     *
     * @return string the value for the label field
     */
    public static function getLabelFieldValue($identifiers, $tableName, $language, $local = false)
    {
        $value = '';
        $labelFields = self::getLabelFields($tableName, $language, $local);
        if (count($labelFields)) {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($tableName);
            $queryBuilder->from($tableName)->select('uid');
            foreach ($labelFields as $labelField) {
                $queryBuilder->addSelect($labelField);
            }
            $whereCount = 0;
            if ($identifiers['uid']) {
                $queryBuilder->where(
                    $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter((int)$identifiers['uid']), \PDO::PARAM_INT)
                );
                $whereCount++;
            } elseif (!empty($identifiers['iso'])) {
                $isoCode = is_array($identifiers['iso']) ? $identifiers['iso'] : [$identifiers['iso']];
                foreach ($isoCode as $index => $code) {
                    if ($code) {
                        $field = self::getIsoCodeField($tableName, $code, $index);
                        if ($field) {
                            if ($whereCount) {
                                $queryBuilder->andWhere(
                                    $queryBuilder->expr()->eq($field, $queryBuilder->createNamedParameter($code))
                                );
                                $whereCount++;
                            } else {
                                $queryBuilder->where(
                                    $queryBuilder->expr()->eq($field, $queryBuilder->createNamedParameter($code))
                                );
                                $whereCount++;
                            }
                        }
                    }
                }
            }
            // Get the entity
            if ($whereCount) {
                $row = $queryBuilder->execute()->fetch();
                if ($row) {
                    foreach ($labelFields as $labelField) {
                        if ($row[$labelField]) {
                            $value = $row[$labelField];
                            break;
                        }
                    }
                }
            }
        }
        return $value;
    }

    /**
     * Returns the label fields for a given language
     *
     * @param string table name
     * @param string ISO language code to be used
     * @param bool If set, we are looking for the "local" title field
     * @param mixed $tableName
     * @param mixed $lang
     * @param mixed $local
     *
     * @return array field names
     */
    public static function getLabelFields($tableName, $lang, $local = false)
    {
        $labelFields = [];
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['static_info_tables']['tables'][$tableName]['label_fields'])) {
            $alternativeLanguages = [];
            if (count(self::$alternativeLanguageKeys)) {
                $alternativeLanguages = array_reverse(self::$alternativeLanguageKeys);
            }
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['static_info_tables']['tables'][$tableName]['label_fields'] as $field) {
                if ($local) {
                    $labelField = str_replace('##', 'local', $field);
                } else {
                    $labelField = str_replace('##', strtolower($lang), $field);
                }
                // Make sure the resulting field name exists in the table
                if (is_array($GLOBALS['TCA'][$tableName]['columns'][$labelField])) {
                    $labelFields[] = $labelField;
                }
                // Add fields for alternative languages
                if (strpos($field, '##') !== false && count($alternativeLanguages)) {
                    foreach ($alternativeLanguages as $language) {
                        $labelField = str_replace('##', strtolower($language), $field);
                        // Make sure the resulting field name exists in the table
                        if (is_array($GLOBALS['TCA'][$tableName]['columns'][$labelField])) {
                            $labelFields[] = $labelField;
                        }
                    }
                }
            }
        }
        return $labelFields;
    }

    /**
     * Returns a iso code field for the passed table name, iso code and index
     *
     * @param string table name
     * @param string iso code
     * @param int index in the table's isocode_field configuration array
     * @param mixed $table
     * @param mixed $isoCode
     * @param mixed $index
     *
     * @return string field name
     */
    public static function getIsoCodeField($table, $isoCode, $index = 0)
    {
        $isoCodeField = '';
        $isoCodeFieldTemplate = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['static_info_tables']['tables'][$table]['isocode_field'][$index];
        if ($isoCode && $table && $isoCodeFieldTemplate) {
            $field = str_replace('##', self::isoCodeType($isoCode), $isoCodeFieldTemplate);
            if (is_array($GLOBALS['TCA'][$table]['columns'][$field])) {
                $isoCodeField = $field;
            }
        }
        return $isoCodeField;
    }

    /**
     * Returns the type of an iso code: nr, 2, 3
     *
     * @param string iso code
     * @param mixed $isoCode
     *
     * @return string iso code type
     */
    protected static function isoCodeType($isoCode)
    {
        $type = '';
        $isoCodeAsInteger = MathUtility::canBeInterpretedAsInteger($isoCode);
        if ($isoCodeAsInteger) {
            $type = 'nr';
        } elseif (strlen($isoCode) == 2) {
            $type = '2';
        } elseif (strlen($isoCode) == 3) {
            $type = '3';
        }
        return $type;
    }

    /**
     * Get the ISO language key corresponding to a TYPO3 language key
     *
     * @param string $key The TYPO3 language key
     *
     * @return string the ISO language key
     */
    public static function getIsoLanguageKey($key)
    {
        return $key === 'default' ? 'EN' : $key;
    }

    /**
     * Get the current TYPO3 language
     *
     * @return string the TYP3 language key
     */
    public static function getCurrentLanguage()
    {
        if (self::$languageKey === 'default') {
            self::setLanguageKeys();
        }
        return self::$languageKey;
    }

    /**
     * Sets the currently active language/language_alt keys.
     * Default values are "default" for language key and "" for language_alt key.
     *
     * @return void
     */
    protected static function setLanguageKeys()
    {
        self::$languageKey = 'default';
        self::$alternativeLanguageKeys = [];
        if (TYPO3_MODE === 'FE') {
            $tsfe = static::getTypoScriptFrontendController();
            $siteLanguage = self::getCurrentSiteLanguage();
            // Get values from site language, which takes precedence over TypoScript settings since TYPO3 9 LTS
            if (class_exists('TYPO3\\CMS\\Core\\Site\\Entity\\SiteLanguage') && $siteLanguage instanceof \TYPO3\CMS\Core\Site\Entity\SiteLanguage) {
                self::$languageKey = $siteLanguage->getTypo3Language();
            } elseif (isset($tsfe->config['config']['language'])) {
                self::$languageKey = $tsfe->config['config']['language'];
            }
            if (isset($tsfe->config['config']['language_alt'])) {
                self::$alternativeLanguageKeys[] = $tsfe->config['config']['language_alt'];
            }
            if (empty(self::$alternativeLanguageKeys)) {
                $locales = GeneralUtility::makeInstance(Locales::class);
                if (in_array(self::$languageKey, $locales->getLocales())) {
                    foreach ($locales->getLocaleDependencies(self::$languageKey) as $language) {
                        self::$alternativeLanguageKeys[] = $language;
                    }
                }
            }
        } else {
            if (!empty($GLOBALS['BE_USER']->uc['lang'])) {
                self::$languageKey = $GLOBALS['BE_USER']->uc['lang'];
            } elseif (!empty(static::getLanguageService()->lang)) {
                self::$languageKey = static::getLanguageService()->lang;
            }
            // Get standard locale dependencies for the backend
            $locales = GeneralUtility::makeInstance(Locales::class);
            if (in_array(self::$languageKey, $locales->getLocales())) {
                foreach ($locales->getLocaleDependencies(self::$languageKey) as $language) {
                    self::$alternativeLanguageKeys[] = $language;
                }
            }
        }
        if (!self::$languageKey || self::$languageKey === 'default') {
            self::$languageKey = 'EN';
        }
    }

    /**
     * Set the collating locale
     *
     * @return mixed the set locale or false
     */
    public static function setCollatingLocale()
    {
        if (self::$collatingLocale === '') {
            $languageCode = self::getCurrentLanguage();
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            $languageRepository = $objectManager->get(LanguageRepository::class);
            list($languageIsoCodeA2, $countryIsoCodeA2) = explode('_', $languageCode, 2);
            $language = $languageRepository->findOneByIsoCodes($languageIsoCodeA2, $countryIsoCodeA2 ? $countryIsoCodeA2 : '');
            // If $language is NULL, current language was not found in the Language repository. Most probably, the repository is empty.
            self::$collatingLocale = ($language instanceof Language) ? $language->getCollatingLocale() : 'en_GB';
        }
        return setlocale(
            LC_COLLATE,
            [
                self::$collatingLocale . '.UTF-8',
                self::$collatingLocale . '.UTF8',
                self::$collatingLocale . '.utf8',
            ]
        );
    }

    /**
     * Returns the currently configured "site language" if a site is configured (= resolved)
     * in the current request.
     *
     * @return \TYPO3\CMS\Core\Site\Entity\SiteLanguage|null
     */
    protected static function getCurrentSiteLanguage()
    {
        if ($GLOBALS['TYPO3_REQUEST'] instanceof ServerRequestInterface) {
            return $GLOBALS['TYPO3_REQUEST']->getAttribute('language', null);
        }
        return null;
    }

    /**
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected static function getTypoScriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }

    /**
     * @return \TYPO3\CMS\Core\Localization\LanguageService|\TYPO3\CMS\Lang\LanguageService
     */
    protected static function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }
}
