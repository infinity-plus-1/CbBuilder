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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Configuration class for flex fields.
 */
final class FlexFieldConfig extends Config
{
    /**
     * Data structure configuration.
     */
    protected array $ds = [];

    /**
     * Data structure pointer field.
     */
    protected string $ds_pointerField = '';

    /**
     * Whether the field is read-only.
     */
    protected ?bool $readOnly = null;

    /**
     * Identifier for the field.
     */
    private string $identifier = '';

    /**
     * Get the data structure configuration.
     *
     * @return array The data structure configuration.
     */
    public function getDs(): array
    {
        return $this->ds;
    }

    /**
     * Get the data structure pointer field.
     *
     * @return string The data structure pointer field.
     */
    public function getDsPointerField(): string
    {
        return $this->ds_pointerField;
    }

    /**
     * Check if the field is read-only.
     *
     * @return bool|null Whether the field is read-only.
     */
    public function isReadOnly(): ?bool
    {
        return $this->readOnly;
    }

    /**
     * Set the data structure configuration.
     *
     * @param array $ds The data structure configuration.
     */
    public function setDs(array $ds): void
    {
        $this->ds = $ds;
    }

    /**
     * Set the data structure pointer field.
     *
     * @param string $ds_pointerField The data structure pointer field.
     */
    public function setDsPointerField(string $ds_pointerField): void
    {
        $this->ds_pointerField = $ds_pointerField;
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

        if (!empty($foreign->getDs())) {
            $this->ds = $foreign->getDs();
        }

        if (!empty($foreign->getDsPointerField())) {
            $this->ds_pointerField = $foreign->getDsPointerField();
        }

        if ($foreign->isReadOnly() !== null) {
            $this->readOnly = $foreign->isReadOnly();
        }
    }

    /**
     * Validate the data structure configuration.
     *
     * @param mixed $value The value to validate.
     * @param array $config The configuration array.
     *
     * @throws Exception If validation fails.
     */
    private function _validateDs(mixed $value, array $config): void
    {
        $this->validateArrayStringString($value, $config, 'ds', 'Flex');
        if (!isset($config['ds_pointerField'])) {
            if (!isset($value['default'])) {
                throw new Exception(
                    "'Flex' field '$this->identifier' configuration 'ds': Neither 'ds_pointerField' nor a " .
                    "'default' key in 'ds' is set. Either specify 'ds_pointerField' or add 'default' key to " .
                    "'ds' that includes a path to a xml file or that contains xml code directly.\nFix:\n" .
                    "ds_pointerField: 'fieldName1, Fieldname2'\nOR\n" .
                    "ds:\n  default: 'pathToXmlFile' or '<xmlCode>'"
                );
            }
        }
    }

    /**
     * Validate the data structure pointer field.
     *
     * @param mixed $value The value to validate.
     * @param array $config The configuration array.
     *
     * @throws Exception If validation fails.
     */
    private function _validateDsPointerField(mixed $value, array $config): void
    {
        if (!is_string($value)) {
            throw new Exception(
                "'Flex' field '$this->identifier' configuration 'ds_pointerField' must be of type string.\nFix:\n" .
                "ds_pointerField: 'fieldName1, Fieldname2'"
            );
        }
        $value = GeneralUtility::trimExplode(',', $value);
        if (count($value) > 2) {
            throw new Exception(
                "'Flex' field '$this->identifier' configuration 'ds_pointerField' can only contain up to two field names.\nFix:\n" .
                "ds_pointerField: 'fieldName1, Fieldname2'"
            );
        }
        $i = 0;
        foreach ($value as $field) {
            $this->validateField($field, $config, "ds_pointerField[" . $i . "]", 'Flex');
            $i++;
        }
    }

    /**
     * Convert an array configuration to the object configuration.
     *
     * @param array $config The array configuration.
     * @param array $fieldProperties The field properties.
     */
    public function arrayToConfig(array $config, array $fieldProperties, ?array $globalConf = NULL, mixed $misc = NULL): void
    {
        $globalConf = $globalConf ?? $config;
        $this->checkRequirements($globalConf, [], 'Flex');
        $this->identifier = $config['identifier'];
        $properties = get_object_vars($this);
        foreach ($config as $configKey => $value) {
            if ($this->isValidConfig($properties, $configKey)) {
                if ($GLOBALS['CbBuilder']['config']['Strict'] === true) {
                    switch ($configKey) {
                        case 'ds':
                            $this->_validateDs($value, $globalConf);
                            $this->ds = $value;
                            break;
                        case 'ds_pointerField':
                            $this->_validateDsPointerField($value, $globalConf);
                            $this->ds_pointerField = $value;
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
                    "'Flex' field '$identifier' configuration '$configKey' is not valid.\n" .
                    "Valid settings are: " . implode(', ', array_keys($properties))
                );
            }
        }
    }

    /**
     * Convert the object configuration to an array for element creation.
     *
     * @return array The configuration array for element creation.
     */
    public function configToElement(): array
    {
        $properties = get_object_vars($this);
        return parent::_configToElement('flex', $properties);
    }
}

/**
 * Class representing a flex field.
 */
final class FlexField extends Field
{
    /**
     * Configuration for the flex field.
     */
    protected FlexFieldConfig $config;

    /**
     * Get the configuration for the flex field.
     *
     * @return FlexFieldConfig The configuration for the flex field.
     */
    public function getConfig(): FlexFieldConfig
    {
        return $this->config;
    }

    /**
     * Convert an array to a field object.
     *
     * @param array $field The array to convert.
     */
    private function _arrayToField(array $field): void
    {
        $this->__arrayToField('flex', $field);
        $field['table'] = $this->table;
        $field['useExistingField'] = $this->useExistingField;
        $field['identifier'] = $this->identifier;
        $config = new FlexFieldConfig();
        $config->arrayToConfig($field, array_keys(get_object_vars($this)));
        $this->config = $config;
    }

    /**
     * Convert the field to an array for element creation.
     *
     * @return array The array for element creation.
     */
    public function fieldToElement(): array
    {
        $element = [];
        $element['config'] = $this->config->configToElement();
        return $element;
    }

    /**
     * Merge the current field with another field.
     *
     * @param FlexField $foreign The foreign field to merge.
     */
    public function mergeField(FlexField $foreign): void
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
     * Convert the field to an array.
     *
     * @return array The field as an array.
     */
    public function fieldToArray(): array
    {
        return $this->__fieldToArray($this->config);
    }

    /**
     * Constructor for the flex field.
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