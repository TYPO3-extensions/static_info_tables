<?php
namespace SJBR\StaticInfoTables\Domain\Repository;

/***************************************************************
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
 ***************************************************************/

use SJBR\StaticInfoTables\Cache\ClassCacheManager;
use SJBR\StaticInfoTables\Domain\Model\LanguagePack;
use SJBR\StaticInfoTables\Utility\VersionNumberUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extensionmanager\Utility\InstallUtility;

class LanguagePackRepository extends Repository
{
    /**
     * @var string Name of the extension this class belongs to
     */
    protected $extensionName = 'StaticInfoTables';

    /**
     * Writes the language pack files
     *
     * @param LanguagePack the object to be stored
     *
     * @return array localized messages
     */
    public function writeLanguagePack(LanguagePack $languagePack)
    {
        $content = [];

        $extensionKey = GeneralUtility::camelCaseToLowerCaseUnderscored($this->extensionName);
        $extensionPath = ExtensionManagementUtility::extPath($extensionKey);

        $content = [];
        $locale = $languagePack->getLocale();
        $localeLowerCase = strtolower($locale);
        $localeUpperCase = strtoupper($locale);
        $localeCamel = GeneralUtility::underscoredToUpperCamelCase(strtolower($locale));

        $languagePackExtensionKey = $extensionKey . '_' . $localeLowerCase;
        $languagePackExtensionPath = \TYPO3\CMS\Core\Core\Environment::getPublicPath() . '/typo3conf/ext/' . $languagePackExtensionKey . '/';

        // Cleanup any pre-existing language pack
        if (is_dir($languagePackExtensionPath)) {
            GeneralUtility::rmdir($languagePackExtensionPath, true);
        }
        // Create language pack directory structure
        if (!is_dir($languagePackExtensionPath)) {
            GeneralUtility::mkdir_deep($languagePackExtensionPath);
        }
        if (!is_dir($languagePackExtensionPath . 'Classes/Domain/Model/')) {
            GeneralUtility::mkdir_deep($languagePackExtensionPath . 'Classes/Domain/Model/');
        }
        if (!is_dir($languagePackExtensionPath . 'Configuration/DomainModelExtension/')) {
            GeneralUtility::mkdir_deep($languagePackExtensionPath . 'Configuration/DomainModelExtension/');
        }
        if (!is_dir($languagePackExtensionPath . 'Configuration/TCA/Overrides/')) {
            GeneralUtility::mkdir_deep($languagePackExtensionPath . 'Configuration/TCA/Overrides/');
        }
        if (!is_dir($languagePackExtensionPath . 'Configuration/PageTSconfig/')) {
            GeneralUtility::mkdir_deep($languagePackExtensionPath . 'Configuration/PageTSconfig/');
        }
        if (!is_dir($languagePackExtensionPath . 'Configuration/Extbase/Persistence/')) {
            GeneralUtility::mkdir_deep($languagePackExtensionPath . 'Configuration/Extbase/Persistence/');
        }
        if (!is_dir($languagePackExtensionPath . 'Resources/Private/Language/')) {
            GeneralUtility::mkdir_deep($languagePackExtensionPath . 'Resources/Private/Language/');
        }
        if (!is_dir($languagePackExtensionPath . 'Resources/Public/Icons/')) {
            GeneralUtility::mkdir_deep($languagePackExtensionPath . 'Resources/Public/Icons/');
        }

        // Get the source files of the language pack template
        $sourcePath = $extensionPath . 'Resources/Private/LanguagePackTemplate/';
        $sourceFiles = [];
        $sourceFiles = GeneralUtility::getAllFilesAndFoldersInPath($sourceFiles, $sourcePath);
        $sourceFiles = GeneralUtility::removePrefixPathFromList($sourceFiles, $sourcePath);
        $typo3VersionRange = VersionNumberUtility::splitVersionRange($languagePack->getTypo3VersionRange());
        $typo3VersionMinArray = VersionNumberUtility::convertVersionStringToArray($typo3VersionRange[0]);
        $typo3VersionMaxArray = VersionNumberUtility::convertVersionStringToArray(VersionNumberUtility::raiseVersionNumber('main', $typo3VersionRange[1]));
        // Set markers replacement values
        $replace = [
            '###LANG_ISO_LOWER###' => $localeLowerCase,
            '###LANG_ISO_UPPER###' => $localeUpperCase,
            '###LANG_ISO_CAMEL###' => $localeCamel,
            '###TYPO3_VERSION_RANGE###' => $languagePack->getTypo3VersionRange(),
            '###TYPO3_VERSION_MIN###' => $typo3VersionMinArray['version_main'] . '.' . $typo3VersionMinArray['version_sub'],
            '###TYPO3_VERSION_MAX###' => $typo3VersionMaxArray['version_main'] . '.0',
            '###VERSION###' => $languagePack->getVersion(),
            '###LANG_NAME###' => $languagePack->getLanguage(),
            '###AUTHOR###' => $languagePack->getAuthor(),
            '###AUTHOR_EMAIL###' => $languagePack->getAuthorEmail(),
            '###AUTHOR_COMPANY###' => $languagePack->getAuthorCompany(),
            '###VERSION_BASE###' => $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extensionKey]['version'],
            '###LANG_TCA_LABELS###' => $languagePack->getLocalizationLabels(),
            '###LANG_SQL_UPDATE###' => $languagePack->getUpdateQueries(),
        ];
        // Create the language pack files
        $success = true;
        foreach ($sourceFiles as $hash => $file) {
            $fileContent = GeneralUtility::getUrl($sourcePath . $file);
            foreach ($replace as $marker => $replacement) {
                $fileContent = str_replace($marker, $replacement, $fileContent);
            }
            $success = GeneralUtility::writeFile($languagePackExtensionPath . str_replace('.code', '.php', $file), $fileContent);
            if (!$success) {
                $content[] = LocalizationUtility::translate('couldNotWriteFile', $this->extensionName) . ' ' . $languagePackExtensionPath . $file;
                break;
            }
        }
        if ($success) {
            $classCacheManager = $this->objectManager->get(ClassCacheManager::class);
            $installUtility = $this->objectManager->get(InstallUtility::class);
            $installed = ExtensionManagementUtility::isLoaded($languagePackExtensionKey);
            if ($installed) {
                $content[] =  LocalizationUtility::translate('languagePack', $this->extensionName)
                    . ' ' . $languagePackExtensionKey
                    . ' ' . LocalizationUtility::translate('languagePackUpdated', $this->extensionName);
            } else {
                $content[] = LocalizationUtility::translate('languagePackCreated', $this->extensionName) . ' ' . $languagePack->getLanguage() . ' (' . $locale . ')';
                $installUtility->install($languagePackExtensionKey);
                $content[] = LocalizationUtility::translate('languagePack', $this->extensionName)
                    . ' ' . $languagePackExtensionKey
                    . ' ' . LocalizationUtility::translate('wasInstalled', $this->extensionName);
            }
            $classCacheManager->reBuild();
        }
        return $content;
    }
}