<?php
namespace SJBR\StaticInfoTables\Hook\Backend\Form\FormDataProvider;

/*
 *  Copyright notice
 *
 *  (c) 2017 Stanislas Rolland <typo3(arobas)sjbr.ca>
 *  All rights reserved
 *
 *  This script is part of the Typo3 project. The Typo3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
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

use SJBR\StaticInfoTables\Utility\LocalizationUtility;
use TYPO3\CMS\Backend\Form\Wizard\SuggestWizardDefaultReceiver;

/**
 * Processor for suggest items
 */
class SuggestLabelProcessor
{
    /**
     * Translate label of entity in suggest selector
     *
     * @param array $params: table, uid, row, entry
     * @param SuggestWizardDefaultReceiver $parentObj
     *
     * @return void
     */
    public function translateLabel(array &$params, SuggestWizardDefaultReceiver $parentObj)
    {
        $path = $params['entry']['path'];
        if (mb_strlen($path, 'utf-8') > 30) {
            $croppedPath = '<abbr title="' . htmlspecialchars($path) . '">' .
                htmlspecialchars(
                    mb_substr($path, 0, 10, 'utf-8')
                        . '...'
                        . mb_substr($path, -20, null, 'utf-8')
                ) .
                '</abbr>';
        } else {
            $croppedPath = htmlspecialchars($path);
        }
        $label = LocalizationUtility::translate(['uid' => $params['uid']], $params['table']);
        $params['entry']['text'] = '<span class="suggest-label">' . $label . '</span><span class="suggest-uid">[' . $params['uid'] . ']</span><br />
								<span class="suggest-path">' . $croppedPath . '</span>';
    }
}
