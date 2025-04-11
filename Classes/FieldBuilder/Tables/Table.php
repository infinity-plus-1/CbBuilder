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

use DS\CbBuilder\Collector\Collector;
use DS\CbBuilder\Config\CbBuilderConfig;
use DS\CbBuilder\FieldBuilder\FieldBuilder;
use DS\CbBuilder\FieldBuilder\Fields\CollectionContainer;
use DS\CbBuilder\FieldBuilder\Fields\Field;
use DS\CbBuilder\FieldBuilder\Fields\FileField;
use DS\CbBuilder\FieldBuilder\Fields\LinebreakField;
use DS\CbBuilder\FieldBuilder\Fields\PaletteContainer;
use DS\CbBuilder\FileCreater\FileCreater;
use DS\CbBuilder\Wrapper\Wrapper;
use DS\CbBuilder\Utility\ArrayParser;
use DS\CbBuilder\Utility\SimpleDatabaseQuery;
use Exception;
use InvalidArgumentException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Represents a foreign type configuration.
 */
final class ForeignType
{
    /**
     * The name of the foreign table.
     */
    protected string $foreignTable = '';

    /**
     * The type of the foreign field.
     */
    protected string $type = '';

    /**
     * The identifier of the foreign field.
     */
    protected string $identifier = '';

    /**
     * Get the name of the foreign table.
     *
     * @return string The foreign table name.
     */
    public function getForeignTable(): string
    {
        return $this->foreignTable;
    }

    /**
     * Set the name of the foreign table.
     *
     * @param string $foreignTable The foreign table name to set.
     */
    public function setForeignTable(string $foreignTable): void
    {
        $this->foreignTable = $foreignTable;
    }

    /**
     * Get the type of the foreign field.
     *
     * @return string The foreign field type.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set the type of the foreign field.
     *
     * @param string $type The foreign field type to set.
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * Get the identifier of the foreign field.
     *
     * @return string The foreign field identifier.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Set the identifier of the foreign field.
     *
     * @param string $identifier The foreign field identifier to set.
     */
    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    /**
     * Check if the provided field configuration matches a foreign type.
     *
     * @param array $field The field configuration to check.
     *
     * @return bool True if the field matches a foreign type, false otherwise.
     */
    public function checkForeignType(array $field): bool
    {
        if (isset($field[0][0]) && $field[0][0] === 'TCA') {
            if (isset($field[1][0])) {
                $this->foreignTable = $field[1][0];
            }
            if (isset($field[2][0])) {
                $this->type = $field[2][0];
            }
            if (isset($field[3][0])) {
                $this->identifier = $field[3][0];
            }
            return $this->foreignTable !== '' && $this->type !== '' && $this->identifier !== '';
        }
        return false;
    }
}

/**
 * Represents an unknown type.
 */
final class UnknownType
{
    /**
     * Get the identifier of the unknown type.
     *
     * @return string The identifier.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Set the identifier of the unknown type.
     *
     * @param string $identifier The identifier to set.
     */
    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    /**
     * Constructor for UnknownType.
     *
     * @param string $identifier The identifier for the unknown type.
     */
    public function __construct(protected string $identifier) { }
}

/**
 * Base class for tables.
 */
class Table
{
    /**
     * Palettes defined for the table.
     */
    protected array $palettes = [];

    /**
     * Global palettes available for the table.
     */
    protected array $globalPalettes = [];

    /**
     * Columns defined for the table.
     */
    protected array $columns = [];

    /**
     * All fields, including local and global ones.
     */
    protected array $allFields = [];

    /**
     * Global columns available for the table.
     */
    protected array $globalColumns = [];

    /**
     * Fields defined for the table.
     */
    protected array $fields = [];

    /**
     * All elements, including palettes, sorted.
     */
    protected array $sortedElements = [];

    /**
     * Array fields defined for the table.
     */
    protected array $arrayFields = [];

    /**
     * The name of the table.
     */
    protected string $table = '';

    /**
     * Whether this table overrides another.
     */
    protected bool $isOverride = false;

    /**
     * The type of the table.
     */
    protected ?Type $type = null;

    /**
     * Types defined for the table.
     */
    protected array $types = [];

    /**
     * Type identifiers for the table.
     */
    protected array $typeIdentifiers = [];

    /**
     * Content of the table.
     */
    protected array $content = [];

    /**
     * String representation of the table content.
     */
    protected string $stringContent = '';

    /**
     * Control settings for the table.
     */
    protected array $ctrl = [];

    /**
     * Identifier for the table.
     */
    protected string $identifier = '';

    /**
     * Parent table if applicable.
     */
    protected ?Table $parentTable = null;

    /**
     * Path to the table.
     */
    protected string $path = '';

    /**
     * Constants for table types.
     */
    const TT_CONTENT_TABLE = 1;
    const COLLECTION_TABLE = 2;

    /**
     * Check if a global element exists.
     *
     * @param string $field The name of the field to check.
     *
     * @return bool True if the field exists globally, false otherwise.
     */
    public function globalElementExists(string $field): bool
    {
        $sdq = new SimpleDatabaseQuery();
        if ($sdq->fieldExists($field, [$this->table])) {
            return true;
        } elseif (FieldBuilder::fieldExists($field, '', '', $this->arrayFields)) {
            return true;
        }
        return (isset($this->globalColumns[$field]) || isset($this->globalPalettes[$field]));
    }

    /**
     * Check if a field is declared locally in this table.
     *
     * @param string $field The name/identifier of the field.
     *
     * @return int Returns 1 if the field is declared locally, 0 if not,
     *             -1 if the field is declared in another table, or -2 if the field is an unknown type.
     */
    public function isValidLocalField(string $field): int
    {
        if (in_array($field, array_keys($this->fields))) {
            $field = $this->fields[$field];
            if ($field instanceof ForeignType) {
                return -1;
            } elseif ($field instanceof UnknownType) {
                return -2;
            }
            return 1;
        }
        return 0;
    }

    /**
     * Convert columns to fields.
     */
    private function _columnsToFields(): void
    {
        foreach ($this->columns as $identifier => $field) {
            if (is_array($field)) {
                if (isset($field['config']) && isset($field['config']['type'])) {
                    $field['identifier'] = $identifier;
                    $config = $field['config'];
                    unset($field['config']);
                    $field = array_merge_recursive($field, $config);
                    $field['type'] = FieldBuilder::convertTypeColumnToField($field['type']);
                    $this->fields[$identifier] = Field::createField($field, $this->table, $this);
                } else {
                    $invalidField = new ForeignType();
                    if (!$invalidField->checkForeignType($field)) {
                        $invalidField = new UnknownType($identifier);
                    }
                    $this->fields[$identifier] = $invalidField;
                }
            } else {
                $this->fields[$identifier] = new UnknownType($identifier);
            }
        }
    }

    /**
     * Extract columns from the table content.
     */
    private function _extractColumns(): void
    {
        if (isset($this->content['GLOBALS']['TCA'][$this->table]['columns'])) {
            $this->columns = $this->content['GLOBALS']['TCA'][$this->table]['columns'];
        }
        if (isset($this->content['return']['columns'])) {
            $this->columns = array_merge_recursive($this->palettes, $this->content['return']['columns']);
        }
    }

    /**
     * Extract global columns for the table.
     */
    private function _extractGlobalColumns(): void
    {
        $this->globalColumns = Collector::collectData($this->table);
    }

    /**
     * Convert palettes to fields.
     */
    private function _palettesToFields(): void
    {
        foreach ($this->palettes as $identifier => $palette) {
            if (is_array($palette)) {
                if (isset($palette['showitem'])) {
                    $paletteElement = new PaletteContainer();
                    $paletteElement->injectRawPalette($identifier, $palette, $this->table, $this);
                    $this->palettes[$identifier] = $paletteElement;
                } else {
                    $invalidField = new ForeignType();
                    if (!$invalidField->checkForeignType($palette)) {
                        $invalidField = new UnknownType($identifier);
                    }
                    $this->palettes[$identifier] = $invalidField;
                }
            } else {
                $this->palettes[$identifier] = new UnknownType($identifier);
            }
        }
    }

    /**
     * Extract palettes from the table content.
     */
    private function _extractPalettes(): void
    {
        if (isset($this->content['GLOBALS']['TCA'][$this->table]['palettes'])) {
            $this->palettes = $this->content['GLOBALS']['TCA'][$this->table]['palettes'];
        }
        if (isset($this->content['return']['palettes'])) {
            $this->palettes = array_merge_recursive($this->palettes, $this->content['return']['palettes']);
        }
    }

    /**
     * Extract global palettes for the table.
     */
    private function _extractGlobalPalettes(): void
    {
        $this->globalPalettes = Collector::collectData($this->table, Collector::COLLECT_PALETTES);
    }

    /**
     * Extract types from the table content.
     */
    private function _extractTypes(): void
    {
        if (isset($this->content['GLOBALS']['TCA'][$this->table]['types'])) {
            $this->types = $this->content['GLOBALS']['TCA'][$this->table]['types'];
        }
        if (isset($this->content['return']['types'])) {
            $this->types = array_merge_recursive($this->types, $this->content['return']['types']);
        }
    }

    /**
     * Get the name of the table.
     *
     * @return string The table name.
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Get the fields defined for the table.
     *
     * @return array The fields.
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Get the palettes defined for the table.
     *
     * @return array The palettes.
     */
    public function getPalettes(): array
    {
        return $this->palettes;
    }

    /**
     * Get the columns defined for the table.
     *
     * @return array The columns.
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Get the array fields defined for the table.
     *
     * @return array The array fields.
     */
    public function getArrayFields(): array
    {
        return $this->arrayFields;
    }

    /**
     * Get all fields, including local and global ones.
     *
     * @return array All fields.
     */
    public function getAllFields(): array
    {
        return $this->allFields;
    }

    /**
     * Load the content of the table from a file.
     */
    private function _loadContent(): void
    {
        $path = $this->isOverride ? CbBuilderConfig::getOverridesPath() : CbBuilderConfig::getTCAPath();
        $filesystem = new Filesystem();
        if (!$filesystem->exists($path)) {
            $filesystem->mkdir($path);
        }
        if (!$filesystem->exists($path . "/$this->table.php")) {
            file_put_contents($path . "/$this->table.php", "<?php\n\n");
        }
        $this->path = $path . "/$this->table.php";
        $content = file_get_contents($path . "/$this->table.php");
        $this->stringContent = $content !== '' ? Wrapper::extract($content) : '';
        $this->content = ArrayParser::extractArraysFromString($this->stringContent, $GLOBALS['CbBuilder']['config']['useEval'], '', true);
    }

    /**
     * Load default control settings for the table.
     */
    public function loadDefaultCtrl(): void {}

    /**
     * Set default control settings for the table.
     */
    public function setDefaultCtrl(): void {}

    /**
     * Load metadata for the table.
     */
    public function loadMeta(): void {}

    /**
     * Set metadata for the table.
     *
     * @return string The metadata as a string.
     */
    public function setMeta(): string { return ''; }

    /**
     * Set the type of the table based on the provided table type.
     *
     * @param int $tableType The type of the table.
     */
    private function _setType(int $tableType): void
    {
        $identifier = $GLOBALS['CbBuilder']['identifier'];
        if (isset($this->types[$identifier])) {
            $this->type = new Type($this->types[$identifier], $identifier, $this->table, $this->arrayFields, $tableType);
        } else {
            $this->type = new Type([], $identifier, $this->table, $this->arrayFields, $tableType);
        }    
    }

    /**
     * Extract all necessary data for the table.
     */
    private function _extractAll(): void
    {
        if ($this instanceof TtContentTable) {
            $this->loadMeta();
        }
        if ($this instanceof CollectionTable) {
            $this->loadDefaultCtrl();
        }
        $this->_extractTypes();
        $this->_extractColumns();
        $this->_extractGlobalColumns();
        $this->_columnsToFields();
        $this->_extractPalettes();
        $this->_extractGlobalPalettes();
        $this->_palettesToFields();
    }

    /**
     * Add fields to the table.
     *
     * @param array $fields The fields to add.
     */
    public function addFields(array $fields): void
    {
        $this->arrayFields = $fields;
        if (isset($this->arrayFields['identifier'])) {
            $fields = [$this->arrayFields['identifier'] => $this->arrayFields];
            $this->arrayFields = $fields;
        } else {
            try {
                $this->arrayFields = array_combine(array_column($this->arrayFields, 'identifier'), array_map(fn($entry) => $entry, $this->arrayFields));
            } catch (\Throwable $th) {
                $CType = $GLOBALS['CbBuilder']['identifier'] ?? 'Unknown CType';
                $fieldType = 'Unknown type';
                foreach ($fields as $field) {
                    if (!isset($field['identifier'])) {
                        $fieldType = $field['type'] ?? 'Unknown type';
                    }
                }
                throw new ParseException (
                    "Not all of your fields have identifiers. Thrown in: CType: '$CType' and table: '$this->identifier'.\n" .
                    "Field is of type: $fieldType"
                );
            }
        }
        $this->allFields = Collector::collectAllAvailableFields($this);
        foreach ($fields as $field) {
            if (array_key_exists('type', $field) && array_key_exists('identifier', $field)) {
                $identifier = $field['identifier'];
                if (!$identifier) {
                    $CType = $GLOBALS['CbBuilder']['identifier'] ?? 'Unknown CType';
                    $fieldType = $field['type'] ?? 'Unknown type';
                    throw new ParseException (
                        "Not all of your fields have identifiers. Thrown in: CType: '$CType' and table: '$this->identifier'.\n" .
                        "Field is of type: $fieldType"
                    );
                }
                $isLocaleField = $this->isValidLocalField($field['identifier']);
                if ($isLocaleField === -1) {
                    if (!FieldBuilder::isSurpressedWarning(580546127)) {
                        $foreignTableField = $this->fields[$identifier];
                        $foreignTable = $foreignTableField->getForeignTable();
                        throw new Exception (
                            "WARNING: Field '$identifier' seems to be declared in the foreign table '$foreignTable' " .
                            "and table '$this->table' is extending the field. Please override it with the corresponding Collection." .
                            "You can surpress this warning in the cbConfig.yaml by adding the code 580546127 to surpressWarning."
                        );
                    }
                } elseif ($isLocaleField === -2) {
                    if (!FieldBuilder::isSurpressedWarning(410163683)) {
                        throw new Exception (
                            "WARNING: Could not parse type and data for field '$identifier'. " .
                            "This element would be ignored, please check if you use a supported syntax." .
                            "You can surpress this warning in the cbConfig.yaml by adding the code 410163683 to surpressWarning."
                        );
                    }
                } elseif ($isLocaleField !== 1 && $isLocaleField !== 0) {
                    throw new Exception (
                        "Unknown response from 'isValidLocalField' function."
                    );
                }

                switch ($field['type']) {
                    case 'Collection':
                        $table = (isset($field['foreign_table']) && $field['foreign_table'] !== '') ? $field['foreign_table'] : $identifier;
                        if (isset($this->arrayFields[$identifier])) {
                            if (!isset($this->arrayFields[$identifier]['foreign_table'])) {
                                $this->arrayFields[$identifier]['foreign_table'] = $table;
                            }
                            if (!isset($this->arrayFields[$identifier]['foreign_field'])) {
                                $this->arrayFields[$identifier]['foreign_field'] = $table;
                            }
                        }
                        $collection = new CollectionTable($identifier, $table, false, $this);
                        $collection->createCollectionContainer($field, $this->table);
                        if (isset($field['fields'])) {
                            $collection->addFields($field['fields']);
                        }
                        if ($isLocaleField === 1 && isset($this->fields[$identifier]) && $this->fields[$identifier] instanceof CollectionContainer) {
                            $this->fields[$identifier]->mergeField($collection->getCollectionContainer());
                            $collection->setCollectionContainer($this->fields[$identifier]);
                        } elseif ($isLocaleField === 0 || $isLocaleField === -1) {
                            $this->fields[$identifier] = $collection->getCollectionContainer();
                        }
                        $this->fields[$identifier] = $collection;
                        $this->sortedElements[$identifier] = $collection;
                        break;
                    case 'Palette':
                        $palette = Field::createField($field, $this->table, $this);
                        $identifier = $palette->getIdentifier();
                        if (isset($this->palettes[$identifier])) {
                            $cPalette = $this->palettes[$identifier];
                            if ($cPalette instanceof PaletteContainer) {
                                $cPalette->mergePalettes($palette);
                                $this->palettes[$identifier] = $cPalette;
                                $this->sortedElements[$identifier] = $cPalette;
                            } else {
                                throw new Exception (
                                    "'Table.php' property '\$this->palettes' palette '$identifier' is not an " .
                                    "object of type PaletteContainer."
                                );
                            }
                        } else {
                            $this->palettes[$identifier] = $palette;
                            $this->sortedElements[$identifier] = $palette;
                        }
                        break;
                    default:
                        $fieldObj = Field::createField($field, $this->table, $this);
                        $type = 'DS\CbBuilder\FieldBuilder\Fields\\' . $field['type'] . 'Field';
                        if ($isLocaleField === 1 && isset($this->fields[$identifier]) && is_a($this->fields[$identifier], $type)) {
                            $this->fields[$identifier]->mergeField($fieldObj);
                            $this->sortedElements[$identifier] = $fieldObj;
                        } elseif ($isLocaleField === 0 || $isLocaleField === -1) {
                            $this->fields[$identifier] = $fieldObj;
                            $this->sortedElements[$identifier] = $fieldObj;
                        }
                        break;
                }
            } else {
                $tableType = '';
                if ($this instanceof TtContentTable) {
                    $tableType = 'tt_content (first dimension of fields)';
                } else if ($this instanceof CollectionTable) {
                    $tableType = 'collection';
                }
                if (!isset($field['identifier'])) {
                    if (isset($field['type']) && is_string($field['type'])) {
                        $type = $field['type'];
                        throw new Exception (
                            "Table of type '$tableType' and identifier '$this->table': Field of type '$type' " .
                            "has no identifier set. Every field must have an identifier."
                        );
                    } else {
                        throw new Exception (
                            "Table of type '$tableType' and identifier '$this->table': Field of unknown type " .
                            "has no identifier and type set. Every field must have an identifier and type."
                        );
                    }
                } else {
                    if (isset($field['identifier']) && is_string($field['identifier'])) {
                        $identifier = $field['identifier'];
                        throw new Exception (
                            "Table of type '$tableType' and identifier '$this->table': Field with identifier " .
                            "'$identifier' has no type set. Every field must have a type."
                        );
                    } else {
                        throw new Exception (
                            "Table of type '$tableType' and identifier '$this->table': Field of unknown type " .
                            "has no identifier and type set. Every field must have an identifier and type."
                        );
                    }
                }
            }
        }
    }

    /**
     * Parse the control settings into a string.
     *
     * @return string The control settings as a string.
     */
    public function parseCtrl(): string { return ''; }

    /**
     * Parse the columns for the table based on the table type.
     *
     * @param int $tableType The type of the table.
     *
     * @return string The parsed columns as a string.
     */
    private function _parseColumns(int $tableType): string
    {
        $this->_setType($tableType);
        $columns = '';
        if ($tableType === self::TT_CONTENT_TABLE) {
            foreach ($this->fields as $key => $field) {
                if ($field instanceof Field && !($field instanceof LinebreakField)) {
                    if ($field->isUseExistingField() === null || $field->isUseExistingField() === false) {
                        $columns .= "\n\$GLOBALS['TCA']['tt_content']['columns']['$key'] = ";
                        $columns .= $field->parseField(Field::PARSE_WITHOUT_KEY_MODE, 0);
                        $columns .= ";\n";
                    } else {
                        $sdq = new SimpleDatabaseQuery();
                        if (!$sdq->fieldExists($field->getIdentifier(), [$this->table])) {
                            $type = $field->getType();
                            $identifier = $field->getIdentifier();
                            $table = $this->table;
                            throw new InvalidArgumentException (
                                "'$type' field '$identifier' configuration 'useExistingField' is set to true but " .
                                "there is no existing field to use in table '$table'."
                            );
                        }
                        $this->type->addColumnToOverride($field);
                    }
                } elseif ($field instanceof CollectionTable) {
                    if (
                        $field->getCollectionContainer()->isUseExistingField() === null
                        || $field->getCollectionContainer()->isUseExistingField() === false
                    ) {
                        $columns .= "\n\$GLOBALS['TCA']['tt_content']['columns']['$key'] = ";
                        $columns .= $field->getCollectionContainer()->parseField(Field::PARSE_WITHOUT_KEY_MODE, 0);
                        $columns .= ";\n";
                    } else {
                        $this->type->addColumnToOverride($field->getCollectionContainer());
                    }
                }
            }
        } elseif ($tableType === self::COLLECTION_TABLE) {
            $columns = "\n\t'columns' => [\n";
            foreach ($this->fields as $key => $field) {
                if ($field instanceof Field && !($field instanceof LinebreakField)) {
                    if ($field->isUseExistingField() === null || $field->isUseExistingField() === false) {
                        $columns .= $field->parseField(Field::PARSE_WITH_KEY_MODE, 2);
                        $columns .= ",\n";
                    } else {
                        $sdq = new SimpleDatabaseQuery();
                        if (!$sdq->fieldExists($field->getIdentifier(), [$this->table])) {
                            $type = $field->getType();
                            $identifier = $field->getIdentifier();
                            $table = $this->table;
                            throw new InvalidArgumentException (
                                "'$type' field '$identifier' configuration 'useExistingField' is set to true but " .
                                "there is no existing field to use in table '$table'."
                            );
                        }
                        $this->type->addColumnToOverride($field);
                    }
                } elseif ($field instanceof CollectionTable) {
                    if (
                        $field->getCollectionContainer()->isUseExistingField() === null
                        || $field->getCollectionContainer()->isUseExistingField() === false
                    ) {
                        $columns .= $field->getCollectionContainer()->parseField(Field::PARSE_WITH_KEY_MODE, 2);
                        $columns .= ",\n";
                    } else {
                        $this->type->addColumnToOverride($field->getCollectionContainer());
                    }
                }
            }
            $columns .= "\t],\n";
        }
        return $columns;
    }

    /**
     * Parse the types for the table based on the table type.
     *
     * @param int $tableType The type of the table.
     *
     * @return string The parsed types as a string.
     */
    private function _parseType(int $tableType): string
    {
        $types = '';
        if ($tableType === self::TT_CONTENT_TABLE) {
            foreach ($this->types as $key => $value) {
                $types = "\n\$GLOBALS['TCA']['tt_content']['types']['$key'] = ";
                if ($key !== $GLOBALS['CbBuilder']['identifier']) {
                    $types .= ArrayParser::arrayToString($value, '', 1, false) . ";\n";
                }
            }
            if ($this->type !== null) {
                $value = $this->type->typeToArray();
                $identifier = $GLOBALS['CbBuilder']['identifier'];
                $types = "\n\$GLOBALS['TCA']['tt_content']['types']['$identifier'] = ";
                $types .= ArrayParser::arrayToString($value, '', 1, false) . ";\n";
            }
        } elseif ($tableType === self::COLLECTION_TABLE) {
            $types .= "\t'types' => [\n";
            foreach ($this->types as $key => $value) {
                if ($key !== $GLOBALS['CbBuilder']['identifier'] && $key !== '0' && $key !== 0) {
                    $types .= "\t\t'" . $key . "' => " . ArrayParser::arrayToString($value, '', 3, false) . ",\n";
                }
            }
            if ($this->type !== null) {
                $value = $this->type->typeToArray();
                if (isset($this->types['0']) || isset($this->types[0])) {
                    $value = array_replace_recursive($this->types['0'], $value);
                }
                $types .= "\t\t'0' => " . ArrayParser::arrayToString($value, '', 3, false) . ",\n";
            }
            $types .= "\t],";
        }
        return $types;
    }

    /**
     * Parse the palettes for the table based on the table type.
     *
     * @param int $tableType The type of the table.
     *
     * @return string The parsed palettes as a string.
     */
    private function _parsePalettes(int $tableType): string
    {
        $palettes = '';
        if ($tableType === self::TT_CONTENT_TABLE) {
            foreach ($this->palettes as $key => $palette) {
                $palettes .= "\n\$GLOBALS['TCA']['tt_content']['palettes']['$key'] = ";
                $palettes .= $palette->parsePalette(PaletteContainer::PARSE_WITHOUT_KEY_MODE, 0);
                $palettes .= ";\n";
            }
        } elseif ($tableType === self::COLLECTION_TABLE) {
            $palettes .= "\n\t'palettes' => [\n";
            foreach ($this->palettes as $key => $palette) {
                $palettes .= $palette->parsePalette(PaletteContainer::PARSE_WITH_KEY_MODE, 2);
                $palettes .= ",\n";
            }
            $palettes .= "\t],\n";
        }
        return $palettes;
    }

    /**
     * Parse classes for the table.
     *
     * @return array|null An array of classes if not a tt_content table, null otherwise.
     */
    public function parseClasses(): ?array
    {
        $cbIdentifier = $GLOBALS['CbBuilder']['identifier'];
        $file = __DIR__ . "/../../../Configuration/classesMap.yaml";
        $filesystem = new Filesystem();
        $map = [];
        $mainClasses = [];
        if ($this instanceof TtContentTable) {
            if (!$filesystem->exists($file)) {
                FileCreater::makeClassesMapYaml($cbIdentifier);
            }
            $map = Yaml::parse(file_get_contents($file));

            $meta = Collector::collectFieldsYamlMeta($cbIdentifier);

            if (is_array($meta) && $meta !== []) {
                if (isset($meta['classes'])) {
                    $classes = $meta['classes'];
                    if (is_string($classes) && $classes !== '') {
                        $classes = GeneralUtility::trimExplode(',', $classes);
                    }
                    
                    if (is_array($classes) && $classes !== []) {
                        $mainClasses = $classes;
                    }
                }
            }

            if (!isset($map[$cbIdentifier])) {
                $map[$cbIdentifier] = [];
            }
        }
        $subMap = [];
        foreach ($this->fields as $identifier => $field) {
            if ($field instanceof CollectionTable) {
                $classes = $field->parseClasses();
                if ($classes !== []) {
                    $subMap[$identifier] = $classes;
                }
            }
            if (!$field instanceof TtContentTable) {
                if ($field->getLabel() !== '') $identifier = $field->getLabel();
                $fieldClasses = $field->getClasses();
                if ($fieldClasses !== []) { 
                    if (is_array($fieldClasses) && $fieldClasses !== [] && isset($fieldClasses[0]) && $fieldClasses[0] !== '') {
                        $subMap[$identifier] = $fieldClasses;
                    }
                }
            }
        }
        if ($this instanceof TtContentTable) {
            unset($map[$cbIdentifier]);
            $map[$cbIdentifier] = $subMap;
            if ($mainClasses !== []) {
                $map[$cbIdentifier]['main'] = $mainClasses;
            }
            $map = Yaml::dump($map, PHP_INT_MAX, 2);
            
            file_put_contents($file, $map);
        } else {
            return $subMap;
        }
        return null;
    }


    /**
     * Convert the Table class object into an array.
     * 
     * @return array The parsed array.
     */
    public function tableToArray(): array
    {
        $tableArray = [];
        foreach ($this->fields as $identifier => $field) {
            if ($field instanceof Field) {
                $tableArray[$identifier] = $field->fieldToYamlArray($field->getConfig());
            } elseif ($field instanceof CollectionTable) {
                $container = $field->getCollectionContainer();
                $tableArray[$identifier] = $container->fieldToYamlArray($container->getConfig());
                $tableArray[$identifier]['fields'] = $field->tableToArray();
            }
        }
        return $tableArray;
    }

    /**
     * Sort the output array that will be written to fields.yaml, preserving the original order of 
     * fields.yaml. Fields declared only in files will be appended to the end of the corresponding table.
     * 
     * @param array $parsedTableArray The array representation of this Table class.
     * 
     * @return array The sorted $parsedTableArray.
     */
    public function sortFields(array $parsedTableArray): array
    {
        $sortedFields = $this->sortedElements;
        $sortedParsedTableArray = [];
        foreach ($sortedFields as $key => $value) {
            if (array_key_exists($key, $parsedTableArray)) {
                $sortedParsedTableArray[$key] = $parsedTableArray[$key];
                unset($parsedTableArray[$key]);
            } else {
                if ($value instanceof PaletteContainer) {
                    $sortedParsedTableArray[$key] = $value->paletteToArray();
                }
            }
        }
        foreach ($parsedTableArray as $key => $value) {
            $sortedParsedTableArray[$key] = $value;
        }
        return $sortedParsedTableArray;
    }

    public function collectFilesMap(): array
    {
        $files = [];
        foreach ($this->fields as $value) {
            if ($value instanceof CollectionTable) {
                $files[$value->getTable()] = $value->collectFilesMap();
            } elseif ($value instanceof FileField) {
                $files[] = $value->getIdentifier();
            }
        }
        return $files;
    }

    private function _setFilesMap(): void
    {
        if ($this instanceof TtContentTable) {
            FileCreater::addFileFieldsToMap($this->collectFilesMap());
        }
    }

    /**
     * Parse and generate the table content.
     */
    public function parseTable(): array
    {
        $tableStr = '';
        $tableType = 0;
        $parsedTableArray = [];
        if (CbBuilderConfig::isCrossParsing()) {
            $parsedTableArray = $this->sortFields($this->tableToArray());
        }
        $this->_setFilesMap();
        $this->setDefaultCtrl();
        if ($this instanceof TtContentTable) {
            $meta = $this->setMeta();
            $tableStr = $meta;
            $tableType = self::TT_CONTENT_TABLE;
            $this->parseClasses();
        } elseif ($this instanceof CollectionTable) {
            $tableStr .= "return [\n";
            $tableStr .= $this->parseCtrl();
            $tableType = self::COLLECTION_TABLE;
        }
        foreach ($this->fields as $field) {
            if ($field instanceof CollectionTable) {
                if (CbBuilderConfig::isCrossParsing() && isset($parsedTableArray[$field->getTable()]['fields'])) {
                    $parsedTableArray[$field->getTable()]['fields'] = $field->parseTable();
                } else {
                    $field->parseTable();
                }
                
            }
        }

        $tableStr .= $this->_parseColumns($tableType);
        $tableStr .= $this->_parseType($tableType);
        $tableStr .= $this->_parsePalettes($tableType);
        $GLOBALS['CbBuilder']['SqlCreater']->createAndAddTable($this->table);
        if ($GLOBALS['CbBuilder']['SqlCreater']->enrichSingleTableFieldsFromTcaColumns($this->table, $this) === true) {
            $sqlQueries = $GLOBALS['CbBuilder']['SqlCreater']->exportSqlQueries();
            $GLOBALS['CbBuilder']['SqlCreater']->writeQueries($sqlQueries);
        }
        
        if ($this instanceof CollectionTable) {
            $tableStr .= "];\n";
        }
        
        Wrapper::inject($this->path, $tableStr, true);

        return $parsedTableArray;
    }

    /**
     * Constructor for the Table class.
     *
     * @param string $identifier The identifier of the table.
     * @param string $table The name of the table.
     * @param bool $isOverride Whether this table overrides another.
     * @param Table|null $parentTable The parent table if applicable.
     */
    public function __construct(string $identifier, string $table, bool $isOverride, ?Table &$parentTable = null)
    {
        $this->identifier = $identifier;
        $this->table = $table;
        $this->isOverride = $isOverride;
        $this->parentTable = $parentTable;
        $this->_loadContent();
        $this->_extractAll();
    }
}