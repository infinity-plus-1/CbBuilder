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
 * Configuration class for UUID field settings.
 */
final class UuidFieldConfig extends Config
{
    /**
     * Whether to enable copying the UUID to the clipboard.
     */
    protected ?bool $enableCopyToClipboard = null;

    /**
     * Whether the field is required.
     */
    protected ?bool $required = null;

    /**
     * The size of the UUID field.
     */
    protected int $size = -1;

    /**
     * The version of the UUID.
     */
    protected int $version = -1;

    /**
     * Get whether copying the UUID to the clipboard is enabled.
     *
     * @return bool|null Whether copying to clipboard is enabled.
     */
    public function getEnableCopyToClipboard(): ?bool
    {
        return $this->enableCopyToClipboard;
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
     * Get the size of the UUID field.
     *
     * @return int The size of the UUID field.
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Get the version of the UUID.
     *
     * @return int The version of the UUID.
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * Set whether copying the UUID to the clipboard is enabled.
     *
     * @param bool|null $enableCopyToClipboard Whether to enable copying to clipboard.
     */
    public function setEnableCopyToClipboard(?bool $enableCopyToClipboard): void
    {
        $this->enableCopyToClipboard = $enableCopyToClipboard;
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
     * Set the size of the UUID field.
     *
     * @param int $size The size of the UUID field.
     */
    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    /**
     * Set the version of the UUID.
     *
     * @param int $version The version of the UUID.
     */
    public function setVersion(int $version): void
    {
        $this->version = $version;
    }

    /**
     * Merge the current configuration with another configuration.
     *
     * @param self $foreign The foreign configuration to merge with.
     */
    public function mergeConfig(Config $foreign): void
    {
        if (!$foreign instanceof self) {
            throw new InvalidArgumentException (
                "Config 'foreign' must be of type " . get_class($this)
            );
        }
        $this->mergeMainConfig($foreign);
        
        if ($foreign->getEnableCopyToClipboard() !== null) {
            $this->enableCopyToClipboard = $foreign->getEnableCopyToClipboard();
        }

        if ($foreign->getRequired() !== null) {
            $this->required = $foreign->getRequired();
        }

        if ($foreign->getSize() !== -1) {
            $this->size = $foreign->getSize();
        }

        if ($foreign->getVersion() !== -1) {
            $this->version = $foreign->getVersion();
        }
    }

    /**
     * Valid UUID versions.
     */
    const VALID_VERSIONS = [
        4, 6, 7
    ];

    /**
     * Validate the UUID version.
     *
     * @param mixed $entry The version to validate.
     * @param array $config The configuration array.
     *
     * @throws Exception If the version is invalid.
     */
    private function _validateVersion($entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (($entry = $this->handleIntegers($entry)) === null) {
            throw new Exception (
                "'Uuid' field '$identifier' configuration 'version' must be of type integer."
            );
        }
        if (!in_array($entry, self::VALID_VERSIONS)) {
            throw new Exception (
                "'Uuid' field '$identifier' configuration 'version' $entry is not a valid value.\n" .
                "Valid values are: " . implode(',', self::VALID_VERSIONS)
            );
        }
    }

    /**
     * Convert an array configuration to the current config.
     *
     * @param array $config The configuration array.
     * @param array $fieldProperties The properties of the field.
     */
    public function arrayToConfig(array $config, array $fieldProperties, ?array $globalConf = NULL, mixed $misc = NULL): void
    {
        $globalConf = $globalConf ?? $config;
        $this->checkRequirements($globalConf, [], 'Uuid');
        $properties = get_object_vars($this);
        foreach ($config as $configKey => $value) {
            if ($this->isValidConfig($properties, $configKey)) {
                if ($GLOBALS['CbBuilder']['config']['Strict'] === true) {
                    switch ($configKey) {
                        case 'size':
                            $this->validateInteger($value, $globalConf, 'size', 'Uuid', 10, 50);
                            $this->size = $value;
                            break;
                        case 'version':
                            $this->_validateVersion($value, $globalConf);
                            $this->version = $value;
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
                throw new Exception (
                    "'Uuid' field '$identifier' configuration '$configKey' is not valid.\n" .
                    "Valid settings are: " . implode(', ', array_keys($properties))
                );
            }
        }
    }
}

/**
 * Class representing a UUID field.
 */
final class UuidField extends Field
{
    /**
     * The configuration for the UUID field.
     */
    protected UuidFieldConfig $config;

    /**
     * Get the configuration for the UUID field.
     *
     * @return UuidFieldConfig The configuration.
     */
    public function getConfig(): UuidFieldConfig
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
        $this->__arrayToField('uuid', $field);
        $field['table'] = $this->table;
        $field['useExistingField'] = $this->useExistingField;
        $field['identifier'] = $this->identifier;
        $config = new UuidFieldConfig();
        $config->arrayToConfig($field, array_keys(get_object_vars($this)));
        $this->config = $config;
        
    }

    /**
     * Merge the current field with another field.
     *
     * @param self $foreign The foreign field to merge with.
     */
    public function mergeField(UuidField $foreign): void
    {
        $this->config->mergeConfig($foreign->getConfig());
        $this->mergeFields($foreign);
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
     * Parse the field based on the given mode and level.
     *
     * @param int $mode The parsing mode.
     * @param int $level The parsing level.
     *
     * @return string The parsed field.
     */
    public function parseField(int $mode, int $level): string
    {
        return $this->__parseField($this->config, $mode, $level);
    }

    /**
     * Constructor for the UUID field.
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