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

namespace DS\CbBuilder\SqlCreater;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use DS\CbBuilder\Collector\Collector;
use DS\CbBuilder\Config\CbBuilderConfig;
use DS\CbBuilder\FieldBuilder\FieldBuilder;
use DS\CbBuilder\FieldBuilder\Tables\Table as CbBuilderTable;
use DS\CbBuilder\Wrapper\Wrapper;
use DS\CbBuilder\Utility\SimpleDatabaseQuery;
use Exception;
use Symfony\Component\Filesystem\Filesystem;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * Custom exception for SQL creator.
 */
class SqlCreaterException extends Exception {}

/**
 * Class responsible for creating and managing SQL queries for tables.
 */
final class SqlCreater
{
    /**
     * Doctrine schema instance.
     */
    protected ?Schema $schema = null;

    /**
     * Database platform instance.
     */
    protected ?AbstractPlatform $databasePlatform = null;

    /**
     * Initializes the schema and database platform.
     */
    public function __construct()
    {
        $this->schema = new Schema();
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionByName('Default');
        $this->databasePlatform = $connection->getDatabasePlatform();
    }

    /**
     * Creates a Content Builder table in the database.
     *
     * @param string $identifier Identifier for the table.
     */
    public static function createCbTable(string $identifier): void
    {
        $expression = "\nCREATE TABLE cb_table (`uid` INT UNSIGNED AUTO_INCREMENT NOT NULL, " .
            "`pid` INT UNSIGNED DEFAULT 0 NOT NULL, `tstamp` INT UNSIGNED DEFAULT 0 NOT NULL, " .
            "`crdate` INT UNSIGNED DEFAULT 0 NOT NULL, `deleted` SMALLINT UNSIGNED DEFAULT 0 NOT NULL, " .
            "`hidden` SMALLINT UNSIGNED DEFAULT 0 NOT NULL, `starttime` INT UNSIGNED DEFAULT 0 NOT NULL, " .
            "`endtime` INT UNSIGNED DEFAULT 0 NOT NULL, `fe_group` VARCHAR(255) DEFAULT '0' NOT NULL, " .
            "`sorting` INT DEFAULT 0 NOT NULL, `editlock` SMALLINT UNSIGNED DEFAULT 0 NOT NULL, " .
            "`sys_language_uid` INT DEFAULT 0 NOT NULL, `l18n_parent` INT UNSIGNED DEFAULT 0 NOT NULL, " .
            "`l10n_source` INT UNSIGNED DEFAULT 0 NOT NULL, `l10n_state` TEXT DEFAULT NULL, " .
            "`l18n_diffsource` MEDIUMBLOB DEFAULT NULL, `t3ver_oid` INT UNSIGNED DEFAULT 0 NOT NULL, " .
            "`t3ver_wsid` INT UNSIGNED DEFAULT 0 NOT NULL, `t3ver_state` SMALLINT DEFAULT 0 NOT NULL, " .
            "`t3ver_stage` INT DEFAULT 0 NOT NULL, `preset` VARCHAR(255) DEFAULT '' NOT NULL, " .
            "`classes` LONGTEXT DEFAULT NULL, INDEX `parent` (pid, deleted, hidden), `CType` VARCHAR(255) DEFAULT '' NOT NULL," .
            "`tt_content_uid` INT UNSIGNED DEFAULT 0 NOT NULL, INDEX `translation_source` (l10n_source), " .
            "INDEX `t3ver_oid` (t3ver_oid, t3ver_wsid), PRIMARY KEY(uid));\n" .
            "CREATE TABLE tt_content (`cb_index` INT UNSIGNED DEFAULT 0 NOT NULL);\n";

        if (!isset($GLOBALS['CbBuilder']['extensionPath'])) {
            CbBuilderConfig::loadLocalConfig($identifier);
        }
        $file = $GLOBALS['CbBuilder']['extensionPath'] . "/ext_tables.sql";
        $filesystem = new Filesystem();
        if (!$filesystem->exists($file)) {
            $filesystem->touch($file);
        }
        Wrapper::inject(
            $file,
            $expression,
            true,
            '--',
            "CB_TABLE",
            "§%!&(&?%§",
            "§%!&)&?%§",
            "Do not modify this part of the code unless you want to uninstall the entire extension."
        );
    }

    /**
     * Creates a new table in the schema.
     *
     * @param string $name Name of the table to create.
     */
    public function createAndAddTable(string $name): void
    {
        $this->schema->createTable($name);
    }

    /**
     * Quotes a database identifier.
     *
     * @param string $identifier Identifier to quote.
     *
     * @return string Quoted identifier.
     */
    protected function quote(string $identifier): string
    {
        return '`' . $identifier . '`';
    }

    /**
     * Checks if a column is defined for a specific table.
     *
     * @param array  $tables     Array of tables.
     * @param string $tableName  Name of the table to check.
     * @param string $fieldName  Name of the column to check.
     *
     * @return bool Whether the column is defined for the table.
     */
    protected function isColumnDefinedForTable(array $tables, string $tableName, string $fieldName): bool
    {
        return ($tables[$tableName] ?? null)?->hasColumn($fieldName) ?? false;
    }

    /**
     * Exports SQL queries for the schema.
     *
     * @return string SQL queries as a string.
     */
    public function exportSqlQueries(): string
    {
        $sqlQueries = '';
        
        // Remove empty tables from the schema.
        $tables = $this->schema->getTables();
        foreach ($tables as $table) {
            if ($table->getColumns() === []) {
                $this->schema->dropTable($table->getName());
            }
        }
        
        // Convert schema to SQL queries.
        $sqlArrayQueries = $this->schema->toSql($this->databasePlatform);
        
        // Concatenate queries into a single string.
        foreach ($sqlArrayQueries as $query) {
            $sqlQueries .= $query . ";\n";
        }
        
        return $sqlQueries;
    }

    /**
     * Writes SQL queries to a file.
     *
     * @param string $sqlQueries SQL queries to write.
     */
    public static function writeQueries(string $sqlQueries): void
    {
        $file = CbBuilderConfig::getExtensionPath() . "/ext_tables.sql";
        Wrapper::inject($file, $sqlQueries, true, '--');
    }

    /**
     * Add single fields based on tables TCA 'columns'.
     *
     * @param array<non-empty-string, Table> $tables
     * @return array<non-empty-string, Table>
     * Taken from the TYPO3 core. Adjusted to own needings.
     */
    public function enrichSingleTableFieldsFromTcaColumns(string $name, CbBuilderTable $table): bool
    {
        // In the following, columns for TCA fields with a dedicated TCA type are
        // added. In the unlikely case that no columns exist, we can skip the table.

        $allFields = Collector::collectAllAvailableFields($table, true);
        $fields = array_filter(array_map(function ($entry) {
                return isset($entry['fieldAlreadyExisted']) ? NULL : $entry;
            }, $allFields
        ));
        
        array_walk($fields, function (&$entry) {
            if (isset($entry['config'])) {
                $entry = array_merge($entry, $entry['config']);
                unset($entry['config']);
            }
        });

        if ($fields === []) {
            return false;
        }

        $fieldIdentifiers = array_keys($table->getFields());

        foreach ($fields as $fieldName => $fieldConfig) {
            if (!in_array($fieldName, $fieldIdentifiers)) {
                continue;
            }
            $isOverride = $fieldConfig['useExistingField'] ?? false;
            if ($isOverride === true) {
                continue;
            }

            
            $type = (string)($fieldConfig['type'] ?? '');
            if ($type === '') {
                continue;
            }

            $type = FieldBuilder::convertTypeFieldToColumn($type);

            if ($type === '') {
                continue;
            }

            switch ($type) {
                case 'category':
                    if (($fieldConfig['relationship'] ?? '') === 'oneToMany') {
                        $this->schema->getTable($name)->addColumn(
                            $this->quote($fieldName),
                            Types::TEXT,
                            [
                                'notnull' => false,
                            ]
                        );
                    } else {
                        $this->schema->getTable($name)->addColumn(
                            $this->quote($fieldName),
                            Types::INTEGER,
                            [
                                'default' => 0,
                                'notnull' => true,
                                'unsigned' => true,
                            ]
                        );
                    }
                    break;

                case 'datetime':
                    $dbType = $fieldConfig['dbType'] ?? '';
                    // Add datetime fields for all tables, defining datetime columns (TCA type=datetime), except
                    // those columns, which had already been added due to definition in "ctrl", e.g. "starttime".
                    if (in_array($dbType, QueryHelper::getDateTimeTypes(), true)) {
                        $nullable = $fieldConfig['nullable'] ?? true;
                        $this->schema->getTable($name)->addColumn(
                            $this->quote($fieldName),
                            $dbType,
                            [
                                // native datetime fields are nullable by default, and
                                // are only not-nullable if `nullable` is explicitly set to false.
                                'notnull' => !$nullable,
                            ]
                        );
                    } else {
                        // int unsigned:            from 1970 to 2106.
                        // int signed:              from 1901 to 2038.
                        // bigint unsigned/signed:  from whenever to whenever
                        //
                        // Anything like crdate,tstamp,starttime,endtime is good with
                        //  "int unsigned" and can survive the 2038 apocalypse (until 2106).
                        //
                        // However, anything that has birthdates or dates
                        // from the past (sys_file_metadata.content_creation_date) was saved
                        // as a SIGNED INT. It allowed birthdays of people older than 1970,
                        // but with the downside that it ends in 2038.
                        //
                        // This is now changed to utilize BIGINT everywhere, even when smaller
                        // date ranges are requested. To reduce complexity, we specifically
                        // do not evaluate "range.upper/lower" fields and use a unified type here.
                        $this->schema->getTable($name)->addColumn(
                            $this->quote($fieldName),
                            Types::BIGINT,
                            [
                                'default' => 0,
                                'notnull' => !($fieldConfig['nullable'] ?? false),
                                'unsigned' => false,
                            ]
                        );
                    }
                    break;

                case 'slug':
                    $this->schema->getTable($name)->addColumn(
                        $this->quote($fieldName),
                        Types::TEXT,
                        [
                            'length' => 65535,
                            'notnull' => false,
                        ]
                    );
                    break;

                case 'json':
                    $this->schema->getTable($name)->addColumn(
                        $this->quote($fieldName),
                        Types::JSON,
                        [
                            'notnull' => false,
                        ]
                    );
                    break;

                case 'uuid':
                    $this->schema->getTable($name)->addColumn(
                        $this->quote($fieldName),
                        Types::STRING,
                        [
                            'length' => 36,
                            'default' => '',
                            'notnull' => true,
                        ]
                    );
                    break;

                case 'file':
                    $this->schema->getTable($name)->addColumn(
                        $this->quote($fieldName),
                        Types::INTEGER,
                        [
                            'default' => 0,
                            'notnull' => true,
                            'unsigned' => true,
                        ]
                    );
                    break;

                case 'folder':
                case 'imageManipulation':
                case 'flex':
                case 'text':
                    $this->schema->getTable($name)->addColumn(
                        $this->quote($fieldName),
                        Types::TEXT,
                        [
                            'notnull' => false,
                        ]
                    );
                    break;

                case 'email':
                    $isNullable = (bool)($fieldConfig['nullable'] ?? false);
                    $this->schema->getTable($name)->addColumn(
                        $this->quote($fieldName),
                        Types::STRING,
                        [
                            'length' => 255,
                            'default' => ($isNullable ? null : ''),
                            'notnull' => !$isNullable,
                        ]
                    );
                    break;

                case 'check':
                    $this->schema->getTable($name)->addColumn(
                        $this->quote($fieldName),
                        Types::SMALLINT,
                        [
                            'default' => $fieldConfig['default'] ?? 0,
                            'notnull' => true,
                            'unsigned' => true,
                        ]
                    );
                    break;

                case 'language':
                    $this->schema->getTable($name)->addColumn(
                        $this->quote($fieldName),
                        Types::INTEGER,
                        [
                            'default' => 0,
                            'notnull' => true,
                            'unsigned' => false,
                        ]
                    );
                    break;

                case 'group':
                    if (isset($fieldConfig['MM'])) {
                        $this->schema->getTable($name)->addColumn(
                            $this->quote($fieldName),
                            Types::INTEGER,
                            [
                                'default' => 0,
                                'notnull' => true,
                                'unsigned' => true,
                            ]
                        );
                    } else {
                        $this->schema->getTable($name)->addColumn(
                            $this->quote($fieldName),
                            Types::TEXT,
                            [
                                'notnull' => false,
                            ]
                        );
                    }
                    break;

                case 'password':
                    if ($fieldConfig['nullable'] ?? false) {
                        $this->schema->getTable($name)->addColumn(
                            $this->quote($fieldName),
                            Types::STRING,
                            [
                                'length' => 255,
                                'default' => null,
                                'notnull' => false,
                            ]
                        );
                    } else {
                        $this->schema->getTable($name)->addColumn(
                            $this->quote($fieldName),
                            Types::STRING,
                            [
                                'length' => 255,
                                'default' => '',
                                'notnull' => true,
                            ]
                        );
                    }
                    break;

                case 'color':
                    $opacity = (bool)($fieldConfig['opacity'] ?? false);
                    if ($fieldConfig['nullable'] ?? false) {
                        $this->schema->getTable($name)->addColumn(
                            $this->quote($fieldName),
                            Types::STRING,
                            [
                                'length' => $opacity ? 9 : 7,
                                'default' => null,
                                'notnull' => false,
                            ]
                        );
                    } else {
                        $this->schema->getTable($name)->addColumn(
                            $this->quote($fieldName),
                            Types::STRING,
                            [
                                'length' => $opacity ? 9 : 7,
                                'default' => '',
                                'notnull' => true,
                            ]
                        );
                    }
                    break;

                case 'radio':
                    $hasItemsProcFunc = ($fieldConfig['itemsProcFunc'] ?? '') !== '';
                    $items = $fieldConfig['items'] ?? [];
                    // With itemsProcFunc we can't be sure, which values are persisted. Use type string.
                    if ($hasItemsProcFunc) {
                        $this->schema->getTable($name)->addColumn(
                            $this->quote($fieldName),
                            Types::STRING,
                            [
                                'length' => 255,
                                'default' => '',
                                'notnull' => true,
                            ]
                        );
                        break;
                    }
                    // If no items are configured, use type string to be safe for values added directly.
                    if ($items === []) {
                        $this->schema->getTable($name)->addColumn(
                            $this->quote($fieldName),
                            Types::STRING,
                            [
                                'length' => 255,
                                'default' => '',
                                'notnull' => true,
                            ]
                        );
                        break;
                    }
                    // If only one value is NOT an integer use type string.
                    foreach ($items as $item) {
                        if (!MathUtility::canBeInterpretedAsInteger($item['value'])) {
                            $this->schema->getTable($name)->addColumn(
                                $this->quote($fieldName),
                                Types::STRING,
                                [
                                    'length' => 255,
                                    'default' => '',
                                    'notnull' => true,
                                ]
                            );
                            // continue with next $tableDefinition['columns']
                            // see: DefaultTcaSchemaTest->enrichAddsRadioStringVerifyThatCorrectLoopIsContinued()
                            break 2;
                        }
                    }
                    // Use integer type.
                    $allValues = array_map(fn(array $item): int => (int)$item['value'], $items);
                    $minValue = min($allValues);
                    $maxValue = max($allValues);
                    // Try to safe some bytes - can be reconsidered to simply use Types::INTEGER.
                    $integerType = ($minValue >= -32768 && $maxValue < 32768)
                        ? Types::SMALLINT
                        : Types::INTEGER;
                    $this->schema->getTable($name)->addColumn(
                        $this->quote($fieldName),
                        $integerType,
                        [
                            'default' => 0,
                            'notnull' => true,
                        ]
                    );
                    break;

                case 'link':
                    $nullable = $fieldConfig['nullable'] ?? false;
                    $this->schema->getTable($name)->addColumn(
                        $this->quote($fieldName),
                        Types::TEXT,
                        [
                            'length' => 65535,
                            'default' => $nullable ? null : '',
                            'notnull' => !$nullable,
                        ]
                    );
                    break;

                case 'input':
                    $length = (int)($fieldConfig['max'] ?? 255);
                    $nullable = $fieldConfig['nullable'] ?? false;
                    if ($length > 255) {
                        $this->schema->getTable($name)->addColumn(
                            $this->quote($fieldName),
                            Types::TEXT,
                            [
                                'length' => 65535,
                                'default' => $nullable ? null : '',
                                'notnull' => !$nullable,
                            ]
                        );
                        break;
                    }
                    $this->schema->getTable($name)->addColumn(
                        $this->quote($fieldName),
                        Types::STRING,
                        [
                            'length' => $length,
                            'default' => '',
                            'notnull' => !$nullable,
                        ]
                    );
                    break;

                case 'inline':
                    if (($fieldConfig['MM'] ?? '') !== '' || ($fieldConfig['foreign_field'] ?? '') !== '') {
                        // Parent "count" field
                        $this->schema->getTable($name)->addColumn(
                            $this->quote($fieldName),
                            Types::INTEGER,
                            [
                                'default' => 0,
                                'notnull' => true,
                                'unsigned' => true,
                            ]
                        );
                    } else {
                        // Inline "csv"
                        $this->schema->getTable($name)->addColumn(
                            $this->quote($fieldName),
                            Types::STRING,
                            [
                                'default' => '',
                                'notnull' => true,
                                'length' => 255,
                            ]
                        );
                    }
                    if (($fieldConfig['foreign_field'] ?? '') !== '') {
                        // Add definition for "foreign_field" (contains parent uid) in the child table if it is not defined
                        // in child TCA or if it is "just" a "passthrough" field, and not manually configured in ext_tables.sql
                        $childTable = $fieldConfig['foreign_table'];
                        $sdq = new SimpleDatabaseQuery();
                        if (!$this->schema->hasTable($childTable) && !$sdq->tableExists($childTable)) {
                            $this->schema->createTable($childTable);
                            //throw new DefaultTcaSchemaTablePositionException('Table ' . $childTable . ' not found in schema list', 1527854474);
                        }
                        $childTableForeignFieldName = $fieldConfig['foreign_field'];
                        $childTableForeignFieldConfig = $GLOBALS['TCA'][$childTable]['columns'][$childTableForeignFieldName] ?? [];
                        if (($childTableForeignFieldConfig === [] || ($childTableForeignFieldConfig['type'] ?? '') === 'passthrough')
                            && !$this->isColumnDefinedForTable($this->schema->getTables(), $childTable, $childTableForeignFieldName)
                        ) {
                            $this->schema->getTable($childTable)->addColumn(
                                $this->quote($childTableForeignFieldName),
                                Types::INTEGER,
                                [
                                    'default' => 0,
                                    'notnull' => true,
                                    'unsigned' => true,
                                ]
                            );
                        }
                        // Add definition for "foreign_table_field" (contains name of parent table) in the child table if it is not
                        // defined in child TCA or if it is "just" a "passthrough" field, and not manually configured in ext_tables.sql
                        $childTableForeignTableFieldName = $fieldConfig['foreign_table_field'] ?? '';
                        $childTableForeignTableFieldConfig = $GLOBALS['TCA'][$childTable]['columns'][$childTableForeignTableFieldName] ?? [];
                        if ($childTableForeignTableFieldName !== ''
                            && ($childTableForeignTableFieldConfig === [] || ($childTableForeignTableFieldConfig['type'] ?? '') === 'passthrough')
                            && !$this->isColumnDefinedForTable($this->schema->getTables(), $childTable, $childTableForeignTableFieldName)
                        ) {
                            $this->schema->getTable($childTable)->addColumn(
                                $this->quote($childTableForeignTableFieldName),
                                Types::STRING,
                                [
                                    'default' => '',
                                    'notnull' => true,
                                    'length' => 255,
                                ]
                            );
                        }
                    }
                    break;

                case 'number':
                    $type = ($fieldConfig['format'] ?? '') === 'decimal' ? Types::DECIMAL : Types::INTEGER;
                    $nullable = $fieldConfig['nullable'] ?? false;
                    $lowerRange = $fieldConfig['range']['lower'] ?? -1;
                    // Integer type for all database platforms.
                    if ($type === Types::INTEGER) {
                        $this->schema->getTable($name)->addColumn(
                            $this->quote($fieldName),
                            Types::INTEGER,
                            [
                                'default' => $nullable === true ? null : 0,
                                'notnull' => !$nullable,
                                'unsigned' => $lowerRange >= 0,
                            ]
                        );
                        break;
                    }
                    // SQLite internally defines NUMERIC() fields as real, and therefore as floating numbers. pdo_sqlite
                    // then returns PHP float which can lead to rounding issues. See https://bugs.php.net/bug.php?id=81397
                    // for more details. We create a 'string' field on SQLite as workaround.
                    // @todo: Database schema should be created with MySQL in mind and not mixed. Transforming to the
                    //        concrete database platform is handled in the database compare area. Sadly, this is not
                    //        possible right now but upcoming preparation towards doctrine/dbal 4 makes it possible to
                    //        move this "hack" to a different place.
                    if ($this->databasePlatform instanceof SQLitePlatform) {
                        $this->schema->getTable($name)->addColumn(
                            $this->quote($fieldName),
                            Types::STRING,
                            [
                                'default' => $nullable === true ? null : '0.00',
                                'notnull' => !$nullable,
                                'length' => 255,
                            ]
                        );
                        break;
                    }
                    // Decimal for all supported platforms except SQLite
                    $this->schema->getTable($name)->addColumn(
                        $this->quote($fieldName),
                        Types::DECIMAL,
                        [
                            'default' => $nullable === true ? null : 0.00,
                            'notnull' => !$nullable,
                            'unsigned' => $lowerRange >= 0,
                            'precision' => 10,
                            'scale' => 2,
                        ]
                    );
                    break;

                case 'select':
                    if (($fieldConfig['MM'] ?? '') !== '') {
                        // MM relation, this is a "parent count" field. Have an int.
                        $this->schema->getTable($name)->addColumn(
                            $this->quote($fieldName),
                            Types::INTEGER,
                            [
                                'notnull' => true,
                                'default' => 0,
                                'unsigned' => true,
                            ]
                        );
                        break;
                    }
                    $dbFieldLength = (int)($fieldConfig['dbFieldLength'] ?? 0);
                    // If itemsProcFunc is not set, check the item values
                    if (($fieldConfig['itemsProcFunc'] ?? '') === '') {
                        $items = $fieldConfig['items'] ?? [];
                        $itemsContainsOnlyIntegers = true;
                        foreach ($items as $item) {
                            if (!MathUtility::canBeInterpretedAsInteger($item['value'])) {
                                $itemsContainsOnlyIntegers = false;
                                break;
                            }
                        }
                        $itemsAreAllPositive = true;
                        foreach ($items as $item) {
                            if ($item['value'] < 0) {
                                $itemsAreAllPositive = false;
                                break;
                            }
                        }
                        // @todo: The dependency to renderType is unfortunate here. It's only purpose is to potentially have int fields
                        //        instead of string when this is a 'single' relation / value. However, renderType should usually not
                        //        influence DB layer at all. Maybe 'selectSingle' should be changed to an own 'type' instead to make
                        //        this more explicit. Maybe DataHandler could benefit from this as well?
                        if (($fieldConfig['renderType'] ?? '') === 'selectSingle' || ($fieldConfig['maxitems'] ?? 0) === 1) {
                            // With 'selectSingle' or with 'maxitems = 1', only a single value can be selected.
                            if (
                                !is_array($fieldConfig['fileFolderConfig'] ?? false)
                                && ($items !== [] || ($fieldConfig['foreign_table'] ?? '') !== '')
                                && $itemsContainsOnlyIntegers === true
                            ) {
                                // If the item list is empty, or if it contains only int values, an int field is enough.
                                // Also, the config must not be a 'fileFolderConfig' field which takes string values.
                                $this->schema->getTable($name)->addColumn(
                                    $this->quote($fieldName),
                                    Types::INTEGER,
                                    [
                                        'notnull' => true,
                                        'default' => 0,
                                        'unsigned' => $itemsAreAllPositive,
                                    ]
                                );
                                break;
                            }
                            // If int is no option, have a string field.
                            $this->schema->getTable($name)->addColumn(
                                $this->quote($fieldName),
                                Types::STRING,
                                [
                                    'notnull' => true,
                                    'default' => '',
                                    'length' => $dbFieldLength > 0 ? $dbFieldLength : 255,
                                ]
                            );
                            break;
                        }
                        if ($itemsContainsOnlyIntegers) {
                            // Multiple values can be selected and will be stored comma separated. When manual item values are
                            // all integers, or if there is a foreign_table, we end up with a comma separated list of integers.
                            // Using string / varchar 255 here should be long enough to store plenty of values, and can be
                            // changed by setting 'dbFieldLength'.
                            $this->schema->getTable($name)->addColumn(
                                $this->quote($fieldName),
                                Types::STRING,
                                [
                                    // @todo: nullable = true is not a good default here. This stems from the fact that this
                                    //        if triggers a lot of TEXT->VARCHAR() field changes during upgrade, where TEXT
                                    //        is always nullable, but varchar() is not. As such, we for now declare this
                                    //        nullable, but could have a look at it later again when a value upgrade
                                    //        for such cases is in place that updates existing null fields to empty string.
                                    'notnull' => false,
                                    'default' => '',
                                    'length' => $dbFieldLength > 0 ? $dbFieldLength : 255,
                                ]
                            );
                            break;
                        }
                    }
                    if ($dbFieldLength > 0) {
                        // If nothing else matches, but there is a dbFieldLength set, have varchar with that length.
                        $this->schema->getTable($name)->addColumn(
                            $this->quote($fieldName),
                            Types::STRING,
                            [
                                'notnull' => true,
                                'default' => '',
                                'length' => $dbFieldLength,
                            ]
                        );
                    } else {
                        // Final fallback creates a (nullable) text field.
                        $this->schema->getTable($name)->addColumn(
                            $this->quote($fieldName),
                            Types::TEXT,
                            [
                                'notnull' => false,
                            ]
                        );
                    }
                    break;
            }
        }
        return true;
    }
}

?>