<?php

declare(strict_types=1);

/**
 * Part of the Cb-Builder
 * 
 * Created by:          Dennis Schwab
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

namespace DS\CbBuilder\Collector;

use DS\CbBuilder\Config\CbBuilderConfig;
use DS\CbBuilder\FieldBuilder\Tables\Table;
use DS\CbBuilder\Utility\CbPathUtility;
use DS\CbBuilder\Utility\ArrayParser;
use DS\CbBuilder\Utility\SimpleDatabaseQuery;
use Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class CollectorException extends Exception {}

final class Collector
{
    const COLLECT_COLUMNS = 1;
    const COLLECT_PALETTES = 2;

    /**
     * Collects content blocks from the 'contentBlocks.yaml' file.
     *
     * @param bool $reducedOutput Whether to return a reduced output.
     * @return array List of content blocks.
     */
    public static function collectContentBlocks(?bool $reducedOutput = true): array
    {
        $contentBlocks = Yaml::parseFile(__DIR__ . '/../../Configuration/contentBlocks.yaml');
        if (!$reducedOutput) {
            return $contentBlocks['contentBlocks'];
        }
        return array_map(function ($contentBlock) {
            return $contentBlock['name'] . ' -> ' . $contentBlock['identifier'];
        }, $contentBlocks['contentBlocks']);
    }

    /**
     * Collects the contents of a specific PHP file across all extensions.
     *
     * @param string $fileName Name of the PHP file to collect.
     * @return string Collected file contents.
     */
    public static function collectFileContents(string $fileName): string
    {
        $extensions = CbPathUtility::scanExtensionFolder();
        $contents = '';
        $filesystem = new Filesystem();
        foreach ($extensions as $extension) {
            $directoryIterator = CbPathUtility::getConfiguration($extension);
            
            if ($directoryIterator !== NULL) {
                $res = CbPathUtility::scanForPhpFiles($directoryIterator, $fileName);
                if (is_array($res) && $res !== []) {
                    $res = current($res);
                    if ($filesystem->exists($res)) {
                        $contents .= file_get_contents($res);
                    }
                }
            }
            $directoryIterator = CbPathUtility::getOverride($extension);
            if ($directoryIterator !== NULL) {
                $res = CbPathUtility::scanForPhpFiles($directoryIterator, $fileName);
                if (is_array($res) && $res !== []) {
                    $res = current($res);
                    if ($filesystem->exists($res)) {
                        $contents .= file_get_contents($res);
                    }
                }
            }
        }
        return $contents;
    }

    /**
     * Recursively finds elements in an array based on the collect type.
     *
     * @param array $subArray Array to search through.
     * @param int $collectType Type of elements to collect (columns or palettes).
     * @param bool $keysOnly Whether to return only keys.
     * @return array Collected elements.
     * @throws CollectorException If the collect type is unknown.
     */
    private static function _findElements(array $subArray, int $collectType, bool $keysOnly): array
    {
        $array = [];
        $type = $collectType === self::COLLECT_COLUMNS ? 'columns' : ($collectType === self::COLLECT_PALETTES ? 'palettes' : NULL);
        if ($type === NULL) {
            throw new CollectorException(
                "Error: Unknown collect type. Valid collect types are COLLECT_COLUMNS and COLLECT_PALETTES."
            );
        }
        foreach ($subArray as $key => $element) {
            if (is_array($element) && $element !== []) {
                if ($key === $type) {
                    if (!isset($array[$type])) $array[$type] = [];
                    foreach ($subArray[$type] as $key => $value) {
                        $array[$key] = $keysOnly === true ? $key : $value;
                    }
                } else {
                    $array = array_replace_recursive($array, Collector::_findElements($element, $collectType, $keysOnly));
                }
            }
        }
        return $array;
    }

    /**
     * Collects data from a TCA table.
     *
     * @param string $table Name of the TCA table.
     * @param int $collectType Type of data to collect (columns or palettes).
     * @param bool $keysOnly Whether to return only keys.
     * @param bool $local Whether to collect data locally.
     * @param string $content Optional content to parse.
     * @return array Collected data.
     * @throws CollectorException If the collect type is unknown.
     */
    public static function collectData(
        string $table,
        int $collectType = self::COLLECT_COLUMNS,
        bool $keysOnly = true,
        bool $local = false,
        string $content = ''
    ): array {
        $array = [];
        $contents = ($local === true && $content !== '') ? $content : Collector::collectFileContents($table . '.php');
        $res = ArrayParser::extractArraysFromString($contents, false, '', true);
        $type = $collectType === self::COLLECT_COLUMNS ? 'columns' : ($collectType === self::COLLECT_PALETTES ? 'palettes' : NULL);
        if ($type === NULL) {
            throw new CollectorException(
                "Error: Unknown collect type. Valid collect types are COLLECT_COLUMNS and COLLECT_PALETTES."
            );
        }
        if ($GLOBALS['CbBuilder']['config']['deepFieldSearch'] === true) {
            $array = Collector::_findElements($res, $collectType, $keysOnly);
        } else {
            if (isset($res['GLOBALS']['TCA'][$table][$type])) {
                if (!isset($array[$type])) $array[$type] = [];
                foreach ($res['GLOBALS']['TCA'][$table][$type] as $key => $value) {
                    $array[$key] = $keysOnly === true ? $key : $value;
                }
            }
            if (isset($res['return'][$type])) {
                if (!isset($array[$type])) $array[$type] = [];
                foreach ($res['return'][$type] as $key => $value) {
                    $array[$key] = $keysOnly === true ? $key : $value;
                }
            }
        }
        return $array;
    }

    /**
     * Collects all available fields for a table.
     *
     * @param Table $table Table object.
     * @param bool $collectLocally Whether to collect fields locally.
     * @return array Array of fields.
     */
    public static function collectAllAvailableFields(Table $table, bool $collectLocally = false): array
    {
        $array = [];
        $tableName = $table->getTable();
        if ($collectLocally === false) {
            $sdq = new SimpleDatabaseQuery();
            if ($sdq->tableExists($tableName)) {
                $array = $sdq->getAllFieldsOfTable($tableName);
                $array = array_combine(array_column($array, 'COLUMN_NAME'), array_map(fn($entry) => NULL, $array));
            }
            if (isset($GLOBALS['TCA'][$tableName]['columns'])) {
                foreach ($GLOBALS['TCA'][$tableName]['columns'] as $key => $value) {
                    $fieldAlreadyExisted = false;
                    
                    if (array_key_exists($key, $array)) {
                        $fieldAlreadyExisted = true;
                    }
                    $array[$key] = $value;
                    if ($fieldAlreadyExisted === true) {
                        $array[$key]['fieldAlreadyExisted'] = true;
                    }
                }
            }
        }
        if (CbBuilderConfig::getLocalConfig('parseFiles') === true) {
            $contents = Collector::collectFileContents($tableName . '.php');
            $res = ArrayParser::extractArraysFromString($contents, false, '', true);
            if (CbBuilderConfig::getLocalConfig('deepFieldSearch')  === true) {
                $tempArray = Collector::_findElements($res, self::COLLECT_COLUMNS, false);
                foreach ($tempArray as $key => $value) {
                    $array[$key] = $value;
                }
            } else {
                if (isset($res['GLOBALS']['TCA'][$table]['columns'])) {
                    foreach ($res['GLOBALS']['TCA'][$table]['columns'] as $key => $value) {
                        $array[$key] = $value;
                    }
                }
                if (isset($res['return']['columns'])) {
                    foreach ($res['return']['columns'] as $key => $value) {
                        $array[$key] = $value;
                    }
                }
            }
        }
        
        $tempArray = $table->getArrayFields();
        
        $array = array_merge($array, $tempArray);
        if (isset($array['columns'])) {
            unset($array['columns']);
        }
        return $array;
    }

    /**
     * Collects all meta information of the fields.yaml except the fields array.
     *
     * @param string $identifier The identifier of the current content block
     * @return array Array of meta information.
     */
    public static function collectFieldsYamlMeta(string $identifier): array
    {
        $fieldsYaml = CbBuilderConfig::getContentBlockPath($identifier) . '/fields.yaml';
        $filesystem = new Filesystem();
        if ($filesystem->exists($fieldsYaml)) {
            $meta = [];
            $entries = Yaml::parseFile($fieldsYaml);
            foreach ($entries as $key => $entry) {
                if ($key !== 'fields') {
                    $meta[$key] = $entry;
                }
            }
            return $meta;
        }
        return [];
    }
}
