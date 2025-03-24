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
 * Configuration class for radio field.
 */
final class RadioFieldConfig extends Config
{
    /**
     * Default value for the field.
     */
    protected string $default = '';

    /**
     * Items for the radio field.
     */
    protected array $items = [];

    /**
     * User function for processing items.
     */
    protected string $itemsProcFunc = '';

    /**
     * Whether the field is read-only.
     */
    protected ?bool $readOnly = null;

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
     * Get the items for the radio field.
     *
     * @return array The items.
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Get the user function for processing items.
     *
     * @return string The user function.
     */
    public function getItemsProcFunc(): string
    {
        return $this->itemsProcFunc;
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
     * Set the default value for the field.
     *
     * @param string $default The default value to set.
     */
    public function setDefault(string $default): void
    {
        $this->default = $default;
    }

    /**
     * Set the items for the radio field.
     *
     * @param array $items The items to set.
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    /**
     * Set the user function for processing items.
     *
     * @param string $itemsProcFunc The user function to set.
     */
    public function setItemsProcFunc(string $itemsProcFunc): void
    {
        $this->itemsProcFunc = $itemsProcFunc;
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

        if ($foreign->getDefault() !== '') {
            $this->default = $foreign->getDefault();
        }

        if ($foreign->getItems() !== []) {
            $this->items = $foreign->getItems();
        }

        if ($foreign->getItemsProcFunc() !== '') {
            $this->itemsProcFunc = $foreign->getItemsProcFunc();
        }

        if ($foreign->getReadOnly() !== null) {
            $this->readOnly = $foreign->getReadOnly();
        }
    }

    /**
     * Valid types for radio field items.
     */
    const VALID_TYPES = [
        '*', 'page', 'url', 'file', 'folder', 'email', 'telephone', 'record'
    ];

    /**
     * Valid keywords for items.
     */
    const VALID_ITEM_KEYWORDS = [
        'label', 'value'
    ];

    /**
     * Validate the items for the radio field.
     *
     * @param mixed $entries The items to validate.
     * @param array $config The configuration array.
     *
     * @throws Exception If the items are invalid.
     */
    private function _validateItems(mixed $entries, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_array($entries)) {
            throw new Exception(
                "'Radio' field '$identifier' configuration 'items' must be of type array. Fix:\n" .
                "items:\n  -\n    label: 'label1'\n    value: 'value1'\n  -\n    label: 'label2'\n    value: 'value2'"
            );
        }
        $i = 0;
        foreach ($entries as $entry) {
            if (!is_array($entry)) {
                throw new Exception(
                    "'Radio' field '$identifier' configuration 'items[$i]' must be of type array. Fix:\n" .
                    "items:\n  -\n    label: 'label1'\n    value: 'value1'\n  -\n    label: 'label2'\n    value: 'value2'"
                );
            }
            $j = 0;
            foreach ($entry as $key => $value) {
                if (!is_string($key)) {
                    throw new Exception(
                        "'Radio' field '$identifier' configuration 'items[$j]' key must be of type string. Fix:\n" .
                        "items:\n  -\n    label: 'label1'\n    value: 'value1'\n  -\n    label: 'label2'\n    value: 'value2'"
                    );
                }
                if (!in_array($key, self::VALID_ITEM_KEYWORDS)) {
                    throw new Exception(
                        "'Radio' field '$identifier' configuration 'items[$key]' '$key' is no valid keyword.\n" .
                        "Valid keywords are: " . implode(', ', self::VALID_ITEM_KEYWORDS)
                    );
                }
                if (!is_string($value) && !is_int($value)) {
                    throw new Exception(
                        "'Radio' field '$identifier' configuration 'items[$i]' value must be of type string or integer. Fix:\n" .
                        "items:\n  -\n    label: 'label1'\n    value: 'value1'\n  -\n    label: 'label2'\n    value: 'value2'"
                    );
                }
                $j++;
            }
            
            $i++;
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
        $this->checkRequirements($globalConf, ['identifier', 'items'], 'Radio');
        $properties = get_object_vars($this);
        foreach ($config as $configKey => $value) {
            if ($this->isValidConfig($properties, $configKey)) {
                if ($GLOBALS['CbBuilder']['config']['Strict'] === true) {
                    switch ($configKey) {
                        case 'items':
                            $this->_validateItems($value, $globalConf);
                            $this->items = $value;
                            break;
                        case 'itemsProcFunc':
                            $this->validateUserFunc($value, $globalConf, 'itemsProcFunc', 'Radio');
                            $this->itemsProcFunc = $value;
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
                    "'Radio' field '$identifier' configuration '$configKey' is not valid.\n" .
                    "Valid settings are: " . implode(', ', array_keys($properties))
                );
            }
        }
    }
}

/**
 * Class representing a radio field.
 */
final class RadioField extends Field
{
    /**
     * Configuration for the radio field.
     */
    protected RadioFieldConfig $config;

    /**
     * Get the configuration for the radio field.
     *
     * @return RadioFieldConfig The configuration.
     */
    public function getConfig(): RadioFieldConfig
    {
        return $this->config;
    }

    /**
     * Convert an array to a radio field.
     *
     * @param array $field The array to convert.
     */
    private function _arrayToField(array $field): void
    {
        $this->__arrayToField('radio', $field);
        $field['table'] = $this->table;
        $field['useExistingField'] = $this->useExistingField;
        $field['identifier'] = $this->identifier;
        $config = new RadioFieldConfig();
        $config->arrayToConfig($field, array_keys(get_object_vars($this)));
        $this->config = $config;
    }

    /**
     * Merge the current field with another field.
     *
     * @param RadioField $foreign The foreign field to merge.
     */
    public function mergeField(RadioField $foreign): void
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
     * Constructor for the radio field.
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