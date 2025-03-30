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
 * Class ColorFieldConfig
 * 
 * Represents the configuration for a color field.
 */
final class ColorFieldConfig extends Config
{
    /**
     * The default color value.
     */
    protected string $default = '';

    /**
     * The mode for the color field.
     */
    protected string $mode = '';

    /**
     * Whether the field is nullable.
     */
    protected ?bool $nullable = NULL;

    /**
     * Whether opacity is enabled.
     */
    protected ?bool $opacity = NULL;

    /**
     * The placeholder text for the field.
     */
    protected string $placeholder = '';

    /**
     * Whether the field is required.
     */
    protected ?bool $required = NULL;

    /**
     * Whether the field is read-only.
     */
    protected ?bool $readOnly = NULL;

    /**
     * The size of the field.
     */
    protected int $size = -1;

    /**
     * The value picker configuration.
     */
    protected array $valuePicker = [];

    /**
     * Get the default color value.
     * 
     * @return string The default color.
     */
    public function getDefault(): string
    {
        return $this->default;
    }

    /**
     * Get the mode for the color field.
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
    public function isNullable(): ?bool
    {
        return $this->nullable;
    }

    /**
     * Get whether opacity is enabled.
     * 
     * @return bool|null Whether opacity is enabled.
     */
    public function isOpacity(): ?bool
    {
        return $this->opacity;
    }

    /**
     * Get the placeholder text for the field.
     * 
     * @return string The placeholder text.
     */
    public function getPlaceholder(): string
    {
        return $this->placeholder;
    }

    /**
     * Get whether the field is required.
     * 
     * @return bool|null Whether the field is required.
     */
    public function isRequired(): ?bool
    {
        return $this->required;
    }

    /**
     * Get whether the field is read-only.
     * 
     * @return bool|null Whether the field is read-only.
     */
    public function isReadOnly(): ?bool
    {
        return $this->readOnly;
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
     * Get the value picker configuration.
     * 
     * @return array The value picker configuration.
     */
    public function getValuePicker(): array
    {
        return $this->valuePicker;
    }

    /**
     * Set the default color value.
     * 
     * @param string $default The default color.
     */
    public function setDefault(string $default): void
    {
        $this->default = $default;
    }

    /**
     * Set the mode for the color field.
     * 
     * @param string $mode The mode to set.
     */
    public function setMode(string $mode): void
    {
        $this->mode = $mode;
    }

    /**
     * Set whether the field is nullable.
     * 
     * @param bool|null $nullable Whether the field should be nullable.
     */
    public function setNullable(?bool $nullable): void
    {
        $this->nullable = $nullable;
    }

    /**
     * Set whether opacity is enabled.
     * 
     * @param bool|null $opacity Whether opacity should be enabled.
     */
    public function setOpacity(?bool $opacity): void
    {
        $this->opacity = $opacity;
    }

    /**
     * Set the placeholder text for the field.
     * 
     * @param string $placeholder The placeholder text.
     */
    public function setPlaceholder(string $placeholder): void
    {
        $this->placeholder = $placeholder;
    }

    /**
     * Set whether the field is required.
     * 
     * @param bool|null $required Whether the field should be required.
     */
    public function setRequired(?bool $required): void
    {
        $this->required = $required;
    }

    /**
     * Set whether the field is read-only.
     * 
     * @param bool|null $readOnly Whether the field should be read-only.
     */
    public function setReadOnly(?bool $readOnly): void
    {
        $this->readOnly = $readOnly;
    }

    /**
     * Set the size of the field.
     * 
     * @param int $size The size to set.
     */
    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    /**
     * Set the value picker configuration.
     * 
     * @param array $valuePicker The value picker configuration.
     */
    public function setValuePicker(array $valuePicker): void
    {
        $this->valuePicker = $valuePicker;
    }

    /**
     * Merge the configuration from another ColorFieldConfig instance.
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
        $this->default = ($foreign->getDefault() !== '') ? $foreign->getDefault() : $this->default;
        $this->mode = ($foreign->getMode() !== '') ? $foreign->getMode() : $this->mode;
        $this->nullable = ($foreign->isNullable() !== null) ? $foreign->isNullable() : $this->nullable;
        $this->opacity = ($foreign->isOpacity() !== null) ? $foreign->isOpacity() : $this->opacity;
        $this->placeholder = ($foreign->getPlaceholder() !== '') ? $foreign->getPlaceholder() : $this->placeholder;
        $this->required = ($foreign->isRequired() !== null) ? $foreign->isRequired() : $this->required;
        $this->readOnly = ($foreign->isReadOnly() !== null) ? $foreign->isReadOnly() : $this->readOnly;
        $this->size = ($foreign->getSize() >= 0) ? $foreign->getSize() : $this->size;
        $this->valuePicker = (!empty($foreign->getValuePicker())) ? $foreign->getValuePicker() : $this->valuePicker;
    }

    /**
     * Validate the 'mode' configuration.
     * 
     * @param mixed $entry The 'mode' value.
     * @param array $config The configuration.
     * 
     * @throws Exception If validation fails.
     */
    private function _validateMode(mixed $entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_string($entry)) {
            throw new Exception(
                "'Color' field '$identifier' configuration 'mode' must be of type string."
            );
        }
        if ('useOrOverridePlaceholder' !== $entry) {
            throw new Exception(
                "'Color' field '$identifier' configuration 'mode' must contain the keyword 'useOrOverridePlaceholder'."
            );
        }
        if (!isset($config['placeholder'])) {
            throw new Exception(
                "'Color' field '$identifier' configuration 'mode' must takes only effect if a placeholder is defined as well."
            );
        }
    }

    /**
     * Validate a color hex value.
     * 
     * @param mixed $entry The color value.
     * @param array $config The configuration.
     * @param string|null $type The type of the value.
     * @param string|null $notStringException The exception message if not a string.
     * @param string|null $wrongFormatException The exception message for wrong format.
     * @param string|null $opacityDisabled The exception message if opacity is disabled.
     * 
     * @throws Exception If validation fails.
     */
    private function _validateColorHex(
        mixed $entry,
        array $config,
        ?string $type = '',
        ?string $notStringException = '',
        ?string $wrongFormatException = '',
        ?string $opacityDisabled = ''
    ): void
    {
        $identifier = $config['identifier'];
        if (!is_string($entry)) {
            if ($type !== '') {
                throw new Exception(
                    "'Color' field '$identifier' configuration '$type' must be of type string."
                );
            } else {
                throw new Exception($notStringException);
            }
        }

        $match = [];

        preg_match("/#([A-Fa-f0-9]{8}|[A-Fa-f0-9]{6}|[A-Fa-f0-9]{4}|[A-Fa-f0-9]{3})/", $entry, $match);
        if ($match[0] !== $entry) {
            if ($type !== '') {
                throw new Exception(
                    "'Color' field '$identifier' configuration '$type' must be a valid html color hex " .
                    "code in format RRGGBB, RRGGBBAA, RGB or RGBA."
                );
            } else {
                throw new Exception($wrongFormatException);
            }
        }

        if ((strlen($entry) === 5 || strlen($entry) === 9) && (!isset($config['opacity']) || !$config['opacity'])) {
            if ($type !== '') {
                throw new Exception(
                    "'Color' field '$identifier' configuration '$type' has format RGBA or RRGGBBAA but opacity is not set or " .
                    "set to false. Omit the opacity values or set ['config']['opacity'] to true."
                );
            } else {
                throw new Exception($opacityDisabled);
            }
        }
    }

    /**
     * Validate the size of the field.
     * 
     * @param mixed $entry The size value.
     * @param array $config The configuration.
     * 
     * @throws Exception If validation fails.
     */
    private function _validateSize(mixed $entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_int($entry)) {
            throw new Exception(
                "'Color' field '$identifier' configuration 'size' must be of type integer."
            );
        }
        $entry = intval($entry);
        if ($entry < 10 || $entry > 50) {
            throw new Exception(
                "'Color' field '$identifier' configuration 'size' must be a range between 10 and 50."
            );
        }
    }

    /**
     * Validate the value picker configuration.
     * 
     * @param mixed $entry The value picker value.
     * @param array $config The configuration.
     * 
     * @throws Exception If validation fails.
     */
    private function _validateValuePicker(mixed $entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_array($entry)) {
            throw new Exception(
                "'Color' field '$identifier' configuration 'valuePicker' must be of type array."
            );
        }

        if (!isset($entry['items'])) {
            throw new Exception(
                "'Color' field '$identifier' configuration 'valuePicker' must contain an array with the key 'items'."
            );
        }

        if (!is_array($entry['items'])) {
            throw new Exception(
                "'Color' field '$identifier' configuration 'valuePicker['items']' must be of type array."
            );
        }

        $this->valuePicker['items'] = [];
        $i = 0;
        foreach ($entry['items'] as $item) {
            if (!is_array($item)) {
                throw new Exception(
                    "'Color' field '$identifier' configuration 'valuePicker['items'][$i]' must be of type array. " .
                    "Fix:\nvaluePicker:\n  items:\n    -\n      - 'key1'\n      - '#ABC'\n    -\n      " .
                    "- 'key2'\n      - '#AA11BB'\n    -\n      - 'key3'\n      - '#AA11BB22'"
                );
            }
            if (count($item) !== 2) {
                throw new Exception(
                    "'Color' field '$identifier' configuration 'valuePicker['items'][$i]' must be of type array. " .
                    "Fix:\nvaluePicker:\n  items:\n    -\n      - 'key1'\n      - '#ABC'\n    -\n      " .
                    "- 'key2'\n      - '#AA11BB'\n    -\n      - 'key3'\n      - '#AA11BB22'"
                );
            }
            $key = $item[0];
            if (!is_string($key)) {
                throw new Exception(
                    "'Color' field '$identifier' configuration 'valuePicker['items'][$i]' must be of type array. " .
                    "Fix:\nvaluePicker:\n  items:\n    -\n      - 'key1'\n      - '#ABC'\n    -\n      " .
                    "- 'key2'\n      - '#AA11BB'\n    -\n      - 'key3'\n      - '#AA11BB22'"
                );
            }
            $value = $item[1];
            $this->_validateColorHex(
                $value, $config, '',
                "'Color' field '$identifier' configuration 'valuePicker['items'][$i][0]' value must be of type string.",
                "'Color' field '$identifier' configuration 'valuePicker['items'][$i][0]' value must be a valid html color hex " .
                "code in format RRGGBB, RRGGBBAA, RGB or RGBA.",
                "'Color' field '$identifier' configuration 'valuePicker['items'][$i][0]' has format RGBA or RRGGBBAA but " .
                "opacity is not set or set to false. Omit the opacity values or set ['config']['opacity'] to true."
            );
            $this->valuePicker['items'][] = $item;
            $i++;
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
        $this->checkRequirements($globalConf, ['identifier'], 'Color');
        $properties = get_object_vars($this);
        foreach ($config as $configKey => $value) {
            if ($this->isValidConfig($properties, $configKey)) {
                if ($GLOBALS['CbBuilder']['config']['Strict'] === true) {
                    switch ($configKey) {
                        case 'default':
                            $this->_validateColorHex($value, $globalConf, 'default');
                            $this->default = $value;
                            break;
                        case 'mode':
                            $this->_validateMode($value, $globalConf);
                            $this->mode = $value;
                            break;
                        case 'placeholder':
                            $this->_validateColorHex($value, $globalConf, 'placeholder');
                            $this->placeholder = $value;
                            break;
                        case 'size':
                            $this->_validateSize($value, $globalConf);
                            $this->size = $value;
                            break;
                        case 'valuePicker':
                            $this->_validateValuePicker($value, $globalConf);
                            $this->valuePicker = $value;
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
                    "'Color' field '$identifier' configuration '$configKey' is not valid.\n" .
                    "Valid settings are: " . implode(', ', array_keys($properties))
                );
            }
        }
    }
}

/**
 * Class ColorField
 * 
 * Represents a color field.
 */
final class ColorField extends Field
{
    /**
     * The configuration for this color field.
     */
    protected ColorFieldConfig $config;

    /**
     * Get the configuration of this color field.
     * 
     * @return ColorFieldConfig The configuration.
     */
    public function getConfig(): ColorFieldConfig
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
        $this->__arrayToField('color', $field);
        $field['table'] = $this->table;
        $field['useExistingField'] = $this->useExistingField;
        $field['identifier'] = $this->identifier;
        $config = new ColorFieldConfig();
        $config->arrayToConfig($field, array_keys(get_object_vars($this)));
        $this->config = $config;
    }

    /**
     * Merge the configuration of another color field into this one.
     * 
     * @param ColorField $foreign The field to merge.
     */
    public function mergeField(ColorField $foreign): void
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
     * Constructor for the ColorField class.
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