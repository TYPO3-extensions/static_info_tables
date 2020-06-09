<?php

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace SJBR\StaticInfoTables\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * Class with helper functions for version number handling
 */
class VersionNumberUtility
{
    /**
     * Splits a version range into an array.
     *
     * If a single version number is given, it is considered a minimum value.
     * If a dash is found, the numbers left and right are considered as minimum and maximum. Empty values are allowed.
     * If no version can be parsed "0.0.0" — "0.0.0" is the result
     *
     * @param string $version A string with a version range.
     * @return array
     */
    public static function splitVersionRange($version)
    {
        $versionRange = [];
        if (strpos($version, '-') !== false) {
            $versionRange = explode('-', $version, 2);
        } else {
            $versionRange[0] = $version;
            $versionRange[1] = '';
        }
        if (!$versionRange[0]) {
            $versionRange[0] = '0.0.0';
        }
        if (!$versionRange[1]) {
            $versionRange[1] = '0.0.0';
        }
        return $versionRange;
    }

    /**
     * Parses the version number x.x.x and returns an array with the various parts.
     * It also forces each … 0 to 999
     *
     * @param string $version Version code, x.x.x
     * @return array
     */
    public static function convertVersionStringToArray($version)
    {
        $parts = GeneralUtility::intExplode('.', $version . '..');
        $parts[0] = MathUtility::forceIntegerInRange($parts[0], 0, 999);
        $parts[1] = MathUtility::forceIntegerInRange($parts[1], 0, 999);
        $parts[2] = MathUtility::forceIntegerInRange($parts[2], 0, 999);
        $result = [];
        $result['version'] = $parts[0] . '.' . $parts[1] . '.' . $parts[2];
        $result['version_int'] = (int)($parts[0] * 1000000 + $parts[1] * 1000 + $parts[2]);
        $result['version_main'] = $parts[0];
        $result['version_sub'] = $parts[1];
        $result['version_dev'] = $parts[2];
        return $result;
    }

    /**
     * Method to raise a version number
     *
     * @param string $raise one of "main", "sub", "dev" - the version part to raise by one
     * @param string $version (like 4.1.20)
     * @return string
     */
    public static function raiseVersionNumber($raise, $version)
    {
        if (!in_array($raise, ['main', 'sub', 'dev'])) {
            throw new Exception('RaiseVersionNumber expects one of "main", "sub" or "dev".', 1342639555);
        }
        $parts = GeneralUtility::intExplode('.', $version . '..');
        $parts[0] = MathUtility::forceIntegerInRange($parts[0], 0, 999);
        $parts[1] = MathUtility::forceIntegerInRange($parts[1], 0, 999);
        $parts[2] = MathUtility::forceIntegerInRange($parts[2], 0, 999);
        switch ((string)$raise) {
            case 'main':
                $parts[0]++;
                $parts[1] = 0;
                $parts[2] = 0;
                break;
            case 'sub':
                $parts[1]++;
                $parts[2] = 0;
                break;
            case 'dev':
                $parts[2]++;
                break;
        }
        return $parts[0] . '.' . $parts[1] . '.' . $parts[2];
    }
}