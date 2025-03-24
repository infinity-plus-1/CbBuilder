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

use DS\CbBuilder\Utility\Utility;
use Exception;
use InvalidArgumentException;

/**
 * Configuration class for number field.
 */
final class NumberFieldConfig extends Config
{
    /**
     * Whether autocomplete is enabled.
     */
    protected ?bool $autocomplete = null;

    /**
     * Default value for the field.
     */
    protected string $default = '';

    /**
     * Format of the field (integer or decimal).
     */
    protected string $format = '';

    /**
     * Mode for the field (e.g., use or override placeholder).
     */
    protected string $mode = '';

    /**
     * Whether the field is nullable.
     */
    protected ?bool $nullable = null;

    /**
     * Range for the field.
     */
    protected array $range = [];

    /**
     * Whether the field is read-only.
     */
    protected ?bool $readOnly = null;

    /**
     * Whether the field is required.
     */
    protected ?bool $required = null;

    /**
     * Size of the field.
     */
    protected int $size = -1;

    /**
     * Slider settings for the field.
     */
    protected array $slider = [];

    /**
     * Value picker settings for the field.
     */
    protected array $valuePicker = [];


    const SLIDER_KEYWORDS = [
        'step', 'width'
    ];

    const VALUEPICKER_KEYWORDS = [
        'items', 'mode'
    ];

    const VALUEPICKER_MODE_KEYWORDS = [
        'blank', 'append', 'prepend'
    ];

    /**
     * Get whether autocomplete is enabled.
     *
     * @return bool|null Whether autocomplete is enabled.
     */
    public function getAutocomplete(): ?bool
    {
        return $this->autocomplete;
    }

    /**
     * Get the default value for the field.
     *
     * @return string The default value.
     */
    public function getDefault(): string
    {
        return $this->default;
    }

    /**
     * Get the format of the field.
     *
     * @return string The format (integer or decimal).
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * Get the mode for the field.
     *
     * @return string The mode.
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * Get whether the field is nullable.
     *
     * @return bool|null Whether the field is nullable.
     */
    public function getNullable(): ?bool
    {
        return $this->nullable;
    }

    /**
     * Get the range for the field.
     *
     * @return array The range.
     */
    public function getRange(): array
    {
        return $this->range;
    }

    /**
     * Get whether the field is read-only.
     *
     * @return bool|null Whether the field is read-only.
     */
    public function getReadOnly(): ?bool
    {
        return $this->readOnly;
    }

    /**
     * Get whether the field is required.
     *
     * @return bool|null Whether the field is required.
     */
    public function getRequired(): ?bool
    {
        return $this->required;
    }

    /**
     * Get the size of the field.
     *
     * @return int The size.
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Get the slider settings for the field.
     *
     * @return array The slider settings.
     */
    public function getSlider(): array
    {
        return $this->slider;
    }

    /**
     * Get the value picker settings for the field.
     *
     * @return array The value picker settings.
     */
    public function getValuePicker(): array
    {
        return $this->valuePicker;
    }

    /**
     * Set whether autocomplete is enabled.
     *
     * @param bool|null $autocomplete Whether autocomplete is enabled.
     */
    public function setAutocomplete(?bool $autocomplete): void
    {
        $this->autocomplete = $autocomplete;
    }

    /**
     * Set the default value for the field.
     *
     * @param string $default The default value.
     */
    public function setDefault(string $default): void
    {
        $this->default = $default;
    }

    /**
     * Set the format of the field.
     *
     * @param string $format The format (integer or decimal).
     */
    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    /**
     * Set the mode for the field.
     *
     * @param string $mode The mode.
     */
    public function setMode(string $mode): void
    {
        $this->mode = $mode;
    }

    /**
     * Set whether the field is nullable.
     *
     * @param bool|null $nullable Whether the field is nullable.
     */
    public function setNullable(?bool $nullable): void
    {
        $this->nullable = $nullable;
    }

    /**
     * Set the range for the field.
     *
     * @param array $range The range.
     */
    public function setRange(array $range): void
    {
        $this->range = $range;
    }

    /**
     * Set whether the field is read-only.
     *
     * @param bool|null $readOnly Whether the field is read-only.
     */
    public function setReadOnly(?bool $readOnly): void
    {
        $this->readOnly = $readOnly;
    }

    /**
     * Set whether the field is required.
     *
     * @param bool|null $required Whether the field is required.
     */
    public function setRequired(?bool $required): void
    {
        $this->required = $required;
    }

    /**
     * Set the size of the field.
     *
     * @param int $size The size.
     */
    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    /**
     * Set the slider settings for the field.
     *
     * @param array $slider The slider settings.
     */
    public function setSlider(array $slider): void
    {
        $this->slider = $slider;
    }

    /**
     * Set the value picker settings for the field.
     *
     * @param array $valuePicker The value picker settings.
     */
    public function setValuePicker(array $valuePicker): void
    {
        $this->valuePicker = $valuePicker;
    }

    /**
     * Merge the current configuration with another configuration.
     *
     * @param self $foreign The foreign configuration to merge.
     */
    public function mergeConfig(Config $foreign): void
    {
        if (!$foreign instanceof self) {
            throw new InvalidArgumentException (
                "Config 'foreign' must be of type " . get_class($this)
            );
        }
        $this->mergeMainConfig($foreign);

        if ($foreign->getAutocomplete() !== null) {
            $this->autocomplete = $foreign->getAutocomplete();
        }

        if ($foreign->getDefault() !== '') {
            $this->default = $foreign->getDefault();
        }

        if ($foreign->getFormat() !== '') {
            $this->format = $foreign->getFormat();
        }

        if ($foreign->getMode() !== '') {
            $this->mode = $foreign->getMode();
        }

        if ($foreign->getNullable() !== null) {
            $this->nullable = $foreign->getNullable();
        }

        if (!empty($foreign->getRange())) {
            $this->range = $foreign->getRange();
        }

        if ($foreign->getReadOnly() !== null) {
            $this->readOnly = $foreign->getReadOnly();
        }

        if ($foreign->getRequired() !== null) {
            $this->required = $foreign->getRequired();
        }

        if ($foreign->getSize() >= 0) {
            $this->size = $foreign->getSize();
        }

        if (!empty($foreign->getSlider())) {
            $this->slider = $foreign->getSlider();
        }

        if (!empty($foreign->getValuePicker())) {
            $this->valuePicker = $foreign->getValuePicker();
        }
    }

    /**
     * Validate the format of the field.
     *
     * @param string $entry The format to validate.
     * @param array $config The configuration array.
     *
     * @throws Exception If the format is not valid.
     */
    private function _validateFormat(string $entry, array $config): void
    {
        $identifier = $config['identifier'];
        if ('integer' !== $entry && 'decimal' !== $entry) {
            throw new Exception(
                "'Number' field '$identifier' configuration 'format' must contain either 'integer' or 'decimal'."
            );
        }
    }

    /**
     * Validate the mode of the field.
     *
     * @param string $entry The mode to validate.
     * @param array $config The configuration array.
     *
     * @throws Exception If the mode is not valid.
     */
    private function _validateMode(string $entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_string($entry)) {
            throw new Exception(
                "'Number' field '$identifier' configuration 'mode' must be of type string."
            );
        }
        if ('useOrOverridePlaceholder' !== $entry) {
            throw new Exception(
                "'Number' field '$identifier' configuration 'mode' must contain the keyword 'useOrOverridePlaceholder'."
            );
        }
        if (!isset($config['placeholder'])) {
            throw new Exception(
                "'Number' field '$identifier' configuration 'mode' must take effect only if a placeholder is defined as well."
            );
        }
    }

    /**
     * Validate and set the range for the field.
     *
     * @param mixed $entry The range configuration.
     * @param array $config The configuration array.
     *
     * @throws Exception If the range is invalid.
     */
    private function _validateAndSetRange(mixed $entry, array $config): void
    {
        $identifier = $config['identifier'];

        if (isset($entry['lower'])) {
            if (($numLow = $this->handleIntegers($entry['lower'])) === false) {
                throw new Exception(
                    "'Number' field '$identifier' configuration 'range['lower']' must contain an integer."
                );
            }
        }
        if (isset($entry['upper'])) {
            if (($numUp = $this->handleIntegers($entry['upper'])) === false) {
                throw new Exception(
                    "'Number' field '$identifier' configuration 'range['upper']' must contain an integer."
                );
            }
        }

        if (isset($numUp) && isset($numLow) && $numUp < $numLow) {
            throw new Exception(
                "'Number' field '$identifier' configuration 'range' entry 'upper' is smaller than 'lower'. Fix: " .
                "Swap values."
            );
        }
        if (isset($numUp)) $this->range['upper'] = $numUp;
        if (isset($numLow)) $this->range['lower'] = $numLow;
    }

    /**
     * Validate and set the slider settings for the field.
     *
     * @param mixed $entry The slider configuration.
     * @param array $config The configuration array.
     *
     * @throws Exception If the slider settings are invalid.
     */
    private function _validateAndSetSlider(mixed $entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_array($entry)) {
            throw new Exception(
                "'Number' field '$identifier' configuration 'slider' must be of type array."
            );
        }
        foreach ($entry as $key => $value) {
            switch ($key) {
                case 'step':
                    if (($num = Utility::toNumber($value)) !== false) {
                        $this->slider['step'] = $num;
                    } else {
                        throw new Exception(
                            "'Number' field '$identifier' configuration 'slider['step']' must contain an integer or float."
                        );
                    }
                    break;
                case 'width':
                    if ($num = $this->handleIntegers($value)) {
                        $this->slider['width'] = $num;
                    } else {
                        throw new Exception(
                            "'Number' field '$identifier' configuration 'slider['width']' must be of type integer or " .
                            "a string representing an integer."
                        );
                    }
                    break;
                default:
                    throw new Exception(
                        "'Number' field '$identifier' configuration 'slider[$key]' '$key' is no valid keyword.\n" .
                        "Valid keywords are: " . implode(', ', self::SLIDER_KEYWORDS)
                    );
                    break;
            }
        }
    }

    /**
     * Validate and set the value picker settings for the field.
     *
     * @param mixed $entry The value picker configuration.
     * @param array $config The configuration array.
     *
     * @throws Exception If the value picker settings are invalid.
     */
    private function _validateAndSetValuePicker(mixed $entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_array($entry)) {
            throw new Exception(
                "'Number' field '$identifier' configuration 'valuePicker' must be of type array."
            );
        }

        foreach ($entry as $key => $value) {
            switch ($key) {
                case 'mode':
                    if (!is_string($value)) {
                        throw new Exception(
                            "'Number' field '$identifier' configuration 'valuePicker['mode']' must be of type string."
                        );
                    }
                    if (in_array($value, self::VALUEPICKER_MODE_KEYWORDS)) {
                        $this->valuePicker['mode'] = $value;
                    } else {
                        $keyword = $value;
                        throw new Exception(
                            "'Number' field '$identifier' configuration 'valuePicker['mode']['$keyword'] '$keyword' is no " .
                            "valid keyword.\n" .
                            "Valid keywords are: " . implode(', ', self::VALUEPICKER_MODE_KEYWORDS)
                        );
                    }
                    break;
                case 'items':
                    if (!is_array($value)) {
                        throw new Exception(
                            "'Number' field '$identifier' configuration 'valuePicker['items']' must be of type array."
                        );
                    }
                    $this->valuePicker['items'] = [];
                    $i = 0;
                    foreach ($value as $item) {
                        $this->valuePicker['items'][$i] = [];
                        if (!is_array($item)) {
                            throw new Exception(
                                "'Number' field '$identifier' configuration 'valuePicker['items'][$i]' must be of type array."
                            );
                        }

                        if (count($item) !== 2) {
                            throw new Exception(
                                "'Number' field '$identifier' configuration 'valuePicker['items'][$i]' must be an array with exactly two" .
                                " entries.\nThe first being the label and the second the value. Fix:\n" .
                                "valuePicker:\n  items:\n    -\n      - label1\n      - 123\n    -\n      - label2\n      - 1.23"
                            );
                        }
                        if (!isset($item[0])) {
                            throw new Exception(
                                "'Number' field '$identifier' configuration 'valuePicker['items'][$i][0]' must " .
                                "be set as a label. Do not define keys in this item array, just values. Fix:\n" .
                                "valuePicker:\n  items:\n    -\n      - label1\n      - 123\n    -\n      - label2\n      - 1.23"
                            );
                        }
                        if (!is_string($item[0])) {
                            throw new Exception(
                                "'Number' field '$identifier' configuration 'valuePicker['items'][$i][0]' must be of type string."
                            );
                        }
                        $this->valuePicker['items'][$i][0] = $item[0];
                        if (!isset($item[1])) {
                            throw new Exception(
                                "'Number' field '$identifier' configuration 'valuePicker['items'][$i][1]' must " .
                                "be set as a numeric value. Do not define keys in this item array, just values. Fix:\n" .
                                "valuePicker:\n  items:\n    -\n      - label1\n      - 123\n    -\n      - label2\n      - 1.23"
                            );
                        }
        
                        if (($num = Utility::toNumber($item[1])) !== false) {
                            $this->valuePicker['items'][$i][1] = $num;
                        } else {
                            if ($num = $this->handleIntegers($item[1])) {
                                $this->valuePicker['items'][$i][1] = $num;
                            } else {
                                throw new Exception(
                                    "'Number' field '$identifier' configuration 'valuePicker['items'][$i][1]' value must " .
                                    "be a string representing a numeric value, an integer or a float."
                                );
                            }
                        }
                        $i++;
                    }
                    break;
                default:
                    throw new Exception(
                        "'Number' field '$identifier' configuration 'valuePicker[$key]' '$key' is no valid keyword.\n" .
                        "Valid keywords are: " . implode(', ', self::VALUEPICKER_KEYWORDS)
                    );
                    break;
            }
        }
    }

    /**
     * Convert an array configuration to the object's properties.
     *
     * @param array $config The configuration array.
     * @param array $fieldProperties The field properties.
     */
    public function arrayToConfig(array $config, array $fieldProperties, ?array $globalConf = NULL, mixed $misc = NULL): void
    {
        $globalConf = $globalConf ?? $config;
        $this->checkRequirements($globalConf, ['identifier'], 'Number');
        $properties = get_object_vars($this);
        foreach ($config as $configKey => $value) {
            if ($this->isValidConfig($properties, $configKey)) {
                if ($GLOBALS['CbBuilder']['config']['Strict'] === true) {
                    switch ($configKey) {
                        case 'format':
                            $this->_validateFormat($value, $globalConf);
                            $this->format = $value;
                            break;
                        case 'mode':
                            $this->_validateMode($value, $globalConf);
                            $this->mode = $value;
                            break;
                        case 'range':
                            $this->_validateAndSetRange($value, $globalConf);
                            break;
                        case 'size':
                            $this->validateInteger($value, $globalConf, 'size', 'Number', 10, 50);
                            $this->size = intval($value);
                            break;
                        case 'slider':
                            $this->_validateAndSetSlider($value, $globalConf);
                            break;
                        case 'valuePicker':
                            $this->_validateAndSetValuePicker($value, $globalConf);
                            break;
                        default:
                            $this->addGenericConfig($configKey, $value, $globalConf, $this, $fieldProperties);
                            break;
                    }
                } else {
                    $this->$configKey = $value;
                }
            } elseif (!in_array($configKey, $fieldProperties)) {
                $identifier = $globalConf['identifier'];
                throw new Exception(
                    "'Number' field '$identifier' configuration '$configKey' is not valid.\n" .
                    "Valid settings are: " . implode(', ', array_keys($properties))
                );
            }
        }
    }
}

/**
 * Class representing a number field.
 */
final class NumberField extends Field
{
    /**
     * Configuration for the number field.
     */
    protected NumberFieldConfig $config;

    /**
     * Get the configuration for the number field.
     *
     * @return NumberFieldConfig The configuration.
     */
    public function getConfig(): NumberFieldConfig
    {
        return $this->config;
    }

    /**
     * Convert an array to a number field.
     *
     * @param array $field The array to convert.
     */
    private function _arrayToField(array $field): void
    {
        $this->__arrayToField('number', $field);
        $field['table'] = $this->table;
        $field['useExistingField'] = $this->useExistingField;
        $field['identifier'] = $this->identifier;
        $config = new NumberFieldConfig();
        $config->arrayToConfig($field, array_keys(get_object_vars($this)));
        $this->config = $config;
    }

    /**
     * Merge the current field with another field.
     *
     * @param NumberField $foreign The foreign field to merge.
     */
    public function mergeField(NumberField $foreign): void
    {
        $this->config->mergeConfig($foreign->getConfig());
        $this->mergeFields($foreign);
    }

    /**
     * Parse the field based on the given mode and level.
     *
     * @param int $mode The mode to parse with.
     * @param int $level The level to parse with.
     *
     * @return string The parsed field.
     */
    public function parseField(int $mode, int $level): string
    {
        return $this->__parseField($this->config, $mode, $level);
    }

    /**
     * Convert the field to an array.
     *
     * @return array The field as an array.
     */
    public function fieldToArray(): array
    {
        return $this->__fieldToArray($this->config);
    }

    /**
     * Constructor for the number field.
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