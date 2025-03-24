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

namespace DS\CbBuilder\FieldBuilder\Fields;

use DS\CbBuilder\FieldBuilder\FieldBuilder;
use DS\CbBuilder\Utility\CbPathUtility;
use DS\CbBuilder\Utility\ArrayParser;
use DS\CbBuilder\Utility\SimpleDatabaseQuery;
use Exception;

/**
 * Custom exception class for configuration errors.
 */
class ConfigException extends Exception {}

/**
 * Base class for field configurations.
 */
class Config
{
    /**
     * Behaviour settings.
     */
    protected array $behaviour = [];

    /**
     * Field control settings.
     */
    protected array $fieldControl = [];

    /**
     * Field information settings.
     */
    protected array $fieldInformation = [];

    /**
     * Field wizard settings.
     */
    protected array $fieldWizard = [];

    /**
     * Render type.
     */
    protected string $renderType = '';

    /**
     * Field type.
     */
    protected string $type = '';

    /**
     * Constants for data types.
     */
    const BOOL_TYPE = 1;
    const STRING_TYPE = 2;
    const INTEGER_TYPE = 3;
    const FLOAT_TYPE = 4;
    const FUNCTION = 5;
    const INTFLOAT_TYPE = 6;

    /**
     * Constants for scanner modes.
     */
    const SCANNER_MODE_FIELD = 1;
    const SCANNER_MODE_TABLE = 2;

    /**
     * Filter types.
     */
    const FILTER_TYPES = [
        'userFunc' => self::FUNCTION,
        'parameters' => self::FUNCTION
    ];

    /**
     * Search configuration keywords.
     */
    const SEARCH_CONFIG_KEYWORDS = [
        'pidonly' => self::BOOL_TYPE,
        'case' => self::BOOL_TYPE,
        'andWhere' => self::STRING_TYPE
    ];

    /**
     * Keywords for the fieldControl array
     */
    const FIELD_CONTROL_KEYWORDS = [
        'editPopup', 'addRecord', 'listModule'
    ];

    /**
     * Check if required configuration settings are present.
     * 
     * @param array $config The configuration array.
     * @param array $requirements The required settings.
     * @param string $type The field type.
     * 
     * @throws ConfigException If a required setting is missing.
     */
    protected function checkRequirements(array $config, array $requirements, string $type, ?string $configIdentifier = NULL): void
    {
        $configIdentifier = $config['identifier'] ?? $configIdentifier;
        foreach ($requirements as $requirement) {
            $_requirement = $config[$requirement] ?? NULL;
            if ($_requirement === NULL) {
                throw new ConfigException(
                    "'$type' field '$configIdentifier' configuration '$requirement' must be set."
                );
            }
        }
    }

    /**
     * Validate a table name.
     * 
     * @param mixed $table The table name.
     * @param array $config The configuration array.
     * @param string $setting The setting name.
     * @param string $type The field type.
     * 
     * @throws ConfigException If validation fails.
     */
    protected function validateTable(mixed $table, array $config, string $setting, string $type): void
    {
        $identifier = $config['identifier'];
        if (!is_string($table)) {
            throw new ConfigException(
                "'$type' field '$identifier' configuration '$setting' must be of type string."
            );
        }
        $sdq = new SimpleDatabaseQuery();
        if (
            !FieldBuilder::isSurpressedWarning(152082918)
            && !$sdq->tableExists($table)
            && !FieldBuilder::fieldExists($table, 'Collection')
            && !$this->_deepScanner($table, self::SCANNER_MODE_TABLE)
        ) {
            throw new ConfigException(
                "WARNING: '$type' field '$identifier' configuration '$setting' table '$table' does neither exist in the db nor " .
                "it will be created in this process.\n" .
                "You can surpress this warning in the cbConfig.yaml by adding the code 152082918 to surpressWarning."
            );
        }
    }

    /**
     * Validate a field name.
     * 
     * @param mixed $field The field name.
     * @param array $config The configuration array.
     * @param string $setting The setting name.
     * @param string $type The field type.
     * @param array $tables The tables to check.
     * 
     * @throws ConfigException If validation fails.
     */
    public function validateField(mixed $field, array $config, string $setting, string $type, array $tables = ['*']): void
    {
        $identifier = $config['identifier'];
        if (!is_string($field)) {
            throw new ConfigException(
                "'$type' field '$identifier' configuration '$setting' must be of type string."
            );
        }
        if (empty($tables)) {
            $tables[0] = '*';
        }
        $sdq = new SimpleDatabaseQuery();
        if (
                !FieldBuilder::isSurpressedWarning(152082917)
                && !$sdq->fieldExists($field, $tables)
                && !FieldBuilder::fieldExists($field, '', 'Pass, Palette')
        ) {
            if (!$GLOBALS['CbBuilder']['config']['deepFieldSearch'] || !$this->_deepScanner($field, self::SCANNER_MODE_FIELD, $tables)) {
                $msg = (isset($tables[0]) && $tables[0] === '*') ? "db" : "table(s) '" . implode(', ', $tables) . "'";
                throw new ConfigException(
                    "WARNING: '$type' field '$identifier' configuration '$setting' field '$field' does neither exist in the $msg nor " .
                    "it will be created in this process.\n" .
                    "You can surpress this warning in the cbConfig.yaml by adding the code 152082917 to surpressWarning."
                );
            }
        }
    }

    /**
     * Validate an array of string-string pairs.
     * 
     * @param mixed $array The array to validate.
     * @param array $config The configuration array.
     * @param string $setting The setting name.
     * @param string $type The field type.
     * 
     * @throws ConfigException If validation fails.
     */
    protected function validateArrayStringString(mixed $array, array $config, string $setting, string $type): void
    {
        $identifier = $config['identifier'];
        if (!is_array($array)) {
            throw new ConfigException(
                "'$type' field '$identifier' configuration '$setting' must be of type array."
            );
        }

        $i = 0;
        foreach ($array as $key => $value) {
            if (!is_string($key)) {
                throw new ConfigException(
                    "'$type' field '$identifier' configuration '$setting [$i]' key must be of type string."
                );
            }
            if (!is_string($value)) {
                throw new ConfigException(
                    "'$type' field '$identifier' configuration '$setting [$i]' value must be of type string."
                );
            }
            $i++;
        }
    }

    /**
     * Validate a user function.
     * 
     * @param mixed $entry The function to validate.
     * @param array $config The configuration array.
     * @param string $setting The setting name.
     * @param string $type The field type.
     * 
     * @throws ConfigException If validation fails.
     */
    protected function validateUserFunc(mixed $entry, array $config, string $setting, string $type): void
    {
        $identifier = $config['identifier'];
        if (!is_string($entry)) {
            throw new ConfigException(
                "'$type' field '$identifier' configuration '$setting' must be of type string."
            );
        }

        if (!str_contains($entry, '->')) {
            throw new ConfigException(
                "'$type' field '$identifier' configuration '$setting' must be in format " .
                "\Vendor\Extension\UserFunction\ClassName -> method."
            );
        }
    }

    /**
     * Validate the mode setting.
     * 
     * @param mixed $entry The mode value.
     * @param array $config The configuration array.
     * @param string $type The field type.
     * 
     * @throws ConfigException If validation fails.
     */
    protected function validateMode(mixed $entry, array $config, string $type): void
    {
        $identifier = $config['identifier'];

        if (!is_string($entry)) {
            throw new ConfigException(
                "'$type' field '$identifier' configuration 'mode' must be of type string."
            );
        }

        if ($entry !== 'useOrOverridePlaceholder') {
            throw new ConfigException(
                "'$type' field '$identifier' configuration 'mode' must contain the value 'useOrOverridePlaceholder'."
            );
        }
        if (!isset($config['placeholder'])) {
            throw new ConfigException(
                "'$type' field '$identifier' configuration 'mode' needs to have 'placeholder' set."
            );
        }
    }

    /**
     * Validate the 'autoSizeMax' configuration.
     * 
     * @param mixed $entry The 'autoSizeMax' value.
     * @param array $config The configuration array.
     * @param string $type The field type.
     * 
     * @throws ConfigException If validation fails.
     */
    protected function validateAutoSizeMax(mixed $entry, array $config, string $type): void
    {
        $identifier = $config['identifier'];
        if ($num = $this->handleIntegers($entry)) {
            if (!isset($config['maxitems'])) {
                throw new ConfigException(
                    "'$type' field '$identifier' configuration 'autoSizeMax' only takes effect when 'maxitems' is set to " .
                    "greater than 1."
                );
            }
            if (($maxitems = $this->handleIntegers($config['maxitems'])) === NULL) {
                throw new ConfigException(
                    "'$type' field '$identifier' configuration 'maxitems' must be of type integer or " .
                    "a string that represents an integer number."
                );
            }
            if ($maxitems < 2) {
                throw new ConfigException(
                    "'$type' field '$identifier' configuration 'autoSizeMax' only takes effect when 'maxitems' is set to " .
                    "a value greater than 1."
                );
            }
            if (isset($config['size'])) {
                if (($size = $this->handleIntegers($config['size'])) === NULL) {
                    throw new ConfigException(
                        "'$type' field '$identifier' configuration 'size' must be of type integer or " .
                        "a string that represents an integer number."
                    );
                }
                if ($num < $size) {
                    throw new ConfigException(
                        "'$type' field '$identifier' configuration 'autoSizeMax' must be greater or equal to 'size'."
                    );
                }
            }
        } else {
            throw new ConfigException(
                "'$type' field '$identifier' configuration 'autoSizeMax' must be of type integer or " .
                "a string that represents an integer number."
            );
        }
    }

    /**
     * Validate the 'MM_match_fields' configuration.
     * 
     * @param mixed $value The 'MM_match_fields' value.
     * @param array $config The configuration array.
     * @param string $type The field type.
     * 
     * @throws ConfigException If validation fails.
     */
    protected function validateMmMatchFields(mixed $value, array $config, string $type): void
    {
        $identifier = $config['identifier'];
        if (!is_array($value)) {
            throw new ConfigException(
                "'$type' field '$identifier' configuration 'MM_match_fields' must be of type array."
            );
        }

        try {
            $this->validateArrayStringString($value, $config, 'MM_match_fields', $type);
        } catch (\Throwable $th) {
            throw new ConfigException($th->getMessage() . "Fix:\nMM_match_fields:\n  fieldName1: 'fieldValue1'\n  fieldName2: 'fieldValue2'");
        }

        $i = 0;
        foreach ($value as $field => $unused) {
            $this->validateField($field, $config, "MM_match_fields[$i]", $type);
            $i++;
        }
    }

    /**
     * Validate the 'MM_opposite_field' configuration.
     * 
     * @param mixed $field The 'MM_opposite_field' value.
     * @param array $config The configuration array.
     * @param string $type The field type.
     * 
     * @throws ConfigException If validation fails.
     */
    protected function validateMmOppositeField(mixed $field, array $config, string $type): void
    {
        $identifier = $config['identifier'];
        if (!is_string($field)) {
            throw new ConfigException(
                "'$type' field '$identifier' configuration 'MM_opposite_field' must be of type string."
            );
        }
        if (!isset($config['foreign_table'])) {
            throw new ConfigException(
                "'$type' field '$identifier' configuration 'MM_opposite_field' needs 'foreign_table' to be set."
            );
        }
        $this->validateField($field, $config, 'MM_opposite_field', $type, [$config['foreign_table']]);
    }

    /**
     * Validate the 'MM_oppositeUsage' configuration.
     * 
     * @param mixed $value The 'MM_oppositeUsage' value.
     * @param array $config The configuration array.
     * @param string $type The field type.
     * 
     * @throws ConfigException If validation fails.
     */
    protected function validateMmOppositeUsage(mixed $value, array $config, string $type): void
    {
        $identifier = $config['identifier'];
        if (!is_array($value)) {
            throw new ConfigException(
                "'$type' field '$identifier' configuration 'MM_oppositeUsage' must be of type array." .
                "Fix:\nMM_oppositeUsage:\n  tableName1:\n    - 'fieldName1'\n    - 'fieldName2'\n  tableName2:\n    - 'fieldName1'"
            );
        }

        foreach ($value as $table => $fields) {
            $this->validateTable($table, $config, "MM_oppositeUsage['$table']", $type);
            if (!is_array($fields)) {
                throw new ConfigException(
                    "'$type' field '$identifier' configuration 'MM_oppositeUsage['$table']' value must be of type array." .
                    "Fix:\nMM_oppositeUsage:\n  tableName1:\n    - 'fieldName1'\n    - 'fieldName2'\n  tableName2:\n    - 'fieldName1'"
                );
            }
            $i = 0;
            foreach ($fields as $field) {
                $this->validateField($field, $config, "MM_oppositeUsage['$table'][$i]", $type, [$table]);
                $i++;
            }
        }
    }

    /**
     * Validate the 'search' configuration.
     * 
     * @param mixed $entry The 'search' value.
     * @param string $identifier The identifier.
     * @param string $type The field type.
     * 
     * @throws ConfigException If validation fails.
     */
    protected function validateSearch(mixed $entry, string $identifier, string $type): void
    {
        if (!is_array($entry)) {
            throw new ConfigException(
                "'$type' field '$identifier' configuration 'search' must be of type array.\n" .
                "Fix:\nfilter:\n  pidonly: true\n  case: true\n  andWhere: \"{#field1}='value1' AND {#field2}=43\""
            );
        }

        $i = 0;
        foreach ($entry as $key => $value) {
            if (!in_array($key, array_keys(self::SEARCH_CONFIG_KEYWORDS))) {
                throw new ConfigException(
                    "'$type' field '$identifier' configuration 'search[$i]' invalid key '$key'. Valid keys are: " .
                    implode(', ', array_keys(self::SEARCH_CONFIG_KEYWORDS))
                );
            }

            switch (self::SEARCH_CONFIG_KEYWORDS[$key]) {
                case self::BOOL_TYPE:
                    if (!is_bool($value)) {
                        throw new ConfigException(
                            "'$type' field '$identifier' configuration 'search[$key]' value must be of type boolean."
                        );
                    }
                    break;
                case self::STRING_TYPE:
                    if (!is_string($value)) {
                        throw new ConfigException(
                            "'$type' field '$identifier' configuration 'search[$key]' value must be of type string."
                        );
                    }
                    break;
            }
            $i++;
        }
    }

    /**
     * Validate the 'softref' configuration.
     * 
     * @param mixed $entry The 'softref' value.
     * @param array $config The configuration array.
     * @param string $type The field type.
     * 
     * @throws ConfigException If validation fails.
     */
    protected function validateSoftRef(mixed $entry, array $config, string $type): void
    {
        $identifier = $config['identifier'];
        if (!is_string($entry)) {
            throw new ConfigException(
                "'$type' field '$identifier' configuration 'softref' must be of type string."
            );
        }
        $matches = [];
        preg_match("/\w+(\\[\w+(;\w+)*\\])?(,\w+(\\[\w+(;\w+)*\\])?)*/", $entry, $matches);
        if ($matches[0] !== $entry) {
            throw new ConfigException(
                "'$type' field '$identifier' configuration 'softref': Error in syntax. Syntax should look like: " .
                "key1,key2[parameter1;parameter2;...],..."
            );
        }
    }

    /**
     * Validate the 'filter' configuration.
     * 
     * @param mixed $entry The 'filter' value.
     * @param array $config The configuration array.
     * @param string $type The field type.
     * 
     * @throws ConfigException If validation fails.
     */
    protected function validateFilter(mixed $entry, array $config, string $type): void
    {
        $identifier = $config['identifier'];
        if (!is_array($entry)) {
            throw new ConfigException(
                "'$type' field '$identifier' configuration 'filter' must be of type array.\n" .
                "Fix:\nfilter:\n  -\n    userFunc: '\Vendor\Extension\UserFunction\ClassName -> method'\n    parameters:\n" .
                "      key1: 'value1'\n      key2: value2"
            );
        }
        $i = 0;
        foreach ($entry as $value) {
            if (!is_array($value)) {
                throw new ConfigException(
                    "'$type' field '$identifier' configuration 'filter[$i]' must be of type array.\n" .
                    "Fix:\nfilter:\n  -\n    userFunc: '\Vendor\Extension\UserFunction\ClassName -> method'\n    parameters:\n" .
                    "      key1: 'value1'\n      key2: value2"
                );
            }
            $j = 0;
            foreach ($value as $key => $filter) {
                if (!key_exists($key, self::FILTER_TYPES)) {
                    throw new ConfigException(
                        "'$type' field '$identifier' configuration 'filter[$i][$j]' key '$key' is no valid keyword.\n" .
                        "Valid keywords are: " . implode(',', array_keys(self::FILTER_TYPES))
                    );
                }
                switch (self::FILTER_TYPES[$key]) {
                    case self::FUNCTION:
                        $function = "_validateConfig_" . $key;
                        call_user_func([$this, $function], $filter, $config, "filter[$i][$key]");
                        break;
                }
                $j++;
            }
            $i++;
        }
    }

    /**
     * Validate a keyword against a list of valid keywords.
     * 
     * @param mixed $entry The keyword to validate.
     * @param array $keywords The list of valid keywords.
     * @param string $identifier The identifier.
     * @param string $setting The setting name.
     * @param string $type The field type.
     * 
     * @throws ConfigException If validation fails.
     */
    protected function validateKeyword(mixed $entry, array $keywords, string $identifier, string $setting, string $type): void
    {
        if (!is_string($entry)) {
            throw new ConfigException(
                "'$type' field '$identifier' configuration '$setting' must be of type string.\n"
            );
        }

        if (!in_array($entry, $keywords)) {
            throw new ConfigException(
                "'$type' field '$identifier' configuration '$setting': '$entry' is an invalid keyword. Valid keywords are: " .
                implode(', ', $keywords)
            );
        }
    }

    /**
     * Validate an integer value within a range.
     * 
     * @param mixed $entry The integer value.
     * @param array $config The configuration array.
     * @param string $setting The setting name.
     * @param string $type The field type.
     * @param int $min The minimum value.
     * @param int $max The maximum value.
     * @param bool|null $isMinMax Whether to check against another setting.
     * @param bool|null $isMax Whether to check for maximum.
     * @param string|null $minOrMaxIdentifier The identifier of the setting to compare against.
     * 
     * @throws ConfigException If validation fails.
     */
    protected function validateInteger(
        mixed $entry,
        array $config,
        string $setting,
        string $type,
        int $min,
        int $max,
        ?bool $isMinMax = false,
        ?bool $isMax = false,
        ?string $minOrMaxIdentifier = ''
    ): void
    {
        $identifier = $config['identifier'];
        if (($num = $this->handleIntegers($entry)) !== NULL) {
            if ($num < $min || $num > $max) {
                if ($num < $min) {
                    $min--;
                    throw new ConfigException(
                        "'$type' field '$identifier' configuration '$setting' must be an integer greater than $min."
                    );
                } else {
                    $max++;
                    throw new ConfigException(
                        "'$type' field '$identifier' configuration '$setting' must be an integer smaller than $max."
                    );
                }
            }
        } else {
            throw new ConfigException(
                "'$type' field '$identifier' configuration '$setting' must be of type integer or " .
                "a string that represents an integer number. '$setting' must be in a range between $min and $max."
            );
        }

        if ($isMinMax && $minOrMaxIdentifier !== '' && isset($config[$minOrMaxIdentifier])) {
            $minOrMax = $config[$minOrMaxIdentifier];
            if (($minOrMax = $this->handleIntegers($minOrMax)) === NULL) {
                throw new ConfigException(
                    "'$type' field '$identifier' configuration '$minOrMaxIdentifier' must be of type integer or " .
                    "a string that represents an integer number."
                );
            }
            if ($isMax) {
                if ($num < $minOrMax) {
                    throw new ConfigException(
                        "'$type' field '$identifier' configuration '$setting' must be greater than or equal to '$minOrMaxIdentifier'."
                    );
                }
            } else {
                if ($num > $minOrMax) {
                    throw new ConfigException(
                        "'$type' field '$identifier' configuration '$setting' must be lesser than or equal to '$minOrMaxIdentifier'."
                    );
                }
            }
        }
    }

    /**
     * Perform a deep scan for a table or field.
     * 
     * @param string $needle The table or field to scan for.
     * @param int $SCANNER_MODE The scanner mode (table or field).
     * @param array $tables The tables to check.
     * 
     * @return bool Whether the table or field is found.
     */
    private function _deepScanner(string $needle, int $SCANNER_MODE, array $tables = ['*']): bool
    {
        $extensions = CbPathUtility::scanExtensionFolder();
        foreach ($extensions as $extension) {
            $directoryIterator = CbPathUtility::getConfiguration($extension);
            $files = [];
            if ($directoryIterator !== NULL) {
                $files = CbPathUtility::scanForPhpFiles($directoryIterator);
            }
            $directoryIterator = CbPathUtility::getOverride($extension);
            if ($directoryIterator !== NULL) {
                $files = array_merge($files, CbPathUtility::scanForPhpFiles($directoryIterator));
            }
            if (!empty($files)) {
                foreach ($files as $file) {
                    $tableName = explode('/', $file);
                    $tableName = $tableName[(count($tableName) - 1)];
                    $tableName = explode('.', $tableName)[0];
                    $arrays = ArrayParser::extractArraysFromFile($file, $GLOBALS['CbBuilder']['config']['useEval']);
                    foreach ($arrays as $array) {
                        if (array_key_exists('columns', $array)) {
                            if ($SCANNER_MODE === self::SCANNER_MODE_TABLE) {
                                if ($needle === $tableName) {
                                    return true;
                                } else {
                                    return false;
                                }
                            }
                            foreach ($array['columns'] as $fieldName => $unused) {
                                if ($fieldName === $needle) {
                                    if (in_array('*', $tables)) {
                                        return true;
                                    } else if (in_array($tableName, $tables)) {
                                        return true;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    /**
     * Check if a table exists.
     * 
     * @param string $table The table name.
     * @param array $config The configuration array.
     * 
     * @return bool Whether the table exists.
     */
    protected function tableExists(string $table, array $config): bool
    {
        $sdq = new SimpleDatabaseQuery();
        if (!$sdq->tableExists($table)) {
            $fields = $GLOBALS['CbBuilder']['fields'];
            foreach ($fields as $value) {
                if (isset($value['type']) && $value['type'] === 'Collection') {
                    if (isset($value['identifier']) && $value['identifier'] === $table) {
                        return true;
                    }
                }
            }
        } else {
            return true;
        }
        return false;
    }

    /**
     * Handle an integer value, sanitizing it if necessary.
     * 
     * @param mixed $integer The integer value to handle.
     * 
     * @return int|null The sanitized integer value or null if invalid.
     */
    protected function handleIntegers(mixed $integer): int|null
    {
        if (is_int($integer)) return $integer;
        if (is_float($integer)) return intval($integer);
        if ($GLOBALS['CbBuilder']['config']['autoSanitizeInteger'] === false) {
            if (!is_numeric($integer) || (string)(int)$integer !== $integer) {
                return NULL;
            }
        }
        $integer = filter_var($integer, FILTER_SANITIZE_NUMBER_INT);
        return $integer !== false ? intval($integer) : NULL;
    }

    /**
     * Handle a float value, sanitizing it if necessary.
     * 
     * @param mixed $float The float value to handle.
     * 
     * @return float|null The sanitized float value or null if invalid.
     */
    protected function handleFloats(mixed $float): float|null
    {
        if (is_int($float)) return floatval($float);
        if (is_float($float)) return $float;
        if ($GLOBALS['CbBuilder']['config']['autoSanitizeInteger'] === false) {
            if (!is_numeric($float) || (string)(float)$float !== $float) {
                return NULL;
            }
        }
        $float = filter_var($float, FILTER_SANITIZE_NUMBER_FLOAT);
        return $float !== false ? floatval($float) : NULL;
    }

    /**
     * Check if a configuration is valid for a specific type.
     * 
     * @param array $allowed The allowed configurations.
     * @param string $config The configuration to check.
     * @param string $type The field type.
     * 
     * @return bool Whether the configuration is valid.
     */
    protected function isValidLeveledConfig(array $allowed, string $config, string $type): bool
    {
        if (array_key_exists($config, $allowed) && (in_array('all', $allowed[$config]) || in_array($type, $allowed[$config]))) {
            return true;
        }
        return false;
    }

    /**
     * Check if a configuration property is valid.
     * 
     * @param array $properties The properties to check against.
     * @param string $config The configuration property.
     * 
     * @return bool Whether the property is valid.
     */
    protected function isValidConfig(array $properties, string $config): bool
    {
        return array_key_exists($config, $properties);
    }

    /**
     * Validate a keyword and its type against a validation map.
     * 
     * @param string $keyword The keyword to validate.
     * @param string $identifier The identifier.
     * @param mixed $item The item to validate.
     * @param string $setting The setting name.
     * @param string $type The field type.
     * @param array $validationMap The validation map.
     * 
     * @throws ConfigException If validation fails.
     */
    protected function validateKeywordAndType(
        string $keyword,
        string $identifier,
        mixed $item,
        string $setting,
        string $type,
        array $validationMap
    ): void
    {
        if (!in_array($keyword, array_keys($validationMap))) {
            throw new ConfigException(
                "'$type' field '$identifier' configuration '$setting' invalid key '$keyword'. Valid keys are: " .
                implode(', ', array_keys($validationMap))
            );
        }

        switch ($validationMap[$keyword]) {
            case self::BOOL_TYPE:
                if (!is_bool($item)) {
                    throw new ConfigException(
                        "'$type' field '$identifier' configuration '" . $setting . "[$keyword]' value must be of type boolean."
                    );
                }
                break;
            case self::STRING_TYPE:
                if (!is_string($item)) {
                    throw new ConfigException(
                        "'$type' field '$identifier' configuration '" . $setting . "[$keyword]' value must be of type string."
                    );
                }
                break;
            case self::FLOAT_TYPE:
                if (!is_float($item)) {
                    throw new ConfigException(
                        "'$type' field '$identifier' configuration '" . $setting . "[$keyword]' value must be of type float."
                    );
                }
                break;
            case self::INTEGER_TYPE:
                if ($this->handleIntegers($item) === NULL) {
                    throw new ConfigException(
                        "'$type' field '$identifier' configuration '" . $setting . "[$keyword]' value must be of type integer."
                    );
                }
                break;
        }
    }

    /**
     * Convert the object's configuration to an array for element creation.
     * 
     * @param string $type The type of the element.
     * @param array $properties The properties to convert.
     * 
     * @return array The converted array.
     */
    protected function _configToElement(string $type, array $properties): array
    {
        $config = [
            'type' => $type
        ];
        foreach ($properties as $property => $value) {
            if (is_string($value)) {
                if ($value != '') $config[$property] = $value;
            } else if (is_numeric($value)) {
                if ($value >= 0) $config[$property] = $value;
            } else if (is_array($value)) {
                if (!empty($value)) $config[$property] = $value;
            } else $config[$property] = $value;
        }
        return $config;
    }

    /**
     * Set the behaviour settings for the field.
     * 
     * @param array $behaviour The behaviour settings.
     * @param string $type The field type.
     */
    protected function setBehaviour(array $behaviour, string $type): void
    {
        $allowed = [
            'allowLanguageSynchronization' => [
                'all'
            ]
        ];
        foreach ($behaviour as $config => $value) {
            if ($this->isValidLeveledConfig($allowed, $config, $type)) {
                $this->behaviour[$config] = $value;
            }
        }
    }

    /**
     * Get the behaviour settings.
     * 
     * @return array The behaviour settings.
     */
    public function getBehaviour(): array
    {
        return $this->behaviour;
    }

    /**
     * Recursively check an array for objects and convert them if necessary.
     * 
     * @param array &$array The array to check.
     * @param array $exclude Properties to exclude from conversion.
     */
    private function _arrayHasObjects(array &$array, array $exclude): void
    {
        foreach ($array as $key => &$value) {
            if (is_object($value)) {
                if ($value instanceof Config) {
                    if (!in_array('type', $exclude)) {
                        $exclude[] = 'type';
                    }
                    $value = $value->parseConfig('', $exclude);
                } else {
                    throw new ConfigException('Unknown instance of an object');
                }
            } else if (is_array($value)) {
                $this->_arrayHasObjects($value, $exclude);
            }
        }
    }

    public function arrayToConfig(array $config, array $fieldProperties, ?array $globalConf = NULL, mixed $misc = NULL): void { return; }

    const EXCLUDE_MAP = [
        'DS\CbBuilder\FieldBuilder\Fields\GroupFieldSuggestOptionsConfig' => 'key'
    ];

    const SPECIAL_CASES_MAP = [
        'format_' => 'format.'
    ];

    /**
     * Parse the object's configuration to an array.
     * 
     * @param string $type The type of the field.
     * @param array|null $exclude Properties to exclude from the parsed array.
     * 
     * @return array The parsed configuration array.
     */
    public function parseConfig(string $type, ?array $exclude = [], bool $convertType = true): array
    {
        $array = [];
        if ($type !== '') $array['type'] = $type;
        $properties = array_keys(get_object_vars($this));
        foreach ($properties as $property) {
            switch ($property) {
                case 'identifier':
                    break;
                case 'type':
                    if (!in_array('type', $exclude) && $convertType) {
                        $array['type'] = FieldBuilder::convertTypeFieldToColumn($this->$property);
                    }
                    break;
                default:
                    if (
                        ((is_string($this->$property) && $this->$property !== '')
                        || (is_int($this->$property) && $this->$property >= 0)
                        || (is_float($this->$property) && $this->$property >= 0.0)
                        || (is_array($this->$property) && !empty($this->$property))
                        || (is_bool($this->$property) && $this->$property !== NULL))
                        && !in_array($property, $exclude)
                        && (
                            !array_key_exists(get_class($this), self::EXCLUDE_MAP)
                            || (array_key_exists(get_class($this), self::EXCLUDE_MAP) && self::EXCLUDE_MAP[get_class($this)] !== $property)
                            )
                    ) {
                        /**
                         * Handle special cases like the configuration format. of the none field.
                         * Must internally be stored as format_ because $format. is not a valid variable declaration in PHP.
                        */
                        if (array_key_exists($property, self::SPECIAL_CASES_MAP)) {
                            if (self::SPECIAL_CASES_MAP[$property]) {
                                if (!is_array(self::SPECIAL_CASES_MAP[$property])) {
                                    $array[self::SPECIAL_CASES_MAP[$property]] = $this->$property;
                                    continue 2;
                                }
                            }
                        }
                        /**
                         * Check for other objects of the Config in this property.
                         */
                        if (is_array($this->$property)) {
                            $this->_arrayHasObjects($this->$property, $exclude);
                            $array[$property] = $this->$property;
                        } else {
                            $array[$property] = $this->$property;
                        }
                    } else if (is_object($this->$property) && $this->$property instanceof Config) {
                        if (!in_array('type', $exclude)) {
                            $exclude[] = 'type';
                        }
                        $array[$property] = $this->$property->parseConfig('', $exclude);
                    }
                    break;
            }
        }
        return $array;
    }

    public function mergeConfig(Config $foreign): void { return; }

    public function addGenericConfig(string $key, mixed $config, array $globalConf, Config $instance, ?array $fieldProperties = NULL): void
    {
        switch ($key) {
            case 'fieldControl':
                foreach ($config as $setting => $value) {
                    if (in_array($setting, self::FIELD_CONTROL_KEYWORDS)) {
                        $this->fieldControl[$setting] = $value;
                        unset($config[$setting]);
                    }
                }
                if ($globalConf && $fieldProperties) {
                    $fieldControlConfig = new $instance($globalConf, '');
                    $fieldControlConfig->arrayToConfig($config, $fieldProperties, $globalConf);
                    $this->fieldControl = array_merge($this->fieldControl, $fieldControlConfig->parseConfig($globalConf['type'], [], false));
                    unset($this->fieldControl['type']);
                }
                
                break;
            default:
                $this->$key = $config;
                break;
        }
    }

    /**
     * Get the render type.
     * 
     * @return string The render type.
     */
    public function getRenderType(): string
    {
        return $this->renderType;
    }

    /**
     * Set the render type.
     * 
     * @param string $renderType The render type to set.
     * 
     * @return self The instance itself for chaining.
     */
    public function setRenderType(string $renderType): self
    {
        $this->renderType = $renderType;

        return $this;
    }

    /**
     * Get the field control.
     * 
     * @return array The field control.
     */
    public function getFieldControl(): array
    {
        return $this->fieldControl;
    }

    /**
     * Set the field control.
     * 
     * @param array $renderType The field control to set.
     * 
     * @return self The instance itself for chaining.
     */
    public function setFieldControl(array $fieldControl): self
    {
        $this->fieldControl = $fieldControl;

        return $this;
    }

    /**
     * Get the field information.
     * 
     * @return array The field information.
     */
    public function getFieldInformation(): array
    {
        return $this->fieldInformation;
    }

    /**
     * Set the field information.
     * 
     * @param array $renderType The field information to set.
     * 
     * @return self The instance itself for chaining.
     */
    public function setFieldInformation(array $fieldInformation): self
    {
        $this->fieldInformation = $fieldInformation;

        return $this;
    }

    /**
     * Get the field wizard.
     * 
     * @return array The field wizard.
     */
    public function getFieldWizard(): array
    {
        return $this->fieldWizard;
    }

    /**
     * Set the field wizard.
     * 
     * @param array $renderType The field wizard to set.
     * 
     * @return self The instance itself for chaining.
     */
    public function setFieldWizard(array $fieldWizard): self
    {
        $this->fieldWizard = $fieldWizard;

        return $this;
    }

    /**
     * Merge the main configuration from another Config instance.
     * 
     * @param Config $foreign The configuration to merge.
     */
    public function mergeMainConfig(Config $foreign): void
    {
        $this->fieldControl = $foreign->getFieldControl();
        $this->fieldInformation = $foreign->getFieldInformation();
        $this->fieldWizard = $foreign->getFieldWizard();
        $this->renderType = $foreign->getRenderType();
    }
}