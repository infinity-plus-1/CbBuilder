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
 * Configuration class for JSON field settings.
 */
final class JsonFieldConfig extends Config
{
    /**
     * Number of columns for the JSON field.
     */
    protected int $cols = -1;

    /**
     * Default value for the JSON field.
     */
    protected string $default = '';

    /**
     * Whether to enable the code editor for the JSON field.
     */
    protected ?bool $enableCodeEditor = null;

    /**
     * Placeholder text for the JSON field.
     */
    protected string $placeholder = '';

    /**
     * Whether the JSON field is read-only.
     */
    protected ?bool $readOnly = null;

    /**
     * Whether the JSON field is required.
     */
    protected ?bool $required = null;

    /**
     * Number of rows for the JSON field.
     */
    protected int $rows = -1;

    /**
     * Get the number of columns for the JSON field.
     *
     * @return int The number of columns.
     */
    public function getCols(): int
    {
        return $this->cols;
    }

    /**
     * Get the default value for the JSON field.
     *
     * @return string The default value.
     */
    public function getDefault(): string
    {
        return $this->default;
    }

    /**
     * Get whether the code editor is enabled for the JSON field.
     *
     * @return bool|null Whether the code editor is enabled.
     */
    public function isEnableCodeEditor(): ?bool
    {
        return $this->enableCodeEditor;
    }

    /**
     * Get the placeholder text for the JSON field.
     *
     * @return string The placeholder text.
     */
    public function getPlaceholder(): string
    {
        return $this->placeholder;
    }

    /**
     * Get whether the JSON field is read-only.
     *
     * @return bool|null Whether the field is read-only.
     */
    public function isReadOnly(): ?bool
    {
        return $this->readOnly;
    }

    /**
     * Get whether the JSON field is required.
     *
     * @return bool|null Whether the field is required.
     */
    public function isRequired(): ?bool
    {
        return $this->required;
    }

    /**
     * Get the number of rows for the JSON field.
     *
     * @return int The number of rows.
     */
    public function getRows(): int
    {
        return $this->rows;
    }

    /**
     * Set the number of columns for the JSON field.
     *
     * @param int $cols The number of columns.
     */
    public function setCols(int $cols): void
    {
        $this->cols = $cols;
    }

    /**
     * Set the default value for the JSON field.
     *
     * @param string $default The default value.
     */
    public function setDefault(string $default): void
    {
        $this->default = $default;
    }

    /**
     * Set whether to enable the code editor for the JSON field.
     *
     * @param bool|null $enableCodeEditor Whether to enable the code editor.
     */
    public function setEnableCodeEditor(?bool $enableCodeEditor): void
    {
        $this->enableCodeEditor = $enableCodeEditor;
    }

    /**
     * Set the placeholder text for the JSON field.
     *
     * @param string $placeholder The placeholder text.
     */
    public function setPlaceholder(string $placeholder): void
    {
        $this->placeholder = $placeholder;
    }

    /**
     * Set whether the JSON field is read-only.
     *
     * @param bool|null $readOnly Whether the field is read-only.
     */
    public function setReadOnly(?bool $readOnly): void
    {
        $this->readOnly = $readOnly;
    }

    /**
     * Set whether the JSON field is required.
     *
     * @param bool|null $required Whether the field is required.
     */
    public function setRequired(?bool $required): void
    {
        $this->required = $required;
    }

    /**
     * Set the number of rows for the JSON field.
     *
     * @param int $rows The number of rows.
     */
    public function setRows(int $rows): void
    {
        $this->rows = $rows;
    }

    /**
     * Merge the configuration with another JSON field configuration.
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

        if ($foreign->getCols() >= 0) {
            $this->cols = $foreign->getCols();
        }

        if ($foreign->getDefault() !== '') {
            $this->default = $foreign->getDefault();
        }

        if ($foreign->isEnableCodeEditor() !== null) {
            $this->enableCodeEditor = $foreign->isEnableCodeEditor();
        }

        if ($foreign->getPlaceholder() !== '') {
            $this->placeholder = $foreign->getPlaceholder();
        }

        if ($foreign->isReadOnly() !== null) {
            $this->readOnly = $foreign->isReadOnly();
        }

        if ($foreign->isRequired() !== null) {
            $this->required = $foreign->isRequired();
        }

        if ($foreign->getRows() >= 0) {
            $this->rows = $foreign->getRows();
        }
    }

    /**
     * Convert an array configuration to the current JSON field configuration.
     *
     * @param array $config The configuration array.
     * @param array $fieldProperties The properties of the field.
     */
    public function arrayToConfig(array $config, array $fieldProperties, ?array $globalConf = NULL, mixed $misc = NULL): void
    {
        $globalConf = $globalConf ?? $config;
        $this->checkRequirements($globalConf, ['identifier'], 'Json');
        $properties = get_object_vars($this);
        foreach ($config as $configKey => $value) {
            if ($this->isValidConfig($properties, $configKey)) {
                if ($GLOBALS['CbBuilder']['config']['Strict'] === true) {
                    switch ($configKey) {
                        case 'cols':
                            $this->validateInteger($value, $globalConf, 'cols', 'Json', 1, 50);
                            $this->cols = intval($value);
                            break;
                        case 'rows':
                            $this->validateInteger($value, $globalConf, 'rows', 'Json', 1, 20);
                            $this->rows = intval($value);
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
                    "'Json' field '$identifier' configuration '$configKey' is not valid.\n" .
                    "Valid settings are: " . implode(', ', array_keys($properties))
                );
            }
        }
    }
}

/**
 * Class representing a JSON field.
 */
final class JsonField extends Field
{
    /**
     * Configuration for the JSON field.
     */
    protected JsonFieldConfig $config;

    /**
     * Get the configuration for the JSON field.
     *
     * @return JsonFieldConfig The field configuration.
     */
    public function getConfig(): JsonFieldConfig
    {
        return $this->config;
    }

    /**
     * Convert an array to a JSON field.
     *
     * @param array $field The array representing the field.
     */
    private function _arrayToField(array $field): void
    {
        $this->__arrayToField('json', $field);
        $field['table'] = $this->table;
        $field['useExistingField'] = $this->useExistingField;
        $field['identifier'] = $this->identifier;
        $config = new JsonFieldConfig();
        $config->arrayToConfig($field, array_keys(get_object_vars($this)));
        $this->config = $config;
    }

    /**
     * Merge the current field with another JSON field.
     *
     * @param JsonField $foreign The foreign field to merge.
     */
    public function mergeField(JsonField $foreign): void
    {
        $this->config->mergeConfig($foreign->getConfig());
        $this->mergeFields($foreign);
    }

    /**
     * Parse the field for rendering.
     *
     * @param int $mode The rendering mode.
     * @param int $level The rendering level.
     *
     * @return string The parsed field.
     */
    public function parseField(int $mode, int $level): string
    {
        return $this->__parseField($this->config, $mode, $level);
    }

    /**
     * Convert the field to an array representation.
     *
     * @return array The field as an array.
     */
    public function fieldToArray(): array
    {
        return $this->__fieldToArray($this->config);
    }

    /**
     * Constructor for the JSON field.
     *
     * @param array $field The array representing the field.
     * @param string $table The table name.
     */
    public function __construct(array $field, string $table)
    {
        $this->table = $table;
        $this->_arrayToField($field);
    }
}
