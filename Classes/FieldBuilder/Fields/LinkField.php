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
 * Configuration class for link field settings.
 */
final class LinkFieldConfig extends Config
{
    /**
     * Default value for the link field.
     */
    protected string $default = '';

    /**
     * Whether the link field can be null.
     */
    protected ?bool $nullable = null;

    /**
     * Whether the link field is required.
     */
    protected ?bool $required = null;

    /**
     * Array of allowed link types.
     */
    protected array $allowedTypes = ['*'];

    /**
     * Get the default value for the link field.
     *
     * @return string The default value.
     */
    public function getDefault(): string
    {
        return $this->default;
    }

    /**
     * Get whether the link field can be null.
     *
     * @return bool|null Whether the field can be null.
     */
    public function isNullable(): ?bool
    {
        return $this->nullable;
    }

    /**
     * Get whether the link field is required.
     *
     * @return bool|null Whether the field is required.
     */
    public function isRequired(): ?bool
    {
        return $this->required;
    }

    /**
     * Get the array of allowed link types.
     *
     * @return array The allowed link types.
     */
    public function getAllowedTypes(): array
    {
        return $this->allowedTypes;
    }

    /**
     * Set the default value for the link field.
     *
     * @param string $default The default value.
     */
    public function setDefault(string $default): void
    {
        $this->default = $default;
    }

    /**
     * Set whether the link field can be null.
     *
     * @param bool|null $nullable Whether the field can be null.
     */
    public function setNullable(?bool $nullable): void
    {
        $this->nullable = $nullable;
    }

    /**
     * Set whether the link field is required.
     *
     * @param bool|null $required Whether the field is required.
     */
    public function setRequired(?bool $required): void
    {
        $this->required = $required;
    }

    /**
     * Set the array of allowed link types.
     *
     * @param array $allowedTypes The allowed link types.
     */
    public function setAllowedTypes(array $allowedTypes): void
    {
        $this->allowedTypes = $allowedTypes;
    }

    /**
     * List of valid link types.
     */
    const VALID_TYPES = [
        '*', 'page', 'url', 'file', 'folder', 'email', 'telephone', 'record'
    ];

    /**
     * Validate the allowed link types.
     *
     * @param mixed $entry The entry to validate.
     * @param array $config The configuration array.
     *
     * @throws Exception If validation fails.
     */
    private function _validateAllowedTypes($entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_array($entry)) {
            throw new Exception(
                "'Link' field '$identifier' configuration 'allowedTypes' must be of type array."
            );
        }
        foreach ($entry as $type) {
            if (!is_string($type)) {
                throw new Exception(
                    "'Link' field '$identifier' configuration 'allowedTypes[0-6]' entries must be of type string."
                );
            }
            if ($type === '*' && count($entry) > 1) {
                throw new Exception(
                    "'Link' field '$identifier' configuration 'allowedTypes[]' found '*' as entry, but more keywords " .
                    "are declared. '*' already accepts all available keywords. Fix: Either remove '*' or all other keywords."
                );
            }
            if (!in_array($type, self::VALID_TYPES)) {
                throw new Exception(
                    "'Link' field '$identifier' configuration 'allowedTypes[]' allowed keywords are: '*', 'page', 'url', " .
                    "'file', 'folder', 'email', 'telephone', 'record'"
                );
            }
        }
    }

    /**
     * Convert an array configuration to the current link field configuration.
     *
     * @param array $config The configuration array.
     * @param array $fieldProperties The properties of the field.
     */
    public function arrayToConfig(array $config, array $fieldProperties, ?array $globalConf = NULL, mixed $misc = NULL): void
    {
        $globalConf = $globalConf ?? $config;
        $this->checkRequirements($globalConf, ['identifier'], 'Link');
        $properties = get_object_vars($this);
        foreach ($config as $configKey => $value) {
            if ($this->isValidConfig($properties, $configKey)) {
                if ($GLOBALS['CbBuilder']['config']['Strict'] === true) {
                    switch ($configKey) {
                        case 'allowedTypes':
                            $this->_validateAllowedTypes($value, $globalConf);
                            $this->allowedTypes = $value;
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
                    "'Link' field '$identifier' configuration '$configKey' is not valid.\n" .
                    "Valid settings are: " . implode(', ', array_keys($properties))
                );
            }
        }
    }

    /**
     * Merge the configuration with another link field configuration.
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

        if ($foreign->isNullable() !== null) {
            $this->nullable = $foreign->isNullable();
        }

        if ($foreign->isRequired() !== null) {
            $this->required = $foreign->isRequired();
        }

        if (!empty($foreign->getAllowedTypes())) {
            $this->allowedTypes = $foreign->getAllowedTypes();
        }
    }
}

/**
 * Class representing a link field.
 */
final class LinkField extends Field
{
    /**
     * Configuration for the link field.
     */
    protected LinkFieldConfig $config;

    /**
     * Get the configuration for the link field.
     *
     * @return LinkFieldConfig The field configuration.
     */
    public function getConfig(): LinkFieldConfig
    {
        return $this->config;
    }

    /**
     * Convert an array to a link field.
     *
     * @param array $field The array representing the field.
     */
    private function _arrayToField(array $field): void
    {
        $this->__arrayToField('link', $field);
        $field['table'] = $this->table;
        $field['useExistingField'] = $this->useExistingField;
        $field['identifier'] = $this->identifier;
        $config = new LinkFieldConfig();
        $config->arrayToConfig($field, array_keys(get_object_vars($this)));
        $this->config = $config;
    }

    /**
     * Merge the current field with another link field.
     *
     * @param LinkField $foreign The foreign field to merge.
     */
    public function mergeField(LinkField $foreign): void
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
     * Constructor for the link field.
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