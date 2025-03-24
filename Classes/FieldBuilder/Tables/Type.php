<?php

declare(strict_types=1);

/**
 * Part of the Cb-Builder
 * 
 * Created by:          Dennis Schwab (dennis.schwab90@icloud.com)
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

namespace DS\CbBuilder\FieldBuilder\Tables;

use DS\CbBuilder\FieldBuilder\Fields\Field;
use DS\CbBuilder\Utility\ArrayParser;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Represents creation options for a table.
 */
final class CreationOptions
{
    /**
     * Whether to save and close after creation.
     */
    protected ?bool $saveAndClose = null;

    /**
     * Default values for creation.
     */
    protected array $defaultValues = [];

    /**
     * Get whether to save and close after creation.
     *
     * @return bool|null Whether to save and close.
     */
    public function getSaveAndClose(): ?bool
    {
        return $this->saveAndClose;
    }

    /**
     * Get the default values for creation.
     *
     * @return array The default values.
     */
    public function getDefaultValues(): array
    {
        return $this->defaultValues;
    }

    /**
     * Set whether to save and close after creation.
     *
     * @param bool|null $saveAndClose Whether to save and close.
     */
    public function setSaveAndClose(?bool $saveAndClose): void
    {
        $this->saveAndClose = $saveAndClose;
    }

    /**
     * Set the default values for creation.
     *
     * @param array $defaultValues The default values to set.
     */
    public function setDefaultValues(array $defaultValues): void
    {
        $this->defaultValues = $defaultValues;
    }

    /**
     * Merge creation options from another instance.
     *
     * @param self $foreign The instance to merge options from.
     */
    public function merge(self $foreign): void
    {
        if ($foreign->getSaveAndClose() !== null) {
            $this->saveAndClose = $foreign->getSaveAndClose();
        }

        $fDefaultValues = $foreign->getDefaultValues();
        if ($fDefaultValues !== []) {
            foreach ($fDefaultValues as $key => $value) {
                $this->defaultValues[$key] = $value;
            }
        }
    }
}

/**
 * Represents a type configuration.
 */
final class Type
{
    /**
     * Columns overrides for the type.
     */
    protected array $columnsOverrides = [];

    /**
     * Creation options for the type.
     */
    protected ?CreationOptions $creationOptions = null;

    /**
     * Preview renderer for the type.
     */
    protected string $previewRenderer = '';

    /**
     * Show item configuration for the type.
     */
    protected string $showitem = '';

    /**
     * Subtype value field for the type.
     */
    protected string $subtype_value_field = '';

    /**
     * Subtypes to add for the type.
     */
    protected array $subtypes_addlist = [];

    /**
     * Subtypes to exclude for the type.
     */
    protected array $subtypes_excludelist = [];

    /**
     * Constants for parsing modes.
     */
    const PARSE_WITH_KEY_MODE = 1;
    const PARSE_WITHOUT_KEY_MODE = 2;

    /**
     * Properties to exclude when parsing.
     */
    const PROPERTY_EXCLUDE = [
        'identifier', 'arrayFields', 'content', 'tableType', 'table'
    ];

    /**
     * Get the identifier of the type.
     *
     * @return string The identifier.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Get the columns overrides for the type.
     *
     * @return array The columns overrides.
     */
    public function getColumnsOverrides(): array
    {
        return $this->columnsOverrides;
    }

    /**
     * Get the creation options for the type.
     *
     * @return CreationOptions|null The creation options.
     */
    public function getCreationOptions(): ?CreationOptions
    {
        return $this->creationOptions;
    }

    /**
     * Get the preview renderer for the type.
     *
     * @return string The preview renderer.
     */
    public function getPreviewRenderer(): string
    {
        return $this->previewRenderer;
    }

    /**
     * Get the show item configuration for the type.
     *
     * @return string The show item configuration.
     */
    public function getShowitem(): string
    {
        return $this->showitem;
    }

    /**
     * Get the subtype value field for the type.
     *
     * @return string The subtype value field.
     */
    public function getSubtypeValueField(): string
    {
        return $this->subtype_value_field;
    }

    /**
     * Get the subtypes to add for the type.
     *
     * @return array The subtypes to add.
     */
    public function getSubtypesAddlist(): array
    {
        return $this->subtypes_addlist;
    }

    /**
     * Get the subtypes to exclude for the type.
     *
     * @return array The subtypes to exclude.
     */
    public function getSubtypesExcludelist(): array
    {
        return $this->subtypes_excludelist;
    }

    /**
     * Set the identifier of the type.
     *
     * @param string $identifier The identifier to set.
     */
    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    /**
     * Set the columns overrides for the type.
     *
     * @param array $columnsOverrides The columns overrides to set.
     */
    public function setColumnsOverrides(array $columnsOverrides): void
    {
        $this->columnsOverrides = $columnsOverrides;
    }

    /**
     * Set the creation options for the type.
     *
     * @param CreationOptions|null $creationOptions The creation options to set.
     */
    public function setCreationOptions(?CreationOptions $creationOptions): void
    {
        $this->creationOptions = $creationOptions;
    }

    /**
     * Set the preview renderer for the type.
     *
     * @param string $previewRenderer The preview renderer to set.
     */
    public function setPreviewRenderer(string $previewRenderer): void
    {
        $this->previewRenderer = $previewRenderer;
    }

    /**
     * Set the show item configuration for the type.
     *
     * @param array $showitem The show item configuration to set.
     */
    public function setShowitem(array $showitem): void
    {
        $this->showitem = $showitem;
    }

    /**
     * Set the subtype value field for the type.
     *
     * @param string $subtypeValueField The subtype value field to set.
     */
    public function setSubtypeValueField(string $subtypeValueField): void
    {
        $this->subtype_value_field = $subtypeValueField;
    }

    /**
     * Set the subtypes to add for the type.
     *
     * @param array $subtypesAddlist The subtypes to add.
     */
    public function setSubtypesAddlist(array $subtypesAddlist): void
    {
        $this->subtypes_addlist = $subtypesAddlist;
    }

    /**
     * Set the subtypes to exclude for the type.
     *
     * @param array $subtypesExcludelist The subtypes to exclude.
     */
    public function setSubtypesExcludelist(array $subtypesExcludelist): void
    {
        $this->subtypes_excludelist = $subtypesExcludelist;
    }

    /**
     * Check if a field is in a palette.
     *
     * @param string $fieldName The name of the field to check.
     *
     * @return bool True if the field is in a palette, false otherwise.
     */
    private function _fieldIsInPalette(string $fieldName): bool
    {
        foreach ($this->arrayFields as $value) {
            $field = $value;
            if (isset($field['type'])) {
                if ($field['type'] === 'Palette') {
                    
                    if (isset($field['fields'])) {
                        $fields = [];
                        if (is_string($field['fields'])) {
                            $fields = GeneralUtility::trimExplode(',', $field['fields']);
                        } elseif (is_array($field['fields'])) {
                            $fields = $field['fields'];
                        } else {
                            continue;
                        }
                        if (is_array($fields) && in_array($fieldName, $fields)) {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    /**
     * Parse the show item configuration.
     *
     * @return string The parsed show item configuration as a string.
     */
    private function _parseSetItem(): string
    {
        $showitem[] = "cb_index";
        foreach ($this->arrayFields as $value) {
            $field = $value;
            if (isset($field['type'])) {
                if ($field['type'] === 'Palette') {
                    if (isset($field['identifier'])) {
                        $label = $field['label'] ?? '';
                        $identifier = $field['identifier'];
                        $showitem[] = "--palette--;$label;$identifier";
                    }
                } elseif ($field['type'] === 'Linebreak') {
                    $showitem[] = "--linebreak--";
                } else {
                    if (isset($field['identifier']) && !$this->_fieldIsInPalette($field['identifier'])) {
                        $showitem[] = $field['identifier'];
                    }
                }
            }
        }
        return implode(',', $showitem);
    }

    /**
     * Merge override settings for a field.
     *
     * @param Field $field The field to merge.
     * @param string $identifier The identifier of the field.
     */
    private function _mergeOverride(Field $field, string $identifier): void
    {
        $fieldArray = $field->fieldToArray();
        
        foreach ($fieldArray as $key => $value) {
            if (is_array($value)) {
                $this->columnsOverrides[$identifier][$key] = array_replace_recursive($this->columnsOverrides[$identifier][$key], $value);
            } else {
                $this->columnsOverrides[$identifier][$key] = $value;
            }
                
        }
    }

    /**
     * Parse override settings into an array.
     *
     * @return array The parsed override settings.
     */
    private function _parseOverrides(): array
    {
        $overrides = [];
        foreach ($this->columnsOverrides as $key => $value) {
            if (is_object($value)) {
                $overrides[$key] = $value->fieldToArray();
            } elseif (is_array($value)) {
                $overrides[$key] = $value;
            }
        }
        return $overrides;
    }

    /**
     * Add a field to the override settings.
     *
     * @param Field $field The field to add.
     */
    public function addColumnToOverride(Field $field): void
    {
        $identifier = $field->getIdentifier();
        if (isset($this->columnsOverrides[$identifier])) {
            $this->_mergeOverride($field, $identifier);
        } else {
            $this->columnsOverrides[$identifier] = $field;
        }
    }

    /**
     * Check if a configuration property is valid.
     *
     * @param array $properties The properties to check against.
     * @param string $config The configuration property to check.
     *
     * @return bool True if the property is valid, false otherwise.
     */
    protected function isValidConfig(array $properties, string $config): bool
    {
        return array_key_exists($config, $properties);
    }

    /**
     * Convert the type configuration to an array.
     *
     * @return array The type configuration as an array.
     */
    public function typeToArray(): array
    {
        $type = [];
        $properties = array_keys(get_object_vars($this));
        foreach ($properties as $property) {
            switch ($property) {
                case 'showitem':
                    $type['showitem'] = $this->_parseSetItem();
                    break;
                case 'columnsOverrides':
                    $type['columnsOverrides'] = $this->_parseOverrides();
                    break;
                default:
                    if (
                        ((is_string($this->$property) && $this->$property !== '')
                        || (is_int($this->$property) && $this->$property >= 0)
                        || (is_float($this->$property) && $this->$property >= 0.0)
                        || (is_array($this->$property) && !empty($this->$property))
                        || (is_bool($this->$property) && $this->$property !== null))
                        && !in_array($property, self::PROPERTY_EXCLUDE)
                    ) {
                        $type[$property] = $this->$property;
                    }
                    break;
            }
        }
        return $type;
    }

    /**
     * Parse the type configuration into a string.
     *
     * @param int $mode The parsing mode.
     * @param int $level The indentation level.
     *
     * @return string The parsed type configuration as a string.
     */
    public function parseType(int $mode, int $level): string
    {
        $type = $this->typeToArray();
        return  ($mode === self::PARSE_WITH_KEY_MODE)
                ? ArrayParser::arrayToString($type, $this->identifier, ($level+1), true)
                : ($mode === self::PARSE_WITHOUT_KEY_MODE
                ? ArrayParser::arrayToString($type, '', ($level+1), false)
                : '');
    }

    /**
     * Convert an array to type configuration.
     */
    public function arrayToType(): void
    {
        $properties = get_object_vars($this);
        foreach ($this->content as $key => $value) {
            if ($this->isValidConfig($properties, $key) && !in_array($key, self::PROPERTY_EXCLUDE)) {
                if ($GLOBALS['CbBuilder']['config']['Strict'] === true) {
                    switch ($key) {
                        case 'showitem':
                            //$this->showitem = GeneralUtility::trimExplode(',', $value);
                            break;
                        default:
                            $this->$key = $value;
                            break;
                    }
                } else {
                    $this->$key = $value;
                }
            }
        }
    }

    /**
     * Constructor for the Type class.
     *
     * @param array $content The content of the type.
     * @param string $identifier The identifier of the type.
     * @param string $table The name of the table.
     * @param array $arrayFields The array fields.
     * @param int $tableType The type of the table.
     */
    public function __construct (
        private array $content,
        private string $identifier,
        private string $table,
        private array $arrayFields,
        private int $tableType
    ) {
        $this->arrayToType();
    }
}