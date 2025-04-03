<?php

declare(strict_types=1);

/**
 * Part of the Cb-Builder
 * 
 * Created by:          Dennis Schwab (dennis.schwab90@icloud.com)
 * Created at:          16.03.2025
 * Last modified by:    -
 * Last modified at:    -
 * Licence: GPL2.0 or later
 * Version: 0.1.0
 */

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

namespace DS\CbBuilder\ChildTableFetcher;

use DS\CbBuilder\Utility\SimpleDatabaseQuery;
use DS\CbBuilder\Utility\Utility;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class ChildTableFetcher
{
    public function __construct (
        protected string $tableName,
        protected string $select,
        protected string $where,
        protected string $order = '',
        protected array $variables = [],
        protected bool $useNative = false
    ) { }

    /**
     * Fetch the current record.
     * 
     * @return array The current record.
     */
    public function basicFetch(): array
    {
        $sdq = new SimpleDatabaseQuery();
        return $sdq->fetch (
            $this->tableName,
            $this->select,
            $this->where,
            $this->order,
            $this->variables,
            $this->useNative
        );    
    }

    /**
     * Filters standard tt_content entries by excluding common fields.
     *
     * @return array The filtered columns.
     */
    private function _filterStdEntries(): array
    {
        $stdKeys = [
            'uid', 'pid', 'tstamp', 'crdate', 'deleted', 'hidden', 'starttime', 'endtime',
            'fe_group', 'sorting', 'rowDescription', 'editlock', 'sys_language_uid', 'l18n_parent',
            'l10n_source', 'l10n_state', 'l18n_diffsource', 't3ver_oid', 't3ver_wsid', 't3ver_state',
            't3ver_stage', 'frame_class', 'colPos', 'table_caption', 'tx_impexp_origuid', 'CType',
            'categories', 'layout', 'space_before_class', 'space_after_class', 'date', 'header',
            'header_layout', 'header_position', 'header_link', 'subheader', 'bodytext', 'image',
            'assets', 'imagewidth', 'imageheight', 'imageorient', 'imageborder', 'image_zoom',
            'imagecols', 'pages', 'recursive', 'list_type', 'media', 'records', 'sectionIndex',
            'linkToTop', 'pi_flexform', 'selected_categories', 'category_field', 'bullets_type',
            'cols', 'table_class', 'table_delimiter', 'table_enclosure', 'table_header_position',
            'table_tfoot', 'file_collections', 'filelink_size', 'filelink_sorting', 'target',
            'uploads_description', 'filelink_sorting_direction', 'uploads_type'
        ];
        return array_filter($GLOBALS['TCA']['tt_content']['columns'], function ($key) use ($stdKeys) {
            return array_search($key, $stdKeys) === false;
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Fetches child table data recursively based on TCA configuration.
     *
     * @param array $array The data array to populate.
     * @param array $tca The TCA configuration for the current table.
     * @param string $parentTableName The name of the parent table.
     * @param int $parentId The ID of the parent record.
     */
    private function _fetchChildTable(array &$array, array $tca, string $parentTableName, int $parentId): void
    {
        $sdq = new SimpleDatabaseQuery();
        if ($parentTableName === 'sys_file_reference') {
            $uidLocal = $sdq->fetch('sys_file_reference', 'uid_local', "uid==$parentId");
            $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
            $array['file'] = $resourceFactory->getFileObject($uidLocal[0]['uid_local']);
        } else {
            foreach ($tca as $key => $entry) {
                if ($key !== 'l10n_parent' && array_key_exists('config', $entry)) {
                    $config = $entry['config'];
                    if (isset($config['type']) && $config['type'] === 'inline') {
                        if (isset($config['foreign_table'])) {
                            $foreignTable = $config['foreign_table'];
                            if ($sdq->tableExists($foreignTable)) {
                                $foreignField = array_key_exists('foreign_field', $config) ? $config['foreign_field'] : '';
                                $foreignTableField = array_key_exists('foreign_table_field', $config) ? $config['foreign_table_field'] : '';
                                $where = '';
                                $results = [];
                                if ($foreignTableField !== '') $where .= $foreignTableField . "=='$parentTableName'";
                                if ($where !== '') $where .= '&&';
                                if ($foreignField !== '') $where .= $foreignField . "==$parentId";
                                if ($where !== '') {
                                    $results = $sdq->fetch(
                                        $foreignTable,
                                        '*',
                                        $where
                                    );
                                }
                                if (is_array($results) && $results !== []) {
                                    if (array_key_exists($foreignTable, $GLOBALS['TCA'])) {
                                        foreach ($results as &$result) {
                                            if (array_key_exists('uid', $result)) {
                                                $uid = $result['uid'];
                                                $this->_fetchChildTable($result, $GLOBALS['TCA'][$foreignTable]['columns'], $foreignTable, $uid);
                                            }
                                        }
                                    }
                                    $array[$key] = $results;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Fetches child tables for the given data and merges them into the data array.
     *
     * @param array $data The data array to populate.
     * @param string $tableName The name of the table.
     * 
     * @return array The fetched child tables of the entry.
     */
    private function _fetchChildTables(array $data): array
    {
        if (array_key_exists('uid', $data) && ($data['uid'] || $data['uid'] !== '')) {
            $customEntries = $this->_filterStdEntries();
            $results = [];
            $this->_fetchChildTable($results, $customEntries, $this->tableName, $data['uid']);
            $data = array_merge($data, $results);
        }
        return $data;
    }

    /**
     * Fetch all childs of the given record array.
     * 
     * @param array $data The record array.
     * 
     */
    public function fetchChilds(array $data): array
    {
        $index = 0;
        foreach ($data as $entry) {
            $data[$index++] = $this->_fetchChildTables($entry);
        }
        return $data;
    }
}