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

use DS\CbBuilder\FieldBuilder\Tables\Table;
use DS\CbBuilder\Utility\ArrayParser;
use Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class representing a palette container.
 */
final class PaletteContainer
{
    /**
     * Whether the palette is hidden.
     */
    protected ?bool $isHiddenPalette = null;

    /**
     * Fields to show in the palette.
     */
    protected array $showitem = [];

    /**
     * Label for the palette.
     */
    protected string $label = '';

    /**
     * Description for the palette.
     */
    protected string $description = '';

    /**
     * Table name for the palette.
     */
    protected string $table = '';

    /**
     * Parent table for the palette.
     */
    protected ?Table $parentTable = null;

    /**
     * Identifier for the palette.
     */
    protected string $identifier = '';

    /**
     * Properties to exclude from parsing.
     */
    const PROPERTY_EXCLUDE = [
        'identifier', 'parentTable', 'table'
    ];

    /**
     * Parsing modes.
     */
    const PARSE_WITH_KEY_MODE = 1;
    const PARSE_WITHOUT_KEY_MODE = 2;

    /**
     * Get the fields to show in the palette.
     *
     * @return array The fields to show.
     */
    public function getShowItem(): array
    {
        return $this->showitem;
    }

    /**
     * Get whether the palette is hidden.
     *
     * @return bool|null Whether the palette is hidden.
     */
    public function getIsHiddenPalette(): bool|null
    {
        return $this->isHiddenPalette;
    }

    /**
     * Get the label for the palette.
     *
     * @return string The label.
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Get the description for the palette.
     *
     * @return string The description.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get the table name for the palette.
     *
     * @return string The table name.
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Get the identifier for the palette.
     *
     * @return string The identifier.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Get the parent table for the palette.
     *
     * @return Table The parent table.
     */
    public function getParentTable(): Table
    {
        return $this->parentTable;
    }

    /**
     * Validate and set the fields for the palette.
     *
     * @param mixed $fields The fields to validate and set.
     *
     * @throws Exception If the fields are invalid.
     */
    private function _validateAndSetFields(mixed $fields): void
    {
        if (is_string($fields)) {
            $fields = GeneralUtility::trimExplode(',', $fields);
        } else if (!is_array($fields)) {
            throw new Exception(
                "'Palette' container '$this->identifier' fields must be of type string representing a comma-separated list or " .
                "an array. Fix:\n fields: header, bodytext\nOR\nfields:\n  - header\n  - bodytext"
            );
        }

        foreach ($fields as $field) {
            if (!$this->parentTable->globalElementExists($field)) {
                throw new Exception(
                    "'Palette' container '$this->identifier' contains field '$field' that is not in parent's scope.\n" .
                    "Fix: All fields added to a palette must be declared in the corresponding table where the palette belongs to.\n" .
                    "E.g., tt_content for the first dimension or a Collection for every dimension afterwards.\n" .
                    "- identifier: myCollection\n  type: Collection\n  fields:\n    - identifier: myPalette\n      type: Palette\n" .
                    "      fields: 'header, bodytext'\n    - identifier: header\n      type: Text\n" .
                    "    - identifier: bodytext\n      type: Textarea\n" .
                    "    - identifier: mySecondCollection\n      type: Collection\n      fields:\n        - identifier: thisIsOutOfScope\n" .
                    "          type: Text\n- identifier: thisIsOutOfScopeAsWell\n  type: Text"
                );
            }
        }
        $this->showitem = $fields;
    }

    /**
     * Add a field to the palette.
     *
     * @param string $identifier The identifier of the field to add.
     * @param bool $trim Whether to trim the field identifier.
     */
    public function addToPalette(string $identifier, ?bool $trim = false): void
    {
        if (!$trim) {
            $this->showitem[] = $identifier;
        } else {
            $this->showitem[] = trim($identifier);
        }
    }

    /**
     * Convert the Palette class object into an array.
     * 
     * @return array The parsed array.
     */
    public function paletteToArray(): array
    {
        $palette = [];
        $properties = get_object_vars($this);
        foreach ($properties as $key => $property) {
            switch ($key) {
                case 'parentTable':
                case 'table':
                    break;
                case 'showitem':
                    $palette['fields'] = implode(', ', $property);
                    break;
                default:
                    if (
                        (is_string($property) && $property !== '')
                        || (is_int($property) && $property >= 0)
                        || (is_float($property) && $property >= 0.0)
                        || (is_array($property) && !empty($property))
                        || (is_bool($property) && $property !== null)
                    ) {
                        $palette[$key] = $property;
                    }
                    break;
            }
        }
        $palette['type'] = 'Palette';
        return $palette;
    }

    /**
     * Convert an array to a palette.
     *
     * @param array $palette The array to convert.
     * @param Table $parentElement The parent table.
     */
    private function _arrayToPalette(array $palette, Table $parentElement): void
    {
        $this->parentTable = $parentElement;
        $properties = get_object_vars($this);
        foreach ($palette as $key => $value) {
            if (array_key_exists($key, $properties)) {
                if (!in_array($key, self::PROPERTY_EXCLUDE)) {
                    $this->$key = $value;
                }
            }
        }
    }

    /**
     * Merge the current palette with another palette.
     *
     * @param PaletteContainer $foreign The foreign palette to merge.
     */
    public function mergePalettes(PaletteContainer $foreign): void
    {
        if ($foreign->getShowItem() !== []) {
            $this->showitem = $foreign->getShowItem();
        }

        if ($foreign->getIsHiddenPalette() !== null) {
            $this->isHiddenPalette = $foreign->getIsHiddenPalette();
        }

        if ($foreign->getDescription() !== '') {
            $this->description = $foreign->getDescription();
        }

        if ($foreign->getLabel() !== '') {
            $this->label = $foreign->getLabel();
        }
    }

    /**
     * Parse the palette based on the given mode and level.
     *
     * @param int $mode The mode to parse with.
     * @param int $level The level to parse with.
     *
     * @return string The parsed palette.
     */
    public function parsePalette(int $mode, int $level): string
    {
        $palette = [];
        $properties = get_object_vars($this);
        foreach ($properties as $key => $property) {
            switch ($key) {
                case 'showitem':
                    $palette[$key] = implode(',', $this->showitem);
                    break;
                default:
                    if (
                        ((is_string($property) && $property !== '')
                        || (is_int($property) && $property >= 0)
                        || (is_float($property) && $property >= 0.0)
                        || (is_array($property) && !empty($property))
                        || (is_bool($property) && $property !== null))
                        && !in_array($key, self::PROPERTY_EXCLUDE)
                    ) {
                        $palette[$key] = $property;
                    }
                    break;
            }
        }
        return ($mode === self::PARSE_WITH_KEY_MODE)
            ? ArrayParser::arrayToString($palette, $this->identifier, ($level + 1), true)
            : ($mode === self::PARSE_WITHOUT_KEY_MODE
                ? ArrayParser::arrayToString($palette, '', ($level + 1), false)
                : '');
    }

    /**
     * Inject a field palette into the container.
     *
     * @param array $field The field configuration.
     * @param string $table The table name.
     * @param Table $parentElement The parent table.
     *
     * @throws Exception If the parent element is not valid.
     */
    public function injectFieldPalette(array $field, string $table, Table $parentElement): void
    {
        $this->table = $table;
        $this->identifier = $field['identifier'];
        if ($parentElement === null) {
            throw new Exception(
                "'Palette' container '$this->identifier' has no valid parent element set."
            );
        }
        $this->parentTable = $parentElement;
        if (!isset($field['fields']) || (isset($field['fields']) && $field['fields'] === [])) {
            throw new Exception(
                "'Palette' container '$this->identifier' must have at least one field set in 'fields'. Fix:\n" .
                "- identifier: myPalette\n      type: Palette\n      fields: 'header, bodytext'"
            );
        }
        $this->_validateAndSetFields($field['fields']);
        $this->_arrayToPalette($field, $parentElement);
    }

    /**
     * Inject a raw palette into the container.
     *
     * @param string $identifier The identifier for the palette.
     * @param array $properties The properties for the palette.
     * @param string $table The table name.
     * @param Table $parentElement The parent table.
     *
     * @throws Exception If the parent element is not valid.
     */
    public function injectRawPalette(
        string $identifier,
        array $properties,
        string $table,
        Table $parentElement
    ): void {
        $this->identifier = $identifier;
        $this->table = $table;
        if ($parentElement === null) {
            throw new Exception(
                "'Palette' container '$this->identifier' has no valid parent element."
            );
        }
        $this->parentTable = $parentElement;
        $this->label = $properties['label'] ?? '';
        $this->description = $properties['description'] ?? '';
        if (!isset($properties['showitem'])) {
            throw new Exception(
                "'Palette' container '$identifier' must contain fields in 'showitem'."
            );
        }
        $items = GeneralUtility::trimExplode(',', $properties['showitem']);
        $this->_validateAndSetFields($items);
        $this->isHiddenPalette = $properties['isHiddenPalette'] ?? null;
    }
}