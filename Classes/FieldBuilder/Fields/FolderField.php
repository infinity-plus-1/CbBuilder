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
 * Configuration class for folder field settings.
 */
final class FolderFieldConfig extends Config
{
    /**
     * Maximum size for auto-sizing.
     */
    protected int $autoSizeMax = -1;

    /**
     * Entry points for the element browser.
     */
    protected array $elementBrowserEntryPoints = [];

    /**
     * Whether to hide the delete icon.
     */
    protected ?bool $hideDeleteIcon = null;

    /**
     * Whether to hide move icons.
     */
    protected ?bool $hideMoveIcons = null;

    /**
     * Maximum number of items allowed.
     */
    protected int $maxitems = -1;

    /**
     * Minimum number of items required.
     */
    protected int $minitems = -1;

    /**
     * Whether multiple items are allowed.
     */
    protected ?bool $multiple = null;

    /**
     * Whether the field is read-only.
     */
    protected ?bool $readOnly = null;

    /**
     * Size of the field.
     */
    protected int $size = -1;

    /**
     * Get the maximum size for auto-sizing.
     *
     * @return int The maximum auto-size value.
     */
    public function getAutoSizeMax(): int
    {
        return $this->autoSizeMax;
    }

    /**
     * Get the entry points for the element browser.
     *
     * @return array The list of entry points.
     */
    public function getElementBrowserEntryPoints(): array
    {
        return $this->elementBrowserEntryPoints;
    }

    /**
     * Get whether the delete icon should be hidden.
     *
     * @return bool|null Whether to hide the delete icon.
     */
    public function isHideDeleteIcon(): ?bool
    {
        return $this->hideDeleteIcon;
    }

    /**
     * Get whether move icons should be hidden.
     *
     * @return bool|null Whether to hide move icons.
     */
    public function isHideMoveIcons(): ?bool
    {
        return $this->hideMoveIcons;
    }

    /**
     * Get the maximum number of items allowed.
     *
     * @return int The maximum number of items.
     */
    public function getMaxItems(): int
    {
        return $this->maxitems;
    }

    /**
     * Get the minimum number of items required.
     *
     * @return int The minimum number of items.
     */
    public function getMinItems(): int
    {
        return $this->minitems;
    }

    /**
     * Get whether multiple items are allowed.
     *
     * @return bool|null Whether multiple items are allowed.
     */
    public function isMultiple(): ?bool
    {
        return $this->multiple;
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
     * @return int The size of the field.
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Set the maximum size for auto-sizing.
     *
     * @param int $autoSizeMax The new maximum auto-size value.
     */
    public function setAutoSizeMax(int $autoSizeMax): void
    {
        $this->autoSizeMax = $autoSizeMax;
    }

    /**
     * Set the entry points for the element browser.
     *
     * @param array $elementBrowserEntryPoints The new list of entry points.
     */
    public function setElementBrowserEntryPoints(array $elementBrowserEntryPoints): void
    {
        $this->elementBrowserEntryPoints = $elementBrowserEntryPoints;
    }

    /**
     * Set whether the delete icon should be hidden.
     *
     * @param bool|null $hideDeleteIcon Whether to hide the delete icon.
     */
    public function setHideDeleteIcon(?bool $hideDeleteIcon): void
    {
        $this->hideDeleteIcon = $hideDeleteIcon;
    }

    /**
     * Set whether move icons should be hidden.
     *
     * @param bool|null $hideMoveIcons Whether to hide move icons.
     */
    public function setHideMoveIcons(?bool $hideMoveIcons): void
    {
        $this->hideMoveIcons = $hideMoveIcons;
    }

    /**
     * Set the maximum number of items allowed.
     *
     * @param int $maxitems The new maximum number of items.
     */
    public function setMaxItems(int $maxitems): void
    {
        $this->maxitems = $maxitems;
    }

    /**
     * Set the minimum number of items required.
     *
     * @param int $minitems The new minimum number of items.
     */
    public function setMinItems(int $minitems): void
    {
        $this->minitems = $minitems;
    }

    /**
     * Set whether multiple items are allowed.
     *
     * @param bool|null $multiple Whether multiple items are allowed.
     */
    public function setMultiple(?bool $multiple): void
    {
        $this->multiple = $multiple;
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
     * Set the size of the field.
     *
     * @param int $size The new size of the field.
     */
    public function setSize(int $size): void
    {
        $this->size = $size;
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

        if ($foreign->getAutoSizeMax() >= 0) {
            $this->autoSizeMax = $foreign->getAutoSizeMax();
        }

        if (!empty($foreign->getElementBrowserEntryPoints())) {
            $this->elementBrowserEntryPoints = $foreign->getElementBrowserEntryPoints();
        }

        if ($foreign->isHideDeleteIcon() !== null) {
            $this->hideDeleteIcon = $foreign->isHideDeleteIcon();
        }

        if ($foreign->isHideMoveIcons() !== null) {
            $this->hideMoveIcons = $foreign->isHideMoveIcons();
        }

        if ($foreign->getMaxItems() >= 0) {
            $this->maxitems = $foreign->getMaxItems();
        }

        if ($foreign->getMinItems() >= 0) {
            $this->minitems = $foreign->getMinItems();
        }

        if ($foreign->isMultiple() !== null) {
            $this->multiple = $foreign->isMultiple();
        }

        if ($foreign->isReadOnly() !== null) {
            $this->readOnly = $foreign->isReadOnly();
        }

        if ($foreign->getSize() >= 0) {
            $this->size = $foreign->getSize();
        }
    }

    /**
     * Validate the auto-size maximum configuration.
     *
     * Ensures that the auto-size maximum value is valid and that 'maxitems' is set appropriately.
     *
     * @param mixed $entry The auto-size maximum value to validate.
     * @param array $config The full configuration array.
     *
     * @throws Exception If validation fails.
     */
    private function _validateAutoSizeMax($entry, $config): void
    {
        $identifier = $config['identifier'];
        if ($num = $this->handleIntegers($entry)) {
            if (!isset($config['maxitems'])) {
                throw new Exception(
                    "'Folder' field '$identifier' configuration 'autoSizeMax' only takes effect when 'maxitems' is set to greater than 1."
                );
            }
            if ($this->maxitems < 1) {
                $this->validateInteger($config['maxitems'], $config, 'maxitems', 'Folder', 1, PHP_INT_MAX);
                $this->maxitems = intval($config['maxitems']);
                if ($this->maxitems < 2) {
                    throw new Exception(
                        "'Folder' field '$identifier' configuration 'autoSizeMax' only takes effect when 'maxitems' is set to greater than 1."
                    );
                }
            }
            $this->autoSizeMax = $num;
        } else {
            throw new Exception(
                "'Folder' field '$identifier' configuration 'autoSizeMax' must be of type integer or a string that represents an integer number."
            );
        }
    }

    /**
     * Validate the element browser entry points configuration.
     *
     * @param mixed $entry The entry points configuration to validate.
     * @param array $config The full configuration array.
     */
    private function _validateElementBrowserEntryPoints($entry, $config): void
    {
        $identifier = $config['identifier'];
        if (!is_array($entry)) {
            throw new Exception(
                "'Folder' field '$identifier' configuration 'elementBrowserEntryPoints' must be of type array."
            );
        }
        $i = 0;
        foreach ($entry as $key => $value) {
            if (!is_string($key)) {
                throw new Exception(
                    "'Folder' field '$identifier' configuration 'elementBrowserEntryPoints[$i][$key]' key must be of type string."
                );
            }

            if ($key === '_default' && (!is_string($value) && !is_int($value))) {
                throw new Exception(
                    "'Folder' field '$identifier' configuration 'elementBrowserEntryPoints[$i][$key]' value must be of type string that represents an entry point if the key is set to '_default'. Fix: _default: '1:/styleguide/', _default: '###CURRENT_PID###', _default: '###PAGE_TSCONFIG_ID###', _default: '###SITEROOT###', ..."
                );
            }
            $i++;
        }
    }

    /**
     * Convert an array configuration to the object's properties.
     *
     * @param array $config The configuration array.
     * @param array $fieldProperties The list of valid field properties.
     */
    public function arrayToConfig(array $config, array $fieldProperties, ?array $globalConf = NULL, mixed $misc = NULL): void
    {
        $globalConf = $globalConf ?? $config;
        $this->checkRequirements($globalConf, [], 'Folder');
        $properties = get_object_vars($this);
        foreach ($config as $configKey => $value) {
            if ($this->isValidConfig($properties, $configKey)) {
                if ($GLOBALS['CbBuilder']['config']['Strict'] === true) {
                    switch ($configKey) {
                        case 'autoSizeMax':
                            $this->_validateAutoSizeMax($value, $globalConf);
                            $this->autoSizeMax = $value;
                            break;
                        case 'elementBrowserEntryPoints':
                            $this->_validateElementBrowserEntryPoints($value, $globalConf);
                            $this->elementBrowserEntryPoints = $value;
                            break;
                        case 'maxitems':
                            $this->validateInteger($value, $globalConf, 'maxitems', 'Folder', 1, PHP_INT_MAX);
                            $this->maxitems = $value;
                            break;
                        case 'minitems':
                            $this->validateInteger($value, $globalConf, 'minitems', 'Folder', 1, PHP_INT_MAX);
                            $this->minitems = $value;
                            break;
                        case 'size':
                            $this->validateInteger($value, $globalConf, 'size', 'Folder', 1, PHP_INT_MAX);
                            $this->size = $value;
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
                    "'Folder' field '$identifier' configuration '$configKey' is not valid.\n" .
                    "Valid settings are: " . implode(', ', array_keys($properties))
                );
            }
        }
    }
}

/**
 * Folder field class.
 */
final class FolderField extends Field
{
    /**
     * The configuration for this folder field.
     */
    protected FolderFieldConfig $config;

    /**
     * Get the configuration for this folder field.
     *
     * @return FolderFieldConfig The folder field configuration.
     */
    public function getConfig(): FolderFieldConfig
    {
        return $this->config;
    }

    /**
     * Initialize the field from an array configuration.
     *
     * @param array $field The field configuration array.
     */
    private function _arrayToField(array $field): void
    {
        $this->__arrayToField('folder', $field);
        $field['table'] = $this->table;
        $field['useExistingField'] = $this->useExistingField;
        $field['identifier'] = $this->identifier;
        $config = new FolderFieldConfig();
        $config->arrayToConfig($field, array_keys(get_object_vars($this)));
        $this->config = $config;
    }

    /**
     * Merge this field's configuration with another field's configuration.
     *
     * @param FolderField $foreign The foreign field to merge with.
     */
    public function mergeField(FolderField $foreign): void
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
     * @return array The field array representation.
     */
    public function fieldToArray(): array
    {
        return $this->__fieldToArray($this->config);
    }

    /**
     * Constructor for the FolderField class.
     *
     * @param array $field The field configuration array.
     * @param string $table The table name.
     */
    public function __construct(array $field, string $table)
    {
        $this->table = $table;
        $this->_arrayToField($field);
    }
}