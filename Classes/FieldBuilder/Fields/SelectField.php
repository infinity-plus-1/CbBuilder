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
 * Configuration class for select field file folder settings.
 */
final class SelectFieldFileFolderConfig extends Config
{
    /**
     * Allowed file extensions for the file folder.
     */
    protected string $allowedExtensions = '';

    /**
     * Depth of the file folder.
     */
    protected int $depth = -1;

    /**
     * Path to the file folder.
     */
    protected string $folder = '';

    /**
     * Get the allowed file extensions.
     *
     * @return string The allowed file extensions.
     */
    public function getAllowedExtensions(): string
    {
        return $this->allowedExtensions;
    }

    /**
     * Get the depth of the file folder.
     *
     * @return int The depth of the file folder.
     */
    public function getDepth(): int
    {
        return $this->depth;
    }

    /**
     * Get the path to the file folder.
     *
     * @return string The path to the file folder.
     */
    public function getFolder(): string
    {
        return $this->folder;
    }

    /**
     * Set the allowed file extensions.
     *
     * @param string $allowedExtensions The allowed file extensions.
     */
    public function setAllowedExtensions(string $allowedExtensions): void
    {
        $this->allowedExtensions = $allowedExtensions;
    }

    /**
     * Set the depth of the file folder.
     *
     * @param int $depth The depth of the file folder.
     */
    public function setDepth(int $depth): void
    {
        $this->depth = $depth;
    }

    /**
     * Set the path to the file folder.
     *
     * @param string $folder The path to the file folder.
     */
    public function setFolder(string $folder): void
    {
        $this->folder = $folder;
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
        if ($foreign->getAllowedExtensions() !== '') {
            $this->allowedExtensions = $foreign->getAllowedExtensions();
        }

        if ($foreign->getDepth() >= 0) {
            $this->depth = $foreign->getDepth();
        }

        if ($foreign->getFolder() !== '') {
            $this->folder = $foreign->getFolder();
        }
    }

    /**
     * Convert an array configuration to the current config.
     *
     * @param array $config The configuration array.
     * @param array $globalConf The global configuration.
     */
    public function arrayToConfig(array $config, array $fieldProperties, ?array $globalConf = NULL, mixed $misc = NULL): void
    {
        $properties = get_object_vars($this);
        foreach ($config as $configKey => $value) {
            if ($this->isValidConfig($properties, $configKey)) {
                switch ($configKey) {
                    case 'depth':
                        $this->validateInteger($value, $globalConf, "fileFolderConfig['depth']", 'Select', 0, 99);
                        $this->depth = intval($value);
                        break;
                    default:
                        $this->$configKey = $value;
                        break;
                }
            }
        }
    }
}

/**
 * Class representing a select field item.
 */
final class SelectFieldItem extends Config
{
    /**
     * Label for the select field item.
     */
    protected string $label = '';

    /**
     * Value of the select field item, which can be either a string or an integer.
     */
    protected string|int $value = '';

    /**
     * Icon associated with the select field item.
     */
    protected string $icon = '';

    /**
     * Grouping category for the select field item.
     */
    protected string $group = '';

    /**
     * Description for the select field item, which can be either a string or an array.
     */
    protected string|array $description = '';

    /**
     * Get the label of the select field item.
     *
     * @return string The label.
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Get the value of the select field item.
     *
     * @return string|int The value.
     */
    public function getValue(): string|int
    {
        return $this->value;
    }

    /**
     * Get the icon associated with the select field item.
     *
     * @return string The icon.
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * Get the grouping category for the select field item.
     *
     * @return string The group.
     */
    public function getGroup(): string
    {
        return $this->group;
    }

    /**
     * Get the description for the select field item.
     *
     * @return string|array The description.
     */
    public function getDescription(): string|array
    {
        return $this->description;
    }

    /**
     * Set the label for the select field item.
     *
     * @param string $label The label.
     */
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    /**
     * Set the value for the select field item.
     *
     * @param string|int $value The value.
     */
    public function setValue(string|int $value): void
    {
        $this->value = $value;
    }

    /**
     * Set the icon for the select field item.
     *
     * @param string $icon The icon.
     */
    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    /**
     * Set the grouping category for the select field item.
     *
     * @param string $group The group.
     */
    public function setGroup(string $group): void
    {
        $this->group = $group;
    }

    /**
     * Set the description for the select field item.
     *
     * @param string|array $description The description.
     */
    public function setDescription(string|array $description): void
    {
        $this->description = $description;
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

        if ($foreign->getLabel() !== '') {
            $this->label = $foreign->getLabel();
        }

        if (
            (is_string($foreign->getValue()) && $foreign->getValue() !== '')
            || (is_int($foreign->getValue()) && $foreign->getValue() >= 0)
        ) {
            $this->value = $foreign->getValue();
        }

        if ($foreign->getIcon() !== '') {
            $this->icon = $foreign->getIcon();
        }

        if ($foreign->getGroup() !== '') {
            $this->group = $foreign->getGroup();
        }

        // Corrected condition for merging description
        if (
            (is_string($foreign->getDescription()) && $foreign->getDescription() !== '')
            || (is_array($foreign->getDescription()) && !empty($foreign->getDescription()))
        ) {
            $this->description = $foreign->getDescription();
        }
    }

    /**
     * Constructor for the select field item.
     *
     * @param array $item The configuration array for the item.
     */
    public function __construct(array $item, int|string $index, string $identifier)
    {
        $properties = get_object_vars($this);
        $i = 0;
        foreach ($item as $configKey => $config) {
            if (array_key_exists($configKey, $properties)) {
                $this->$configKey = $config;
            } else {
                throw new InvalidArgumentException (
                    "'Select' field '$identifier' configuration 'items[$index][$i]' setting '$configKey' is not valid.\n" .
                    "Valid settings are: " . implode(', ', array_keys($properties))
                );
            }
            $i++;
        }
    }
}

/**
 * Class representing the appearance configuration for a select field.
 */
final class SelectFieldAppearance extends Config
{
    /**
     * Whether to show the header.
     */
    protected ?bool $showHeader = null;

    /**
     * Whether to expand all levels by default.
     */
    protected ?bool $expandAll = null;

    /**
     * Maximum number of levels to display.
     */
    protected int $maxLevels = -1;

    /**
     * Levels that are not selectable, specified as a comma-separated string of integers.
     */
    protected string $nonSelectableLevels = '';

    /**
     * Get whether to show the header.
     *
     * @return bool|null Whether to show the header.
     */
    public function getShowHeader(): ?bool
    {
        return $this->showHeader;
    }

    /**
     * Get whether to expand all levels by default.
     *
     * @return bool|null Whether to expand all levels.
     */
    public function getExpandAll(): ?bool
    {
        return $this->expandAll;
    }

    /**
     * Get the maximum number of levels to display.
     *
     * @return int The maximum number of levels.
     */
    public function getMaxLevels(): int
    {
        return $this->maxLevels;
    }

    /**
     * Get the levels that are not selectable.
     *
     * @return string The non-selectable levels as a comma-separated string.
     */
    public function getNonSelectableLevels(): string
    {
        return $this->nonSelectableLevels;
    }

    /**
     * Set whether to show the header.
     *
     * @param bool|null $showHeader Whether to show the header.
     */
    public function setShowHeader(?bool $showHeader): void
    {
        $this->showHeader = $showHeader;
    }

    /**
     * Set whether to expand all levels by default.
     *
     * @param bool|null $expandAll Whether to expand all levels.
     */
    public function setExpandAll(?bool $expandAll): void
    {
        $this->expandAll = $expandAll;
    }

    /**
     * Set the maximum number of levels to display.
     *
     * @param int $maxLevels The maximum number of levels.
     */
    public function setMaxLevels(int $maxLevels): void
    {
        $this->maxLevels = $maxLevels;
    }

    /**
     * Set the levels that are not selectable.
     *
     * @param string $nonSelectableLevels The non-selectable levels as a comma-separated string.
     */
    public function setNonSelectableLevels(string $nonSelectableLevels): void
    {
        $this->nonSelectableLevels = $nonSelectableLevels;
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
        if ($foreign->getShowHeader() !== null) {
            $this->showHeader = $foreign->getShowHeader();
        }

        if ($foreign->getExpandAll() !== null) {
            $this->expandAll = $foreign->getExpandAll();
        }

        if ($foreign->getMaxLevels() !== -1) {
            $this->maxLevels = $foreign->getMaxLevels();
        }

        if ($foreign->getNonSelectableLevels() !== '') {
            $this->nonSelectableLevels = $foreign->getNonSelectableLevels();
        }
    }

    /**
     * Validate the non-selectable levels configuration.
     *
     * @param mixed $value The value to validate.
     * @param array $config The configuration array.
     *
     * @throws Exception If the validation fails.
     */
    private function _validateNonSelectableLevels(mixed $value, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_string($value)) {
            throw new Exception (
                "'Select' field '$identifier' configuration 'treeConfig['appearance']['nonSelectableLevels']' must be of type string."
            );
        }
        $splitted = GeneralUtility::trimExplode(',', $value);
        $i = 0;
        foreach ($splitted as $num) {
            if ($this->handleIntegers($num) === null) {
                throw new Exception (
                    "'Select' field '$identifier' configuration 'treeConfig['appearance']['nonSelectableLevels'][$i]' must be of type " .
                    "integer or a string representing an integer."
                );
            }
            $i++;
        }
    }

    /**
     * Constructor for the select field appearance configuration.
     *
     * @param array $config The configuration array for the appearance.
     * @param array $globalConfig The global configuration.
     */
    public function __construct(array $config, array $globalConfig)
    {
        $properties = get_object_vars($this);
        foreach ($config as $configKey => $setting) {
            if (array_key_exists($configKey, $properties)) {
                switch ($configKey) {
                    case 'maxLevels':
                        $this->validateInteger($setting, $globalConfig, "treeConfig['appearance']['maxLevels']", 'Select', 1, PHP_INT_MAX);
                        $this->maxLevels = $setting;
                        break;
                    case 'nonSelectableLevels':
                        $this->_validateNonSelectableLevels($setting, $globalConfig);
                        $this->nonSelectableLevels = $setting;
                        break;
                    default:
                        $this->$configKey = $setting;
                        break;
                }
                
            } else {
                $identifier = $globalConfig['identifier'];
                throw new Exception (
                    "'Select' field '$identifier' configuration 'treeConfig' setting '$configKey' is not valid.\n" .
                    "Valid settings are: " . implode(', ', array_keys($properties))
                );
            }
        }
    }
}

/**
 * Class representing the tree configuration for a select field.
 */
final class SelectFieldTreeConfig extends Config
{
    /**
     * Appearance configuration for the tree.
     */
    protected ?SelectFieldAppearance $appearance = null;

    /**
     * Field name for the children in the tree structure.
     */
    protected string $childrenField = '';

    /**
     * Data provider for the tree structure.
     */
    protected string $dataProvider = '';

    /**
     * Field name for the parent in the tree structure.
     */
    protected string $parentField = '';

    /**
     * Starting points for the tree structure.
     */
    protected string $startingPoints = '';

    /**
     * Get the appearance configuration for the tree.
     *
     * @return SelectFieldAppearance|null The appearance configuration.
     */
    public function getAppearance(): ?SelectFieldAppearance
    {
        return $this->appearance;
    }

    /**
     * Get the field name for the children in the tree structure.
     *
     * @return string The children field name.
     */
    public function getChildrenField(): string
    {
        return $this->childrenField;
    }

    /**
     * Get the data provider for the tree structure.
     *
     * @return string The data provider.
     */
    public function getDataProvider(): string
    {
        return $this->dataProvider;
    }

    /**
     * Get the field name for the parent in the tree structure.
     *
     * @return string The parent field name.
     */
    public function getParentField(): string
    {
        return $this->parentField;
    }

    /**
     * Get the starting points for the tree structure.
     *
     * @return string The starting points.
     */
    public function getStartingPoints(): string
    {
        return $this->startingPoints;
    }

    /**
     * Set the appearance configuration for the tree.
     *
     * @param SelectFieldAppearance|null $appearance The appearance configuration.
     */
    public function setAppearance(?SelectFieldAppearance $appearance): void
    {
        $this->appearance = $appearance;
    }

    /**
     * Set the field name for the children in the tree structure.
     *
     * @param string $childrenField The children field name.
     */
    public function setChildrenField(string $childrenField): void
    {
        $this->childrenField = $childrenField;
    }

    /**
     * Set the data provider for the tree structure.
     *
     * @param string $dataProvider The data provider.
     */
    public function setDataProvider(string $dataProvider): void
    {
        $this->dataProvider = $dataProvider;
    }

    /**
     * Set the field name for the parent in the tree structure.
     *
     * @param string $parentField The parent field name.
     */
    public function setParentField(string $parentField): void
    {
        $this->parentField = $parentField;
    }

    /**
     * Set the starting points for the tree structure.
     *
     * @param string $startingPoints The starting points.
     */
    public function setStartingPoints(string $startingPoints): void
    {
        $this->startingPoints = $startingPoints;
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
        if ($foreign->getAppearance() !== null) {
            if ($this->appearance !== null) {
                $this->appearance->mergeConfig($foreign->getAppearance());
            } else {
                $this->appearance = $foreign->getAppearance();
            }
        }

        if ($foreign->getChildrenField() !== '') {
            $this->childrenField = $foreign->getChildrenField();
        }

        if ($foreign->getDataProvider() !== '') {
            $this->dataProvider = $foreign->getDataProvider();
        }

        if ($foreign->getParentField() !== '') {
            $this->parentField = $foreign->getParentField();
        }

        if ($foreign->getStartingPoints() !== '') {
            $this->startingPoints = $foreign->getStartingPoints();
        }
    }

    /**
     * Validate the appearance configuration.
     *
     * @param mixed $value The value to validate.
     * @param array $config The configuration array.
     *
     * @throws Exception If the validation fails.
     */
    private function _validateAppearance(mixed $value, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_array($value)) {
            throw new Exception (
                "'Select' field '$identifier' configuration 'treeConfig['appearance']' must be of type array.\n" .
                "Fix:\ntreeConfig:\n  appearance:\n    showHeader: true\n    maxLevels: 2"
            );
        }
    }

    /**
     * Validate a field configuration.
     *
     * @param mixed $field The field to validate.
     * @param array $config The configuration array.
     * @param string $setting The setting being validated.
     *
     * @throws Exception If the validation fails.
     */
    private function _validateField(mixed $field, array $config, string $setting): void
    {
        $identifier = $config['identifier'];
        if (!is_string($field)) {
            throw new Exception (
                "'Select' field '$identifier' configuration '$setting' must be of type string."
            );
        }

        $foreignTable = $config['foreign_table'] ?? '';
        if (!is_string($foreignTable)) {
            throw new Exception (
                "'Select' field '$identifier' configuration 'foreign_table' must be of type string."
            );
        }
        if ($foreignTable === '') {
            throw new Exception (
                "'Select' field '$identifier' configuration '$setting' needs 'foreign_table' to be set."
            );
        }
        $this->validateTable($foreignTable, $config, 'foreign_table', 'Select');
        $this->validateField($field, $config, "treeConfig['$setting']", 'Select', [$foreignTable]);
    }

    /**
     * Constructor for the select field tree configuration.
     *
     * @param array $config The configuration array for the tree.
     * @param array $globalConfig The global configuration.
     */
    public function __construct(array $config, array $globalConfig)
    {
        $properties = get_object_vars($this);
        foreach ($config as $configKey => $setting) {
            if (array_key_exists($configKey, $properties)) {
                switch ($configKey) {
                    case 'appearance':
                        $this->_validateAppearance($setting, $globalConfig);
                        $this->appearance = new SelectFieldAppearance($setting, $globalConfig);
                        break;
                    case 'childrenField':
                        $this->_validateField($setting, $globalConfig, 'childrenField');
                        $this->childrenField = $setting;
                        break;
                    case 'parentField':
                        $this->_validateField($setting, $globalConfig, 'parentField');
                        $this->parentField = $setting;
                        break;
                    default:
                        $this->$configKey = $setting;
                        break;
                }
                
            } else {
                $identifier = $globalConfig['identifier'];
                throw new Exception (
                    "'Select' field '$identifier' configuration 'treeConfig' setting '$configKey' is not valid.\n" .
                    "Valid settings are: " . implode(', ', array_keys($properties))
                );
            }
        }
    }
}

/**
 * Class representing the configuration for a select field.
 */
final class SelectFieldConfig extends Config
{
    /**
     * Whether to allow non-ID values.
     */
    protected ?bool $allowNonIdValues = null;

    /**
     * Authorization mode for the field.
     */
    protected string $authMode = '';

    /**
     * Maximum size for auto-sizing.
     */
    protected int $autoSizeMax = -1;

    /**
     * Length of the database field.
     */
    protected int $dbFieldLength = -1;

    /**
     * Default value for the field.
     */
    protected string $default = '';

    /**
     * Whether to disable the "no matching value" element.
     */
    protected ?bool $disableNoMatchingValueElement = null;

    /**
     * Tables not to remap on copy.
     */
    protected string $dontRemapTablesOnCopy = '';

    /**
     * Exclusive keys for the field.
     */
    protected string $exclusiveKeys = '';

    /**
     * Configuration for file folder selection.
     */
    protected ?SelectFieldFileFolderConfig $fileFolderConfig = null;

    /**
     * Foreign table name.
     */
    protected string $foreign_table = '';

    /**
     * Item group for the foreign table.
     */
    protected string $foreign_table_item_group = '';

    /**
     * Prefix for the foreign table.
     */
    protected string $foreign_table_prefix = '';

    /**
     * WHERE clause for the foreign table.
     */
    protected string $foreign_table_where = '';

    /**
     * Identifier for the field.
     */
    protected string $identifier = '';

    /**
     * Item groups for the field.
     */
    protected array $itemGroups = [];

    /**
     * Items for the field.
     */
    protected array $items = [];

    /**
     * User-defined function for processing items.
     */
    protected string $itemsProcFunc = '';

    /**
     * Whether to localize references at parent localization.
     */
    protected ?bool $localizeReferencesAtParentLocalization = null;

    /**
     * Maximum number of items allowed.
     */
    protected int $maxitems = -1;

    /**
     * Minimum number of items required.
     */
    protected int $minitems = -1;

    /**
     * MM (many-to-many) table name.
     */
    protected string $MM = '';

    /**
     * Match fields for MM relations.
     */
    protected array $MM_match_fields = [];

    /**
     * Opposite field for MM relations.
     */
    protected string $MM_opposite_field = '';

    /**
     * Opposite usage for MM relations.
     */
    protected array $MM_oppositeUsage = [];

    /**
     * WHERE clause for the MM table.
     */
    protected string $MM_table_where = '';

    /**
     * Whether the field allows multiple selections.
     */
    protected ?bool $multiple = null;

    /**
     * Filter items for multi-select.
     */
    protected array $multiSelectFilterItems = [];

    /**
     * Whether the field is read-only.
     */
    protected ?bool $readOnly = null;

    /**
     * Size of the field.
     */
    protected int $size = -1;

    /**
     * Sorting configuration for items.
     */
    protected array $sortItems = [];

    /**
     * Tree configuration for the field.
     */
    protected ?SelectFieldTreeConfig $treeConfig = null;

    /**
     * Get whether to allow non-ID values.
     *
     * @return bool|null Whether non-ID values are allowed.
     */
    public function getAllowNonIdValues(): ?bool
    {
        return $this->allowNonIdValues;
    }

    /**
     * Get the authorization mode for the field.
     *
     * @return string The authorization mode.
     */
    public function getAuthMode(): string
    {
        return $this->authMode;
    }

    /**
     * Get the maximum size for auto-sizing.
     *
     * @return int The maximum size for auto-sizing.
     */
    public function getAutoSizeMax(): int
    {
        return $this->autoSizeMax;
    }

    /**
     * Get the length of the database field.
     *
     * @return int The length of the database field.
     */
    public function getDbFieldLength(): int
    {
        return $this->dbFieldLength;
    }

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
     * Get whether to disable the "no matching value" element.
     *
     * @return bool|null Whether to disable the "no matching value" element.
     */
    public function getDisableNoMatchingValueElement(): ?bool
    {
        return $this->disableNoMatchingValueElement;
    }

    /**
     * Get the tables not to remap on copy.
     *
     * @return string The tables not to remap.
     */
    public function getDontRemapTablesOnCopy(): string
    {
        return $this->dontRemapTablesOnCopy;
    }

    /**
     * Get the exclusive keys for the field.
     *
     * @return string The exclusive keys.
     */
    public function getExclusiveKeys(): string
    {
        return $this->exclusiveKeys;
    }

    /**
     * Get the configuration for file folder selection.
     *
     * @return SelectFieldFileFolderConfig|null The file folder configuration.
     */
    public function getFileFolderConfig(): ?SelectFieldFileFolderConfig
    {
        return $this->fileFolderConfig;
    }

    /**
     * Get the foreign table name.
     *
     * @return string The foreign table name.
     */
    public function getForeignTable(): string
    {
        return $this->foreign_table;
    }

    /**
     * Get the item group for the foreign table.
     *
     * @return string The item group.
     */
    public function getForeignTableItemGroup(): string
    {
        return $this->foreign_table_item_group;
    }

    /**
     * Get the prefix for the foreign table.
     *
     * @return string The prefix.
     */
    public function getForeignTablePrefix(): string
    {
        return $this->foreign_table_prefix;
    }

    /**
     * Get the WHERE clause for the foreign table.
     *
     * @return string The WHERE clause.
     */
    public function getForeignTableWhere(): string
    {
        return $this->foreign_table_where;
    }

    /**
     * Get the identifier for the field.
     *
     * @return string The identifier.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Get the item groups for the field.
     *
     * @return array The item groups.
     */
    public function getItemGroups(): array
    {
        return $this->itemGroups;
    }

    /**
     * Get the items for the field.
     *
     * @return array The items.
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Get the user-defined function for processing items.
     *
     * @return string The user-defined function.
     */
    public function getItemsProcFunc(): string
    {
        return $this->itemsProcFunc;
    }

    /**
     * Get whether to localize references at parent localization.
     *
     * @return bool|null Whether to localize references.
     */
    public function getLocalizeReferencesAtParentLocalization(): ?bool
    {
        return $this->localizeReferencesAtParentLocalization;
    }

    /**
     * Get the maximum number of items allowed.
     *
     * @return int The maximum number of items.
     */
    public function getMaxitems(): int
    {
        return $this->maxitems;
    }

    /**
     * Get the minimum number of items required.
     *
     * @return int The minimum number of items.
     */
    public function getMinitems(): int
    {
        return $this->minitems;
    }

    /**
     * Get the MM (many-to-many) table name.
     *
     * @return string The MM table name.
     */
    public function getMM(): string
    {
        return $this->MM;
    }

    /**
     * Get the match fields for MM relations.
     *
     * @return array The match fields.
     */
    public function getMMMatchFields(): array
    {
        return $this->MM_match_fields;
    }

    /**
     * Get the opposite field for MM relations.
     *
     * @return string The opposite field.
     */
    public function getMMOppositeField(): string
    {
        return $this->MM_opposite_field;
    }

    /**
     * Get the opposite usage for MM relations.
     *
     * @return array The opposite usage.
     */
    public function getMMOppositeUsage(): array
    {
        return $this->MM_oppositeUsage;
    }

    /**
     * Get the WHERE clause for the MM table.
     *
     * @return string The WHERE clause.
     */
    public function getMMTableWhere(): string
    {
        return $this->MM_table_where;
    }

    /**
     * Get whether the field allows multiple selections.
     *
     * @return bool|null Whether multiple selections are allowed.
     */
    public function getMultiple(): ?bool
    {
        return $this->multiple;
    }

    /**
     * Get the filter items for multi-select.
     *
     * @return array The filter items.
     */
    public function getMultiSelectFilterItems(): array
    {
        return $this->multiSelectFilterItems;
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
     * Get the size of the field.
     *
     * @return int The size of the field.
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Get the sorting configuration for items.
     *
     * @return array The sorting configuration.
     */
    public function getSortItems(): array
    {
        return $this->sortItems;
    }

    /**
     * Get the tree configuration for the field.
     *
     * @return SelectFieldTreeConfig|null The tree configuration.
     */
    public function getTreeConfig(): ?SelectFieldTreeConfig
    {
        return $this->treeConfig;
    }

    /**
     * Set whether to allow non-ID values.
     *
     * @param bool|null $allowNonIdValues Whether to allow non-ID values.
     */
    public function setAllowNonIdValues(?bool $allowNonIdValues): void
    {
        $this->allowNonIdValues = $allowNonIdValues;
    }

    /**
     * Set the authorization mode for the field.
     *
     * @param string $authMode The authorization mode.
     */
    public function setAuthMode(string $authMode): void
    {
        $this->authMode = $authMode;
    }

    /**
     * Set the maximum size for auto-sizing.
     *
     * @param int $autoSizeMax The maximum size for auto-sizing.
     */
    public function setAutoSizeMax(int $autoSizeMax): void
    {
        $this->autoSizeMax = $autoSizeMax;
    }

    /**
     * Set the length of the database field.
     *
     * @param int $dbFieldLength The length of the database field.
     */
    public function setDbFieldLength(int $dbFieldLength): void
    {
        $this->dbFieldLength = $dbFieldLength;
    }

    /**
     * Set the default value for the field.
     *
     * @param string $default The default value.
     */
    public function setDefault(string $default): void
    {
        $this->default = $default;
    }

    /**
     * Set whether to disable the "no matching value" element.
     *
     * @param bool|null $disableNoMatchingValueElement Whether to disable the "no matching value" element.
     */
    public function setDisableNoMatchingValueElement(?bool $disableNoMatchingValueElement): void
    {
        $this->disableNoMatchingValueElement = $disableNoMatchingValueElement;
    }

    /**
     * Set the tables not to remap on copy.
     *
     * @param string $dontRemapTablesOnCopy The tables not to remap.
     */
    public function setDontRemapTablesOnCopy(string $dontRemapTablesOnCopy): void
    {
        $this->dontRemapTablesOnCopy = $dontRemapTablesOnCopy;
    }

    /**
     * Set the exclusive keys for the field.
     *
     * @param string $exclusiveKeys The exclusive keys.
     */
    public function setExclusiveKeys(string $exclusiveKeys): void
    {
        $this->exclusiveKeys = $exclusiveKeys;
    }

    /**
     * Set the configuration for file folder selection.
     *
     * @param SelectFieldFileFolderConfig|null $fileFolderConfig The file folder configuration.
     */
    public function setFileFolderConfig(?SelectFieldFileFolderConfig $fileFolderConfig): void
    {
        $this->fileFolderConfig = $fileFolderConfig;
    }

    /**
     * Set the foreign table name.
     *
     * @param string $foreignTable The foreign table name.
     */
    public function setForeignTable(string $foreignTable): void
    {
        $this->foreign_table = $foreignTable;
    }

    /**
     * Set the item group for the foreign table.
     *
     * @param string $foreignTableItemGroup The item group.
     */
    public function setForeignTableItemGroup(string $foreignTableItemGroup): void
    {
        $this->foreign_table_item_group = $foreignTableItemGroup;
    }

    /**
     * Set the prefix for the foreign table.
     *
     * @param string $foreignTablePrefix The prefix.
     */
    public function setForeignTablePrefix(string $foreignTablePrefix): void
    {
        $this->foreign_table_prefix = $foreignTablePrefix;
    }

    /**
     * Set the WHERE clause for the foreign table.
     *
     * @param string $foreignTableWhere The WHERE clause.
     */
    public function setForeignTableWhere(string $foreignTableWhere): void
    {
        $this->foreign_table_where = $foreignTableWhere;
    }

    /**
     * Set the identifier for the field.
     *
     * @param string $identifier The identifier.
     */
    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    /**
     * Set the item groups for the field.
     *
     * @param array $itemGroups The item groups.
     */
    public function setItemGroups(array $itemGroups): void
    {
        $this->itemGroups = $itemGroups;
    }

    /**
     * Set the items for the field.
     *
     * @param array $items The items.
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    /**
     * Set the user-defined function for processing items.
     *
     * @param string $itemsProcFunc The user-defined function.
     */
    public function setItemsProcFunc(string $itemsProcFunc): void
    {
        $this->itemsProcFunc = $itemsProcFunc;
    }

    /**
     * Set whether to localize references at parent localization.
     *
     * @param bool|null $localizeReferencesAtParentLocalization Whether to localize references.
     */
    public function setLocalizeReferencesAtParentLocalization(?bool $localizeReferencesAtParentLocalization): void
    {
        $this->localizeReferencesAtParentLocalization = $localizeReferencesAtParentLocalization;
    }

    /**
     * Set the maximum number of items allowed.
     *
     * @param int $maxitems The maximum number of items.
     */
    public function setMaxitems(int $maxitems): void
    {
        $this->maxitems = $maxitems;
    }

    /**
     * Set the minimum number of items required.
     *
     * @param int $minitems The minimum number of items.
     */
    public function setMinitems(int $minitems): void
    {
        $this->minitems = $minitems;
    }

    /**
     * Set the MM (many-to-many) table name.
     *
     * @param string $MM The MM table name.
     */
    public function setMM(string $MM): void
    {
        $this->MM = $MM;
    }

    /**
     * Set the match fields for MM relations.
     *
     * @param array $MMMatchFields The match fields.
     */
    public function setMMMatchFields(array $MMMatchFields): void
    {
        $this->MM_match_fields = $MMMatchFields;
    }

    /**
     * Set the opposite field for MM relations.
     *
     * @param string $MMOppositeField The opposite field.
     */
    public function setMMOppositeField(string $MMOppositeField): void
    {
        $this->MM_opposite_field = $MMOppositeField;
    }

    /**
     * Set the opposite usage for MM relations.
     *
     * @param array $MMOppositeUsage The opposite usage.
     */
    public function setMMOppositeUsage(array $MMOppositeUsage): void
    {
        $this->MM_oppositeUsage = $MMOppositeUsage;
    }

    /**
     * Set the WHERE clause for the MM table.
     *
     * @param string $MMTableWhere The WHERE clause.
     */
    public function setMMTableWhere(string $MMTableWhere): void
    {
        $this->MM_table_where = $MMTableWhere;
    }

    /**
     * Set whether the field allows multiple selections.
     *
     * @param bool|null $multiple Whether multiple selections are allowed.
     */
    public function setMultiple(?bool $multiple): void
    {
        $this->multiple = $multiple;
    }

    /**
     * Set the filter items for multi-select.
     *
     * @param array $multiSelectFilterItems The filter items.
     */
    public function setMultiSelectFilterItems(array $multiSelectFilterItems): void
    {
        $this->multiSelectFilterItems = $multiSelectFilterItems;
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
     * @param int $size The size of the field.
     */
    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    /**
     * Set the sorting configuration for items.
     *
     * @param array $sortItems The sorting configuration.
     */
    public function setSortItems(array $sortItems): void
    {
        $this->sortItems = $sortItems;
    }

    /**
     * Set the tree configuration for the field.
     *
     * @param SelectFieldTreeConfig|null $treeConfig The tree configuration.
     */
    public function setTreeConfig(?SelectFieldTreeConfig $treeConfig): void
    {
        $this->treeConfig = $treeConfig;
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
        
        if ($foreign->getAllowNonIdValues() !== null) {
            $this->allowNonIdValues = $foreign->getAllowNonIdValues();
        }

        if ($foreign->getAuthMode() !== '') {
            $this->authMode = $foreign->getAuthMode();
        }

        if ($foreign->getAutoSizeMax() !== -1) {
            $this->autoSizeMax = $foreign->getAutoSizeMax();
        }

        if ($foreign->getDbFieldLength() !== -1) {
            $this->dbFieldLength = $foreign->getDbFieldLength();
        }

        if ($foreign->getDefault() !== '') {
            $this->default = $foreign->getDefault();
        }

        if ($foreign->getDisableNoMatchingValueElement() !== null) {
            $this->disableNoMatchingValueElement = $foreign->getDisableNoMatchingValueElement();
        }

        if ($foreign->getDontRemapTablesOnCopy() !== '') {
            $this->dontRemapTablesOnCopy = $foreign->getDontRemapTablesOnCopy();
        }

        if ($foreign->getExclusiveKeys() !== '') {
            $this->exclusiveKeys = $foreign->getExclusiveKeys();
        }

        if ($foreign->getFileFolderConfig() !== null) {
            if ($this->fileFolderConfig !== null) {
                $this->fileFolderConfig->mergeConfig($foreign->getFileFolderConfig());
            } else {
                $this->fileFolderConfig = $foreign->getFileFolderConfig();
            }
        }

        if ($foreign->getForeignTable() !== '') {
            $this->foreign_table = $foreign->getForeignTable();
        }

        if ($foreign->getForeignTableItemGroup() !== '') {
            $this->foreign_table_item_group = $foreign->getForeignTableItemGroup();
        }

        if ($foreign->getForeignTablePrefix() !== '') {
            $this->foreign_table_prefix = $foreign->getForeignTablePrefix();
        }

        if ($foreign->getForeignTableWhere() !== '') {
            $this->foreign_table_where = $foreign->getForeignTableWhere();
        }

        if ($foreign->getIdentifier() !== '') {
            $this->identifier = $foreign->getIdentifier();
        }

        if ($foreign->getItemGroups() !== []) {
            $this->itemGroups = $foreign->getItemGroups();
        }

        if ($foreign->getItems() !== []) {
            $this->items = $foreign->getItems();
        }

        // Removed duplicate condition for getItems()

        if ($foreign->getItemsProcFunc() !== '') {
            $this->itemsProcFunc = $foreign->getItemsProcFunc();
        }

        if ($foreign->getLocalizeReferencesAtParentLocalization() !== null) {
            $this->localizeReferencesAtParentLocalization = $foreign->getLocalizeReferencesAtParentLocalization();
        }

        if ($foreign->getMaxitems() !== -1) {
            $this->maxitems = $foreign->getMaxitems();
        }

        if ($foreign->getMinitems() !== -1) {
            $this->minitems = $foreign->getMinitems();
        }

        if ($foreign->getMM() !== '') {
            $this->MM = $foreign->getMM();
        }

        if ($foreign->getMMMatchFields() !== []) {
            $this->MM_match_fields = $foreign->getMMMatchFields();
        }

        if ($foreign->getMMOppositeField() !== '') {
            $this->MM_opposite_field = $foreign->getMMOppositeField();
        }

        if ($foreign->getMMOppositeUsage() !== []) {
            $this->MM_oppositeUsage = $foreign->getMMOppositeUsage();
        }

        if ($foreign->getMMTableWhere() !== '') {
            $this->MM_table_where = $foreign->getMMTableWhere();
        }

        if ($foreign->getMultiple() !== null) {
            $this->multiple = $foreign->getMultiple();
        }

        if ($foreign->getMultiSelectFilterItems() !== []) {
            $this->multiSelectFilterItems = $foreign->getMultiSelectFilterItems();
        }

        if ($foreign->getReadOnly() !== null) {
            $this->readOnly = $foreign->getReadOnly();
        }

        if ($foreign->getSize() !== -1) {
            $this->size = $foreign->getSize();
        }

        if ($foreign->getSortItems() !== []) {
            $this->sortItems = $foreign->getSortItems();
        }

        if ($foreign->getTreeConfig() !== null) {
            if ($this->treeConfig !== null) {
                $this->treeConfig->mergeConfig($foreign->getTreeConfig());
            } else {
                $this->treeConfig = $foreign->getTreeConfig();
            }
        }
    }

    /**
     * Valid render types for the select field.
     */
    const RENDER_TYPES = [
        'selectSingle',
        'selectSingleBox',
        'selectCheckBox',
        'selectMultipleSideBySide',
        'selectTree'
    ];

    /**
     * Validate the authorization mode configuration.
     *
     * @param mixed $value The value to validate.
     * @param array $config The configuration array.
     *
     * @throws Exception If the validation fails.
     */
    private function _validateAuthMode(mixed $value, array $config): void
    {
        if (!is_string($value)) {
            throw new Exception (
                "'Select' field '$this->identifier' configuration 'authMode' must be of type string."
            );
        }
        if ('explicitAllow' !== $value) {
            throw new Exception (
                "'Select' field '$this->identifier' configuration 'authMode' must have the value 'explicitAllow'."
            );
        }
    }

    /**
     * Validate the foreign table item group configuration.
     *
     * @param mixed $value The value to validate.
     * @param array $config The configuration array.
     *
     * @throws Exception If the validation fails.
     */
    private function _foreignTableItemGroup(mixed $value, array $config): void
    {
        if (!is_string($value)) {
            throw new Exception (
                "'Select' field '$this->identifier' configuration 'foreign_table_item_group' must be of type string."
            );
        }
        if (!isset($config['foreign_table'])) {
            throw new Exception (
                "'Select' field '$this->identifier' configuration 'foreign_table_item_group' needs 'foreign_table'."
            );
        }

        $this->validateField($value, $config, 'foreign_table_item_group', 'Select', [$config['foreign_table']]);
    }

    /**
     * Validate and set the items configuration.
     *
     * @param mixed $items The items to validate and set.
     * @param array $config The configuration array.
     *
     * @throws Exception If the validation fails.
     */
    private function _validateAndSetItems(mixed $items, array $config): void
    {
        if (!is_array($items)) {
            throw new Exception (
                "'Select' field '$this->identifier' configuration 'items' must be of type array.\n" .
                "Fix:\nitems:\n  -\n    label: 'item1'\n    value: 'value1'\n  -\n    label: 'item2'\n    value: 2"
            );
        }
        foreach ($items as $index => $item) {
            if (!is_array($item)) {
                throw new Exception (
                    "'Select' field '$this->identifier' configuration 'items[$index]' must be of type array.\n" .
                    "Fix:\nitems:\n  -\n    label: 'item1'\n    value: 'value1'\n  -\n    label: 'item2'\n    value: 2"
                );
            }
            $this->items[$index] = new SelectFieldItem($item, $index, $this->identifier);
        }
    }

    /**
     * Validate the sorting configuration for items.
     *
     * @param mixed $sorting The sorting configuration to validate.
     * @param array $config The configuration array.
     *
     * @throws Exception If the validation fails.
     */
    private function _validateSortItems(mixed $sorting, array $config): void
    {
        if (!is_array($sorting)) {
            throw new Exception (
                "'Select' field '$this->identifier' configuration 'sortItems' must be of type array.\n" .
                "Fix:\nsortItems:\n  label: 'asc'\n  value: 'desc'"
            );
        }
        $this->validateArrayStringString($sorting, $config, 'sortItems', 'Select');
        foreach ($sorting as $key => $value) {
            if ('desc' !== strtolower($value) && 'asc' !== strtolower($value)) {
                $this->validateUserFunc($value, $config, "sortItems['$key']", 'Select');
            }
        }
    }

    /**
     * Validate a configuration setting based on its type and render type.
     *
     * @param mixed $value The value to validate.
     * @param array $config The configuration array.
     * @param int $type The type of the value (e.g., boolean, integer, string).
     * @param string $setting The setting being validated.
     * @param array $renderTypes The valid render types.
     *
     * @throws Exception If the validation fails.
     */
    private function _validateConfigRenderType(mixed $value, array $config, int $type, string $setting, array $renderTypes): void
    {
        $renderType = $config['renderType'];
        switch ($type) {
            case parent::BOOL_TYPE:
                if (!is_bool($value)) {
                    throw new Exception (
                        "'Select' field '$this->identifier' configuration '$setting' must be of type boolean."
                    );
                }
                break;
            case parent::INTEGER_TYPE:
                if ($this->handleIntegers($value) === null) {
                    throw new Exception (
                        "'Select' field '$this->identifier' configuration '$setting' must be of type integer or " .
                        "a string that represents an integer."
                    );
                }
                break;
            case parent::STRING_TYPE:
                if (!is_string($value)) {
                    throw new Exception (
                        "'Select' field '$this->identifier' configuration '$setting' must be of type string."
                    );
                }
                break;
            case parent::FLOAT_TYPE:
                if (!is_float($value)) {
                    throw new Exception (
                        "'Select' field '$this->identifier' configuration '$setting' must be of type float."
                    );
                }
                break;
        }
        if (!in_array($renderType, $renderTypes)) {
            throw new Exception (
                "'Select' field '$this->identifier' configuration '$setting' needs 'renderType' to be set " .
                "to one of these: " . implode(', ', $renderTypes)
            );
        }
    }

    /**
     * Validate the "don't remap tables on copy" configuration.
     *
     * @param mixed $value The value to validate.
     * @param array $config The configuration array.
     *
     * @throws Exception If the validation fails.
     */
    private function _validateDontRemapTablesOnCopy(mixed $value, array $config): void
    {
        if (!is_string($value)) {
            throw new Exception (
                "'Select' field '$this->identifier' configuration 'dontRemapTablesOnCopy' must be of type string."
            );
        }

        $splittedTables = GeneralUtility::trimExplode(',', $value);
        $i = 0;
        foreach ($splittedTables as $table) {
            $this->validateTable($table, $config, "dontRemapTablesOnCopy[$i]", 'Select');
            $i++;
        }
    }

    /**
     * Validate the multi-select filter items configuration.
     *
     * @param mixed $items The items to validate.
     * @param array $config The configuration array.
     *
     * @throws Exception If the validation fails.
     */
    private function _validateMultiSelectFilterItems(mixed $items, array $config): void
    {
        if (!is_array($items)) {
            throw new Exception (
                "'Select' field '$this->identifier' configuration 'multiSelectFilterItems' must be of type array.\n" .
                "Fix:\nmultiSelectFilterItems:\n  -\n    - 'filter'\n    - 'label'\n  -\n    - 'filter2'\n    - 'label2'"
            );
        }

        $renderType = $config['renderType'];
        if ($renderType !== 'selectMultipleSideBySide') {
            throw new Exception (
                "'Select' field '$this->identifier' configuration 'multiSelectFilterItems' needs 'renderType' to be set " .
                "to 'selectMultipleSideBySide'."
            );
        }
        $i = 0;
        foreach ($items as $item) {
            if (count($item) !== 2) {
                throw new Exception (
                    "'Select' field '$this->identifier' configuration 'multiSelectFilterItems[$i]' array must contain exactly two items.\n" .
                    "Fix:\nmultiSelectFilterItems:\n  -\n    - 'filter'\n    - 'label'\n  -\n    - 'filter2'\n    - 'label2'"
                );
            }
            if (!is_string($item[0])) {
                throw new Exception (
                    "'Select' field '$this->identifier' configuration 'multiSelectFilterItems[$i][0]' must be of type string.\n" .
                    "Fix:\nmultiSelectFilterItems:\n  -\n    - 'filter'\n    - 'label'\n  -\n    - 'filter2'\n    - 'label2'"
                );
            }
            if (!is_string($item[1])) {
                throw new Exception (
                    "'Select' field '$this->identifier' configuration 'multiSelectFilterItems[$i][1]' must be of type string.\n" .
                    "Fix:\nmultiSelectFilterItems:\n  -\n    - 'filter'\n    - 'label'\n  -\n    - 'filter2'\n    - 'label2'"
                );
            }
            $i++;
        }
    }

    /**
     * Validate the tree configuration.
     *
     * @param mixed $value The value to validate.
     * @param array $config The configuration array.
     *
     * @throws Exception If the validation fails.
     */
    private function _validateTreeConfig(mixed $value, array $config): void
    {
        if (!is_array($value)) {
            throw new Exception (
                "'Select' field '$this->identifier' configuration 'treeConfig' must be of type array.\n" .
                "Fix:\ntreeConfig:\n  parentField: 'parentFieldIdentifier'\n  startingPoints: '1,43,47'"
            );
        }
        if ($this->renderType !== 'selectTree') {
            throw new Exception (
                "'Select' field '$this->identifier' configuration 'treeConfig' needs 'renderType' to be set " .
                "to 'selectTree'."
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
        $this->checkRequirements($globalConf, ['identifier', 'renderType'], 'Select');
        $this->identifier = $globalConf['identifier'];
        $properties = get_object_vars($this);
        foreach ($config as $configKey => $value) {
            if ($GLOBALS['CbBuilder']['config']['Strict'] === true) {
                if ($this->isValidConfig($properties, $configKey)) {
                    switch ($configKey) {
                        case 'authMode':
                            $this->_validateAuthMode($value, $globalConf);
                            $this->authMode = 'explicitAllow';
                            break;
                        case 'autoSizeMax':
                            $this->validateAutoSizeMax($value, $globalConf, 'Select');
                            $this->autoSizeMax = intval($value);
                            break;
                        case 'dbFieldLength':
                            $this->validateInteger($value, $globalConf, 'dbFieldLength', 'Select', 1, 255);
                            $this->dbFieldLength = intval($value);
                            break;
                        case 'dontRemapTablesOnCopy':
                            $this->_validateConfigRenderType($value, $globalConf, self::STRING_TYPE, 'dontRemapTablesOnCopy', ['selectCheckBox']);
                            $this->_validateDontRemapTablesOnCopy($value, $globalConf);
                            $this->dontRemapTablesOnCopy = $value;
                            break;
                        case 'exclusiveKeys':
                            $this->_validateConfigRenderType($value, $globalConf, self::STRING_TYPE, 'exclusiveKeys', ['selectMultipleSideBySide', 'selectTree']);
                            $this->exclusiveKeys = $value;
                            break;
                        case 'fileFolderConfig':
                            $this->fileFolderConfig = new SelectFieldFileFolderConfig();
                            $this->fileFolderConfig->arrayToConfig($value, $fieldProperties, $globalConf);
                            break;
                        case 'foreign_table':
                            $this->validateTable($value, $globalConf, 'foreign_table', 'Select');
                            $this->foreign_table = $value;
                            break;
                        case 'foreign_table_item_group':
                            $this->_foreignTableItemGroup($value, $globalConf);
                            $this->foreign_table_item_group = $value;
                            break;
                        case 'itemGroups':
                            $this->validateArrayStringString($value, $globalConf, 'itemGroups', 'Select');
                            $this->itemGroups = $value;
                            break;
                        case 'items':
                            $this->_validateAndSetItems($value, $globalConf);
                            break;
                        case 'itemsProcFunc':
                            $this->validateUserFunc($value, $globalConf, 'itemsProcFunc', 'Select');
                            $this->itemsProcFunc = $value;
                            break;
                        case 'localizeReferencesAtParentLocalization':
                            $this->_validateConfigRenderType($value, $globalConf, self::BOOL_TYPE, 'localizeReferencesAtParentLocalization', ['selectSingleBox']);
                            $this->localizeReferencesAtParentLocalization = $value;
                            break;
                        case 'maxitems':
                            $this->validateInteger($value, $globalConf, 'maxitems', 'Select', 1, PHP_INT_MAX, true, true, 'minitems');
                            $this->maxitems = intval($value);
                            break;
                        case 'minitems':
                            $this->validateInteger($value, $globalConf, 'minitems', 'Select', 1, PHP_INT_MAX, true, false, 'maxitems');
                            $this->minitems = intval($value);
                            break;
                        case 'MM':
                            $this->validateTable($value, $globalConf, 'MM', 'Select');
                            $this->MM = $value;
                            break;
                        case 'MM_match_fields':
                            $this->validateMmMatchFields($value, $globalConf, 'Select');
                            $this->MM_match_fields = $value;
                            break;
                        case 'MM_opposite_field':
                            $this->validateMmOppositeField($value, $globalConf, 'Select');
                            $this->MM_opposite_field = $value;
                            break;
                        case 'MM_oppositeUsage':
                            $this->validateMmOppositeUsage($value, $globalConf, 'Select');
                            $this->MM_oppositeUsage = $value;
                            break;
                        case 'multiSelectFilterItems':
                            $this->_validateMultiSelectFilterItems($value, $globalConf);
                            $this->multiSelectFilterItems = $value;
                            break;
                        case 'size':
                            $this->validateInteger($value, $globalConf, 'size', 'Select', 1, PHP_INT_MAX, true, false, 'autoSizeMax');
                            $this->size = intval($value);
                            break;
                        case 'sortItems':
                            $this->_validateSortItems($value, $globalConf);
                            $this->sortItems = $value;
                            break;
                        case 'treeConfig':
                            $this->_validateTreeConfig($value, $globalConf);
                            $this->treeConfig = new SelectFieldTreeConfig($value, $globalConf);
                            break;
                        default:
                            $this->addGenericConfig($configKey, $value, $globalConf, $this, $fieldProperties);
                            break;
                    }
                } else if (!in_array($configKey, $fieldProperties)) {
                    $identifier = $globalConf['identifier'];
                    throw new Exception (
                        "'Select' field '$identifier' configuration '$configKey' is not valid.\n" .
                        "Valid settings are: " . implode(', ', array_keys($properties))
                    );
                }
            } else {
                $this->$configKey = $value;
            }
        }
    }
}

/**
 * Class representing a select field.
 */
final class SelectField extends Field
{
    /**
     * Configuration for the select field.
     */
    protected SelectFieldConfig $config;

    /**
     * Get the configuration for the select field.
     *
     * @return SelectFieldConfig The configuration.
     */
    public function getConfig(): SelectFieldConfig
    {
        return $this->config;
    }

    /**
     * Convert an array representation to a field.
     *
     * @param array $field The array representation of the field.
     */
    private function _arrayToField(array $field): void
    {
        $this->__arrayToField('select', $field);
        $field['table'] = $this->table;
        $field['useExistingField'] = $this->useExistingField;
        $field['identifier'] = $this->identifier;
        $config = new SelectFieldConfig();
        $config->arrayToConfig($field, array_keys(get_object_vars($this)));
        $this->config = $config;
    }

    /**
     * Merge the current field with another field.
     *
     * @param SelectField $foreign The foreign field to merge with.
     */
    public function mergeField(SelectField $foreign): void
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
     * @return string The parsed field.
     */
    public function parseField(int $mode, int $level): string
    {
        return $this->__parseField($this->config, $mode, $level);
    }

    /**
     * Convert the field to an array representation.
     *
     * @return array The array representation of the field.
     */
    public function fieldToArray(): array
    {
        return $this->__fieldToArray($this->config);
    }

    /**
     * Constructor for the select field.
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