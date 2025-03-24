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

use InvalidArgumentException;

/**
 * Class CustomFieldConfig
 * 
 * Represents the configuration for a custom field.
 */
final class CustomFieldConfig extends Config
{
    /**
     * The parameters for the custom field.
     */
    protected array $parameters = [];

    /**
     * Get the parameters for the custom field.
     * 
     * @return array The parameters.
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Set the parameters for the custom field.
     * 
     * @param array $parameters The parameters to set.
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * Merge the configuration from another CustomFieldConfig instance.
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

        if ($foreign->getParameters() !== NULL) {
            $this->parameters = $foreign->getParameters();
        }
    }

    /**
     * Convert an array configuration to the object's properties.
     * 
     * @param array $config The configuration array.
     * 
     * @throws Exception If a required property is missing or if validation fails.
     */
    public function arrayToConfig(array $config, array $fieldProperties, ?array $globalConf = NULL, mixed $misc = NULL): void
    {
        $globalConf = $globalConf ?? $config;
        $this->checkRequirements($globalConf, ['identifier', 'renderType'], 'Custom');
        $properties = get_object_vars($this);
        foreach ($config as $configKey => $value) {
            if ($this->isValidConfig($properties, $configKey)) {
                if ($GLOBALS['CbBuilder']['config']['Strict'] === true) {
                    switch ($configKey) {
                        default:
                            $this->addGenericConfig($configKey, $value, $globalConf, $this, $fieldProperties);
                            break;
                    }
                } else {
                    $this->$configKey = $value;
                }
            }
        }
    }

    /**
     * Convert the object's configuration to an array for element creation.
     * 
     * @return array The configuration array.
     */
    public function configToElement(): array
    {
        $properties = get_object_vars($this);
        return parent::_configToElement('user', $properties);
    }
}

/**
 * Class CustomField
 * 
 * Represents a custom field.
 */
final class CustomField extends Field
{
    /**
     * The configuration for this custom field.
     */
    protected CustomFieldConfig $config;

    /**
     * Get the configuration of this custom field.
     * 
     * @return CustomFieldConfig The configuration.
     */
    public function getConfig(): CustomFieldConfig
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
        $config = new CustomFieldConfig();
        $config->arrayToConfig($field, array_keys(get_object_vars($this)));
        $this->config = $config;
        $this->__arrayToField('select', $field);
    }

    /**
     * Convert the field to an array for element creation.
     * 
     * @return array The field array.
     */
    public function fieldToElement(): array
    {
        $element = [];
        $element['config'] = $this->config->configToElement();
        return $element;
    }

    /**
     * Merge the configuration of another custom field into this one.
     * 
     * @param CustomField $foreign The field to merge.
     */
    public function mergeField(CustomField $foreign): void
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
     * Constructor for the CustomField class.
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