<?php
namespace SJBR\StaticInfoTables\Hook\Backend\Form\Wizard;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2007-2011 Andreas Wolf <andreas.wolf@ikt-werk.de>
 *  (c) 2013-2018 Stanislas Rolland <typo3(arobas)sjbr.ca>
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

use SJBR\StaticInfoTables\Utility\LocalizationUtility;

/**
 * Default implementation of a handler class for an ajax record selector.
 *
 * Normally other implementations should be inherited from this one.
 * queryTable() should not be overwritten under normal circumstances.
 *
 * @author Andreas Wolf <andreas.wolf@ikt-werk.de>
 * @author Benjamin Mack <benni@typo3.org>
 * @author Stanislas Rolland <typo3(arobas)sjbr.ca>
 */
class SuggestReceiver extends \TYPO3\CMS\Backend\Form\Wizard\SuggestWizardDefaultReceiver
{
    /**
     * Prepare the statement for selecting the records which will be returned to the selector. May also return some
     * other records (e.g. from a mm-table) which will be used later on to select the real records
     *
     * @return void
     */
    protected function prepareSelectStatement()
    {
        $expressionBuilder = $this->queryBuilder->expr();
        $searchWholePhrase = !isset($this->config['searchWholePhrase']) || $this->config['searchWholePhrase'];
        $searchString = $this->params['value'];
        $searchUid = (int)$searchString;
        if ($searchString !== '') {
            $likeCondition = ($searchWholePhrase ? '%' : '') . $this->queryBuilder->escapeLikeWildcards($searchString) . '%';
            // Get the label field for the current language, if any is available
            $lang = LocalizationUtility::getCurrentLanguage();
            $lang = LocalizationUtility::getIsoLanguageKey($lang);
            $labelFields = LocalizationUtility::getLabelFields($this->table, $lang);
            $selectFieldsList = $labelFields[0] . ',' . $this->config['additionalSearchFields'];
            $selectFields = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $selectFieldsList, true);
            $selectFields = array_unique($selectFields);
            $selectParts = $expressionBuilder->orX();
            foreach ($selectFields as $field) {
                $selectParts->add($expressionBuilder->like($field, $this->queryBuilder->createPositionalParameter($likeCondition)));
            }
            $searchClause = $expressionBuilder->orX($selectParts);
            if ($searchUid > 0 && $searchUid == $searchString) {
                $searchClause->add($expressionBuilder->eq('uid', $searchUid));
            }
            $this->queryBuilder->andWhere($expressionBuilder->orX($searchClause));
        }
        if (!empty($this->allowedPages)) {
            $pidList = array_map('intval', $this->allowedPages);
            if (!empty($pidList)) {
                $this->queryBuilder->andWhere(
                    $expressionBuilder->in('pid', $pidList)
                );
            }
        }
        // add an additional search condition comment
        if (isset($this->config['searchCondition']) && $this->config['searchCondition'] !== '') {
            $this->queryBuilder->andWhere(QueryHelper::stripLogicalOperatorPrefix($this->config['searchCondition']));
        }
    }

    /**
     * Prepares the clause by which the result elements are sorted. See description of ORDER BY in
     * SQL standard for reference.
     *
     * @return void
     */
    protected function prepareOrderByStatement()
    {
        // Get the label field for the current language, if any is available
        $lang = LocalizationUtility::getCurrentLanguage();
        $lang = LocalizationUtility::getIsoLanguageKey($lang);
        $labelFields = LocalizationUtility::getLabelFields($this->table, $lang);
        if (!empty($labelFields)) {
            foreach ($labelFields as $labelField) {
                $this->queryBuilder->addOrderBy($labelField);
            }
        } elseif ($GLOBALS['TCA'][$this->table]['ctrl']['label']) {
            $this->queryBuilder->addOrderBy($GLOBALS['TCA'][$this->table]['ctrl']['label']);
        }
    }

    /**
     * Manipulate a record before using it to render the selector; may be used to replace a MM-relation etc.
     *
     * @param array $row
     */
    protected function manipulateRecord(&$row)
    {
        // Localize the record
        $row[$GLOBALS['TCA'][$this->table]['ctrl']['label']] = LocalizationUtility::translate(['uid' => $row['uid']], $this->table);
    }
}
