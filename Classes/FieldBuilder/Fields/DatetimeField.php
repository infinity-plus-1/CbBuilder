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

use Exception;
use InvalidArgumentException;

/**
 * Class DatetimeFieldConfig
 * 
 * Represents the configuration for a datetime field.
 */
final class DatetimeFieldConfig extends Config
{
    /**
     * The database type for this field.
     */
    protected string $dbType = '';

    /**
     * The default value for this field.
     */
    protected string|int $default = '';

    /**
     * Whether to disable age display.
     */
    protected ?bool $disableAgeDisplay = NULL;

    /**
     * The format for displaying dates.
     */
    protected string $format = '';

    /**
     * The mode for this field.
     */
    protected string $mode = '';

    /**
     * Whether this field is nullable.
     */
    protected ?bool $nullable = NULL;

    /**
     * The placeholder text for this field.
     */
    protected string|int $placeholder = '';

    /**
     * The range for this field.
     */
    protected array $range = [];

    /**
     * Whether this field is read-only.
     */
    protected ?bool $readOnly = NULL;

    /**
     * The search configuration for this field.
     */
    protected array $search = [];

    /**
     * The soft reference for this field.
     */
    protected string $softref = '';

    /**
     * Get the database type.
     * 
     * @return string The database type.
     */
    public function getDbType(): string
    {
        return $this->dbType;
    }

    /**
     * Get the default value.
     * 
     * @return string|int The default value.
     */
    public function getDefault(): string|int
    {
        return $this->default;
    }

    /**
     * Check if age display is disabled.
     * 
     * @return bool|null Whether age display is disabled.
     */
    public function isDisableAgeDisplay(): ?bool
    {
        return $this->disableAgeDisplay;
    }

    /**
     * Get the format for displaying dates.
     * 
     * @return string The format.
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * Get the mode for this field.
     * 
     * @return string The mode.
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * Check if this field is nullable.
     * 
     * @return bool|null Whether this field is nullable.
     */
    public function isNullable(): ?bool
    {
        return $this->nullable;
    }

    /**
     * Get the placeholder text.
     * 
     * @return string|int The placeholder text.
     */
    public function getPlaceholder(): string|int
    {
        return $this->placeholder;
    }

    /**
     * Get the range for this field.
     * 
     * @return array The range.
     */
    public function getRange(): array
    {
        return $this->range;
    }

    /**
     * Check if this field is read-only.
     * 
     * @return bool|null Whether this field is read-only.
     */
    public function isReadOnly(): ?bool
    {
        return $this->readOnly;
    }

    /**
     * Get the search configuration.
     * 
     * @return array The search configuration.
     */
    public function getSearch(): array
    {
        return $this->search;
    }

    /**
     * Get the soft reference.
     * 
     * @return string The soft reference.
     */
    public function getSoftref(): string
    {
        return $this->softref;
    }

    /**
     * Set the database type.
     * 
     * @param string $dbType The database type to set.
     */
    public function setDbType(string $dbType): void
    {
        $this->dbType = $dbType;
    }

    /**
     * Set the default value.
     * 
     * @param string|int $default The default value to set.
     */
    public function setDefault(string|int $default): void
    {
        $this->default = $default;
    }

    /**
     * Set whether to disable age display.
     * 
     * @param bool|null $disableAgeDisplay Whether to disable age display.
     */
    public function setDisableAgeDisplay(?bool $disableAgeDisplay): void
    {
        $this->disableAgeDisplay = $disableAgeDisplay;
    }

    /**
     * Set the format for displaying dates.
     * 
     * @param string $format The format to set.
     */
    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    /**
     * Set the mode for this field.
     * 
     * @param string $mode The mode to set.
     */
    public function setMode(string $mode): void
    {
        $this->mode = $mode;
    }

    /**
     * Set whether this field is nullable.
     * 
     * @param bool|null $nullable Whether this field is nullable.
     */
    public function setNullable(?bool $nullable): void
    {
        $this->nullable = $nullable;
    }

    /**
     * Set the placeholder text.
     * 
     * @param string|int $placeholder The placeholder text to set.
     */
    public function setPlaceholder(string|int $placeholder): void
    {
        $this->placeholder = $placeholder;
    }

    /**
     * Set the range for this field.
     * 
     * @param array $range The range to set.
     */
    public function setRange(array $range): void
    {
        $this->range = $range;
    }

    /**
     * Set whether this field is read-only.
     * 
     * @param bool|null $readOnly Whether this field is read-only.
     */
    public function setReadOnly(?bool $readOnly): void
    {
        $this->readOnly = $readOnly;
    }

    /**
     * Set the search configuration.
     * 
     * @param array $search The search configuration to set.
     */
    public function setSearch(array $search): void
    {
        $this->search = $search;
    }

    /**
     * Set the soft reference.
     * 
     * @param string $softref The soft reference to set.
     */
    public function setSoftref(string $softref): void
    {
        $this->softref = $softref;
    }

    /**
     * Merge the configuration from another DatetimeFieldConfig instance.
     * 
     * @param self $foreign The configuration to merge.
     */
    public function mergeConfig(Config $foreign): void
    {
        if (!$foreign instanceof self) {
            throw new InvalidArgumentException (
                "Config 'foreign' must be of type " . get_class($this)
            );
        }
        $this->mergeMainConfig($foreign);

        if ($foreign->getDbType() !== '') {
            $this->dbType = $foreign->getDbType();
        }

        if ($foreign->getDefault() !== '') {
            $this->default = $foreign->getDefault();
        }

        if ($foreign->isDisableAgeDisplay() !== null) {
            $this->disableAgeDisplay = $foreign->isDisableAgeDisplay();
        }

        if ($foreign->getFormat() !== '') {
            $this->format = $foreign->getFormat();
        }

        if ($foreign->getMode() !== '') {
            $this->mode = $foreign->getMode();
        }

        if ($foreign->isNullable() !== null) {
            $this->nullable = $foreign->isNullable();
        }

        if ($foreign->getPlaceholder() !== '') {
            $this->placeholder = $foreign->getPlaceholder();
        }

        if (!empty($foreign->getRange())) {
            $this->range = $foreign->getRange();
        }

        if ($foreign->isReadOnly() !== null) {
            $this->readOnly = $foreign->isReadOnly();
        }

        if (!empty($foreign->getSearch())) {
            $this->search = $foreign->getSearch();
        }

        if ($foreign->getSoftref() !== '') {
            $this->softref = $foreign->getSoftref();
        }
    }

    /**
     * Valid database types for datetime fields.
     */
    const DB_TYPES = [
        'date', 'time', 'datetime'
    ];

    /**
     * Valid formats for datetime fields.
     */
    const FORMATS = [
        'date', 'time', 'datetime', 'timesec'
    ];

    /**
     * Valid keys for the range configuration.
     */
    const RANGE_KEYS = [
        'lower', 'upper'
    ];

    /**
     * Validate a date string.
     * 
     * @param string $datetime The date string to validate.
     * @param array $config The configuration array.
     * @param string $identifier The identifier of the field.
     * @param string $setting The configuration key.
     * 
     * @throws Exception If the date format is invalid.
     */
    private function _validateDate(string $datetime, array $config, string $identifier, string $setting): void
    {
        $match = [];
        preg_match(
            "/(\d{4}-\d{2}-\d{2}|\d{4}\/\d{2}\/\d{2}|\d{4}\\.\d{2}\\.\d{2}|\d{2}-\d{2}-\d{4}|\d{2}\/\d{2}\/\d{4}|\d{2}\\.\d{2}\\.\d{4})(?:\s\d{2}:\d{2}:\d{2})?/", $datetime, $match
        );

        if ($match[0] !== $datetime) {
            try {
                $this->validateInteger($datetime, $config, $setting, 'Datetime', 0, PHP_INT_MAX);
            } catch (\Throwable $th) {
                throw new Exception(
                    "'Datetime' field '$identifier' configuration '$setting' must be of type string or integer (unix ts). Fix:\n" .
                    "Valid datetime formats are:\n" .
                    "'YYYY/MM/DD HH:MM:SS', 'YYYY/MM/DD', 'YYYY-MM-DD HH:MM:SS', 'YYYY-MM-DD',\n" .
                    "'YYYY.MM.DD HH:MM:SS', 'YYYY.MM.DD', 'DD/MM/YYYY HH:MM:SS', 'DD/MM/YYYY',\n" .
                    "'DD-MM-YYYY HH:MM:SS', 'DD-MM-YYYY', 'DD.MM.YYYY HH:MM:SS' and 'DD.MM.YYYY',\n" .
                    "or any (string-)integer number representing an unix timestamp."
                );
            }
        }
    }

    /**
     * Convert a datetime string to a Unix timestamp.
     * 
     * @param string $datetime The datetime string.
     * @param array $config The configuration array.
     * @param string $setting The configuration key.
     * 
     * @return int The Unix timestamp.
     * 
     * @throws Exception If the datetime format is invalid.
     */
    private function _convertToUnix(string $datetime, array $config, string $setting): int
    {
        $identifier = $config['identifier'];

        $datetime = explode(' ', $datetime);

        $count = count($datetime);
        if ($count !== 1 && $count !== 2) {
            throw new Exception(
                "'Datetime' field '$identifier' configuration '$setting' must be of type string or integer (unix ts). Fix:\n" .
                "Valid datetime formats are:\n" .
                "'YYYY/MM/DD HH:MM:SS', 'YYYY/MM/DD', 'YYYY-MM-DD HH:MM:SS', 'YYYY-MM-DD',\n" .
                "'YYYY.MM.DD HH:MM:SS', 'YYYY.MM.DD', 'DD/MM/YYYY HH:MM:SS', 'DD/MM/YYYY',\n" .
                "'DD-MM-YYYY HH:MM:SS', 'DD-MM-YYYY', 'DD.MM.YYYY HH:MM:SS' and 'DD.MM.YYYY',\n" .
                "or any (string-)integer number representing an unix timestamp."
            );
        }
        $date = $datetime[0];
        $time = $count === 2 ? $datetime[1] : '';

        $hour = $min = $sec = 0;
        if ($time !== '') {
            $time = explode(':', $time);
            if (count($time) !== 3) {
                throw new Exception(
                    "'Datetime' field '$identifier' configuration '$setting' time must be in the format HH:MM:SS."
                );
            }
            $hour = intval($time[0]);
            $min = intval($time[1]);
            $sec = intval($time[2]);
        }

        $year = $mon = $day = 0;
        $sep = str_contains($date, '/') ? '/' : (str_contains($date, '-') ? '-' : '.');
        $date = explode($sep, $date);

        if (count($date) !== 3) {
            throw new Exception(
                "'Datetime' field '$identifier' configuration '$setting' date must be in the format YYYY-MM-DD or similar."
            );
        }

        $year = strlen($date[0]) === 4 ? intval($date[0]) : (strlen($date[2]) === 4 ? intval($date[2]) : NULL);
        if ($year === NULL) {
            throw new Exception(
                "'Datetime' field '$identifier' configuration '$setting' year must be in the format YYYY."
            );
        }
        $mon = intval($date[1]);
        $day = strlen($date[2]) === 2 ? intval($date[2]) : (strlen($date[0]) === 2 ? intval($date[0]) : NULL);
        if ($day === NULL) {
            throw new Exception(
                "'Datetime' field '$identifier' configuration '$setting' day must be in the format DD."
            );
        }

        return gmmktime($hour, $min, $sec, $mon, $day, $year);
    }

    /**
     * Validate the database type.
     * 
     * @param mixed $entry The database type to validate.
     * @param array $config The configuration array.
     * 
     * @throws Exception If the database type is invalid.
     */
    private function _validateDbType(mixed $entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_string($entry)) {
            throw new Exception(
                "'Datetime' field '$identifier' configuration 'dbType' must be of type string."
            );
        }
        if (!in_array($entry, self::DB_TYPES)) {
            throw new Exception(
                "'Datetime' field '$identifier' configuration 'dbType' must be one of these keywords: " .
                implode(', ', self::DB_TYPES)
            );
        }
    }

    /**
     * Validate the default or placeholder value.
     * 
     * @param mixed $entry The value to validate.
     * @param array $config The configuration array.
     * @param string $setting The configuration key.
     * 
     * @throws Exception If the value is invalid.
     */
    private function _validateDefaultOrPlaceholder(mixed $entry, array $config, string $setting): void
    {
        $identifier = $config['identifier'];
        if (!is_string($entry) && !is_int($entry)) {
            throw new Exception(
                "'Datetime' field '$identifier' configuration '$setting' must be of type string or integer (unix ts). Fix:\n" .
                "Valid datetime formats are:\n" .
                "'YYYY/MM/DD HH:MM:SS', 'YYYY/MM/DD', 'YYYY-MM-DD HH:MM:SS', 'YYYY-MM-DD',\n" .
                "'YYYY.MM.DD HH:MM:SS', 'YYYY.MM.DD', 'DD/MM/YYYY HH:MM:SS', 'DD/MM/YYYY',\n" .
                "'DD-MM-YYYY HH:MM:SS', 'DD-MM-YYYY', 'DD.MM.YYYY HH:MM:SS' and 'DD.MM.YYYY',\n" .
                "or any (string-)integer number representing an unix timestamp."
            );
        }

        if (is_string($entry)) {
            $this->_validateDate($entry, $config, $identifier, 'default');
        }
    }

    /**
     * Validate the format.
     * 
     * @param mixed $entry The format to validate.
     * @param array $config The configuration array.
     * 
     * @throws Exception If the format is invalid.
     */
    private function _validateFormat(mixed $entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_string($entry)) {
            throw new Exception(
                "'Datetime' field '$identifier' configuration 'format' must be of type string."
            );
        }

        if (!isset($config['readOnly']) || !$config['readOnly']) {
            throw new Exception(
                "'Datetime' field '$identifier' configuration 'format' takes only effect if the config 'readOnly' is set to true. " .
                "Fix: Omit the format or set ['config']['readOnly'] to true."
            );
        }

        if (!in_array($entry, self::FORMATS)) {
            throw new Exception(
                "'Datetime' field '$identifier' configuration 'format' must be one of these keywords: " .
                implode(', ', self::FORMATS)
            );
        }
    }

    /**
     * Validate the mode.
     * 
     * @param mixed $entry The mode to validate.
     * @param array $config The configuration array.
     * 
     * @throws Exception If the mode is invalid.
     */
    private function _validateMode(mixed $entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_string($entry)) {
            throw new Exception(
                "'Datetime' field '$identifier' configuration 'mode' must be of type string."
            );
        }
        if ('useOrOverridePlaceholder' !== $entry) {
            throw new Exception(
                "'Datetime' field '$identifier' configuration 'mode' must contain the keyword 'useOrOverridePlaceholder'."
            );
        }
        if (!isset($config['placeholder'])) {
            throw new Exception(
                "'Datetime' field '$identifier' configuration 'mode' must takes only effect if a placeholder is defined as well."
            );
        }
    }

    /**
     * Validate the 'range' configuration.
     * 
     * @param mixed $entry The 'range' value.
     * @param array $config The configuration array.
     * 
     * @throws Exception If validation fails.
     */
    private function _validateRange(mixed $entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_array($entry)) {
            throw new Exception(
                "'Datetime' field '$identifier' configuration 'range' must be of type array."
            );
        }
        $count = count($entry);
        if ($count !== 1 && $count !== 2) {
            throw new Exception(
                "'Datetime' field '$identifier' configuration 'range' must contain one or two elements."
            );
        }

        foreach ($entry as $key => $value) {
            if (!in_array($key, self::RANGE_KEYS)) {
                throw new Exception(
                    "'Datetime' field '$identifier' configuration 'range' must contain a valid key. " .
                    "Valid keys are: " . implode(', ', self::RANGE_KEYS)
                );
            }
            $this->_validateDate($value, $config, $identifier, "range[$key]");
        }
    }

    /**
     * Convert an array configuration to the object's properties.
     * 
     * @param array $config The configuration array.
     * @param array $fieldProperties Additional field properties.
     * 
     * @throws Exception If a required property is missing or if validation fails.
     */
    public function arrayToConfig(array $config, array $fieldProperties, ?array $globalConf = NULL, mixed $misc = NULL): void
    {
        $globalConf = $globalConf ?? $config;
        $this->checkRequirements($globalConf, ['identifier'], 'Datetime');
        $identifier = $globalConf['identifier'];
        $properties = get_object_vars($this);
        foreach ($config as $configKey => $value) {
            if ($this->isValidConfig($properties, $configKey)) {
                if ($GLOBALS['CbBuilder']['config']['Strict'] === true) {
                    switch ($configKey) {
                        case 'dbType':
                            $this->_validateDbType($value, $globalConf);
                            $this->dbType = $value;
                            break;
                        case 'default':
                            $this->_validateDefaultOrPlaceholder($value, $globalConf, 'default');
                            $this->default = is_int($value) ? $value : $this->_convertToUnix($value, $globalConf, 'default');
                            break;
                        case 'format':
                            $this->_validateFormat($value, $globalConf);
                            $this->format = $value;
                            break;
                        case 'mode':
                            $this->_validateMode($value, $globalConf);
                            $this->mode = $value;
                            break;
                        case 'placeholder':
                            $this->_validateDefaultOrPlaceholder($value, $globalConf, 'placeholder');
                            $this->placeholder = is_int($value) ? $value : $this->_convertToUnix($value, $globalConf, 'placeholder');
                            break;
                        case 'range':
                            $this->_validateRange($value, $globalConf);
                            if (isset($value['upper'])) {
                                $value['upper'] = is_int($value['upper'])
                                    ? $value['upper']
                                    : $this->_convertToUnix($value['upper'], $globalConf, "range['upper']");
                            }
                            if (isset($value['lower'])) {
                                $value['lower'] = is_int($value['lower'])
                                    ? $value['lower']
                                    : $this->_convertToUnix($value['lower'], $globalConf, "range['upper']");
                            }
                            if ($value['upper'] < $value['lower']) {
                                $identifier = $globalConf['identifier'];
                                throw new Exception(
                                    "'Text' field '$identifier' configuration 'range': 'upper' can't be earlier than 'lower'. " .
                                    "Fix: Swap values."
                                );
                            }
                            $this->range = $value;
                            break;
                        case 'search':
                            $this->validateSearch($value, $identifier, 'Datetime');
                            $this->search = $value;
                            break;
                        case 'softref':
                            $value = str_replace(' ', '', $value);
                            $this->validateSoftRef($value, $globalConf, 'Datetime');
                            $this->softref = $value;
                            break;
                        default:
                            $this->addGenericConfig($configKey, $value, $globalConf, $this, $fieldProperties);
                            break;
                    }
                } else {
                    $this->$configKey = $value;
                }
            } else if (!in_array($configKey, $fieldProperties)) {
                $identifier = $globalConf['identifier'];
                throw new Exception(
                    "'Datetime' field '$identifier' configuration '$configKey' is not valid.\n" .
                    "Valid settings are: " . implode(', ', array_keys($properties))
                );
            }
        }
    }
}

/**
 * Class DatetimeField
 * 
 * Represents a datetime field.
 */
final class DatetimeField extends Field
{
    /**
     * The configuration for this datetime field.
     */
    protected DatetimeFieldConfig $config;

    /**
     * Get the configuration of this datetime field.
     * 
     * @return DatetimeFieldConfig The configuration.
     */
    public function getConfig(): DatetimeFieldConfig
    {
        return $this->config;
    }

    /**
     * Convert an array representation of a field into the object's properties.
     * 
     * @param array $field The field array.
     */
    private function _arrayToField(array $field): void
    {
        $this->__arrayToField('datetime', $field);
        $field['table'] = $this->table;
        $field['useExistingField'] = $this->useExistingField;
        $field['identifier'] = $this->identifier;
        $config = new DatetimeFieldConfig();
        $config->arrayToConfig($field, array_keys(get_object_vars($this)));
        $this->config = $config;
    }

    /**
     * Merge the configuration of another datetime field into this one.
     * 
     * @param DatetimeField $foreign The field to merge.
     */
    public function mergeField(DatetimeField $foreign): void
    {
        $this->config->mergeConfig($foreign->getConfig());
        $this->mergeFields($foreign);
    }

    /**
     * Parse the field into a string representation.
     * 
     * @param int $mode The parsing mode.
     * @param int $level The parsing level.
     * 
     * @return string The parsed field string.
     */
    public function parseField(int $mode, int $level): string
    {
        return $this->__parseField($this->config, $mode, $level);
    }

    /**
     * Convert the field to an array representation.
     * 
     * @return array The field array.
     */
    public function fieldToArray(): array
    {
        return $this->__fieldToArray($this->config);
    }

    /**
     * Constructor for the DatetimeField class.
     * 
     * @param array $field The field configuration.
     * @param string $table The table name.
     */
    public function __construct(array $field, string $table)
    {
        $this->table = $table;
        $this->_arrayToField($field);
    }
}