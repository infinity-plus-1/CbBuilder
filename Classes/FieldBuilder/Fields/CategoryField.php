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

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

/**
 * Class CategoryFieldConfig
 * 
 * This class represents the configuration for a category field.
 */
final class CategoryFieldConfig extends Config
{
    /**
     * Default value for the field.
     */
    protected string $default = '';

    /**
     * Exclusive keys for the field.
     */
    protected string $exclusiveKeys = '';

    /**
     * Foreign table name.
     */
    protected string $foreign_table = '';

    /**
     * Prefix for the foreign table.
     */
    protected string $foreign_table_prefix = '';

    /**
     * WHERE clause for the foreign table.
     */
    protected string $foreign_table_where = '';

    /**
     * Item groups for the field.
     */
    protected array $itemGroups = [];

    /**
     * Maximum number of items allowed.
     */
    protected int $maxitems = -1;

    /**
     * Minimum number of items required.
     */
    protected int $minitems = -1;

    /**
     * MM (Match Multiple) configuration.
     */
    protected string $MM = '';

    /**
     * Whether the field is read-only.
     */
    protected ?bool $readOnly = NULL;

    /**
     * Relationship type.
     */
    protected string $relationship = '';

    /**
     * Size of the field.
     */
    protected int $size = -1;

    /**
     * Tree configuration.
     */
    protected array $treeConfig = [];

    /**
     * Merge the configuration from another CategoryFieldConfig instance.
     * 
     * @param CategoryFieldConfig $foreign The configuration to merge.
     * 
     * @throws Exception If an error occurs during merging.
     */
    public function mergeConfig(Config $foreign): void
    {
        if (!$foreign instanceof self) {
            throw new InvalidArgumentException (
                "Config 'foreign' must be of type " . get_class($this)
            );
        }
        try {
            $this->mergeMainConfig($foreign);
            $this->default = ($foreign->getDefault() !== '') ? $foreign->getDefault() : $this->default;
            $this->exclusiveKeys = ($foreign->getExclusiveKeys() !== '') ? $foreign->getExclusiveKeys() : $this->exclusiveKeys;
            $this->foreign_table = ($foreign->getForeignTable() !== '') ? $foreign->getForeignTable() : $this->foreign_table;
            $this->foreign_table_prefix = ($foreign->getForeignTablePrefix() !== '') ? $foreign->getForeignTablePrefix() : $this->foreign_table_prefix;
            $this->foreign_table_where = ($foreign->getForeignTableWhere() !== '') ? $foreign->getForeignTableWhere() : $this->foreign_table_where;
            $this->itemGroups = (!empty($foreign->getItemGroups())) ? $foreign->getItemGroups() : $this->itemGroups;
            $this->maxitems = ($foreign->getMaxitems() >= 0) ? $foreign->getMaxitems() : $this->maxitems;
            $this->minitems = ($foreign->getMinitems() >= 0) ? $foreign->getMinitems() : $this->minitems;
            $this->MM = ($foreign->getMM() !== '') ? $foreign->getMM() : $this->MM;
            $this->readOnly = ($foreign->getReadOnly() !== NULL) ? $foreign->getReadOnly() : $this->readOnly;
            $this->relationship = ($foreign->getRelationship() !== '') ? $foreign->getRelationship() : $this->relationship;
            $this->size = ($foreign->getSize() >= 0) ? $foreign->getSize() : $this->size;
            $this->treeConfig = (!empty($foreign->getTreeConfig())) ? $foreign->getTreeConfig() : $this->treeConfig;
        } catch (Exception $e) {
            throw new Exception("Error merging configurations: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get the default value of the field.
     * 
     * @return string The default value.
     */
    public function getDefault(): string
    {
        return $this->default;
    }

    /**
     * Set the default value of the field.
     * 
     * @param string $default The new default value.
     * 
     * @return self The instance itself for chaining.
     */
    public function setDefault(string $default): self
    {
        $this->default = $default;

        return $this;
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
     * Set the exclusive keys for the field.
     * 
     * @param string $exclusiveKeys The new exclusive keys.
     * 
     * @return self The instance itself for chaining.
     */
    public function setExclusiveKeys(string $exclusiveKeys): self
    {
        $this->exclusiveKeys = $exclusiveKeys;

        return $this;
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
     * Set the foreign table name.
     * 
     * @param string $foreign_table The new foreign table name.
     * 
     * @return self The instance itself for chaining.
     */
    public function setForeignTable(string $foreign_table): self
    {
        $this->foreign_table = $foreign_table;

        return $this;
    }

    /**
     * Get the prefix for the foreign table.
     * 
     * @return string The foreign table prefix.
     */
    public function getForeignTablePrefix(): string
    {
        return $this->foreign_table_prefix;
    }

    /**
     * Set the prefix for the foreign table.
     * 
     * @param string $foreign_table_prefix The new foreign table prefix.
     * 
     * @return self The instance itself for chaining.
     */
    public function setForeignTablePrefix(string $foreign_table_prefix): self
    {
        $this->foreign_table_prefix = $foreign_table_prefix;

        return $this;
    }

    /**
     * Get the WHERE clause for the foreign table.
     * 
     * @return string The foreign table WHERE clause.
     */
    public function getForeignTableWhere(): string
    {
        return $this->foreign_table_where;
    }

    /**
     * Set the WHERE clause for the foreign table.
     * 
     * @param string $foreign_table_where The new foreign table WHERE clause.
     * 
     * @return self The instance itself for chaining.
     */
    public function setForeignTableWhere(string $foreign_table_where): self
    {
        $this->foreign_table_where = $foreign_table_where;

        return $this;
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
     * Set the item groups for the field.
     * 
     * @param array $itemGroups The new item groups.
     * 
     * @return self The instance itself for chaining.
     */
    public function setItemGroups(array $itemGroups): self
    {
        $this->itemGroups = $itemGroups;

        return $this;
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
     * Set the maximum number of items allowed.
     * 
     * @param int $maxitems The new maximum number of items.
     * 
     * @return self The instance itself for chaining.
     */
    public function setMaxitems(int $maxitems): self
    {
        $this->maxitems = $maxitems;

        return $this;
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
     * Set the minimum number of items required.
     * 
     * @param int $minitems The new minimum number of items.
     * 
     * @return self The instance itself for chaining.
     */
    public function setMinitems(int $minitems): self
    {
        $this->minitems = $minitems;

        return $this;
    }

    /**
     * Get the MM (Match Multiple) configuration.
     * 
     * @return string The MM configuration.
     */
    public function getMM(): string
    {
        return $this->MM;
    }

    /**
     * Set the MM (Match Multiple) configuration.
     * 
     * @param string $MM The new MM configuration.
     * 
     * @return self The instance itself for chaining.
     */
    public function setMM(string $MM): self
    {
        $this->MM = $MM;

        return $this;
    }

    /**
     * Get the value of readOnly
     * 
     * @return bool|null Whether the field is read-only.
     */
    public function getReadOnly(): ?bool
    {
        return $this->readOnly;
    }

    /**
     * Set the value of readOnly
     * 
     * @param bool|null $readOnly Whether the field should be read-only.
     * 
     * @return self The instance itself for chaining.
     */
    public function setReadOnly(?bool $readOnly): self
    {
        $this->readOnly = $readOnly;

        return $this;
    }

    /**
     * Get the value of relationship
     * 
     * @return string The relationship type.
     */
    public function getRelationship(): string
    {
        return $this->relationship;
    }

    /**
     * Set the value of relationship
     * 
     * @param string $relationship The new relationship type.
     * 
     * @return self The instance itself for chaining.
     */
    public function setRelationship(string $relationship): self
    {
        $this->relationship = $relationship;

        return $this;
    }

    /**
     * Get the value of size
     * 
     * @return int The size of the field.
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Set the value of size
     * 
     * @param int $size The new size of the field.
     * 
     * @return self The instance itself for chaining.
     */
    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get the value of treeConfig
     * 
     * @return array The tree configuration.
     */
    public function getTreeConfig(): array
    {
        return $this->treeConfig;
    }

    /**
     * Set the value of treeConfig
     * 
     * @param array $treeConfig The new tree configuration.
     * 
     * @return self The instance itself for chaining.
     */
    public function setTreeConfig(array $treeConfig): self
    {
        $this->treeConfig = $treeConfig;

        return $this;
    }

    /**
     * Valid relationship keywords.
     */
    const RELATIONSHIP_KEYWORDS = [
        'oneToOne', 'oneToMany', 'manyToMany'
    ];

    /**
     * Valid tree configuration keywords.
     */
    const TREECONFIG_KEYWORDS = [
        'dataProvider' => parent::STRING_TYPE,
        'childrenField' => parent::STRING_TYPE,
        'parentField' => parent::STRING_TYPE,
        'startingPoints' => parent::STRING_TYPE,
        'appearance' => parent::FUNCTION,
    ];

    /**
     * Valid appearance configuration keywords for treeConfig.
     */
    const TREECONFIG_APPEARANCE_KEYWORDS = [
        'showHeader' => parent::BOOL_TYPE,
        'expandAll' => parent::BOOL_TYPE,
        'maxLevels' => parent::INTEGER_TYPE,
        'nonSelectableLevels' => parent::STRING_TYPE,
    ];

    /**
     * Validate a field within the tree configuration.
     * 
     * @param string $field The field to validate.
     * @param array $config The configuration.
     * @param string $identifier The identifier of the field.
     * @param string $setting The specific setting being validated.
     * 
     * @throws Exception If the foreign table is not set or if validation fails.
     */
    private function _validateField(string $field, array $config, string $identifier, string $setting): void
    {
        if ($this->foreign_table === '') {
            throw new Exception(
                "'Category' field '$identifier' configuration 'treeConfig['appearance']['$setting']' needs 'foreign_table' to be set."
            );
        }

        $this->validateField($field, $config, $setting, 'Category', [$this->foreign_table]);
    }

    /**
     * Validate the 'appearance' option within the tree configuration.
     * 
     * @param mixed $appearance The appearance configuration.
     * @param string $identifier The identifier of the field.
     * 
     * @throws Exception If validation fails.
     */
    private function _validateTreeConfigOption_appearance(mixed $appearance, string $identifier): void
    {
        if (!is_array($appearance)) {
            throw new Exception(
                "'Category' field '$identifier' configuration 'treeConfig['appearance']' must be of type array."
            );
        }
        $i = 0;
        foreach ($appearance as $key => $value) {
            if (!array_key_exists($key, self::TREECONFIG_APPEARANCE_KEYWORDS)) {
                throw new Exception(
                    "'Category' field '$identifier' configuration 'treeConfig['appearance'][$i]' $key is no valid keyword. " .
                    "Valid keywords are: " . implode(', ', array_keys(self::TREECONFIG_APPEARANCE_KEYWORDS))
                );
            }
            switch (self::TREECONFIG_APPEARANCE_KEYWORDS[$key]) {
                case parent::BOOL_TYPE:
                    if (!is_bool($value)) {
                        throw new Exception(
                            "'Category' field '$identifier' configuration 'treeConfig['appearance']['$key']' value must be of type " .
                            "boolean."
                        );
                    }
                    break;
                case parent::INTEGER_TYPE:
                    if (!$this->handleIntegers($value)) {
                        throw new Exception(
                            "'Category' field '$identifier' configuration 'treeConfig['appearance']['$key']' value must be of type " .
                            "integer or a string representing an integer."
                        );
                    }
                    break;
                case parent::STRING_TYPE:
                    if (!is_string($value)) {
                        throw new Exception(
                            "'Category' field '$identifier' configuration 'treeConfig['appearance']['$key']' value must be of type " .
                            "string."
                        );
                    }
                    break;
            }

            if ($key === 'nonSelectableLevels') {
                $splitted = GeneralUtility::trimExplode(',', $value);
                $j = 0;
                foreach ($splitted as $num) {
                    if ($this->handleIntegers($num) === NULL) {
                        throw new Exception(
                            "'Category' field '$identifier' configuration 'treeConfig['appearance']['nonSelectableLevels'][$j]' value " .
                            "must be of type string representing an integer."
                        );
                    }
                    $j++;
                }
            }
            $i++;
        }
    }

    /**
     * Validate the exclusive keys configuration.
     * 
     * @param mixed $entry The exclusive keys value.
     * @param array $config The configuration.
     * 
     * @throws Exception If validation fails.
     */
    private function _validateExclusiveKeys(mixed $entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_string($entry)) {
            throw new Exception(
                "'Category' field '$identifier' configuration 'exclusiveKeys' must be of type string."
            );
        }
        $splitted = GeneralUtility::trimExplode(',', $entry);

        $i = 0;
        $splitted = array_walk($splitted, function ($v) use ($identifier, &$i) {
            if (!$v = $this->handleIntegers($v)) {
                throw new Exception(
                    "'Category' field '$identifier' configuration 'exclusiveKeys[$i]' must be of type integer or a string representing an " .
                    "integer."
                );
            }
            $i++;
            return $v;
        });
    }

    /**
     * Validate the foreign table configuration.
     * 
     * @param mixed $entry The foreign table value.
     * @param array $config The configuration.
     * 
     * @throws Exception If validation fails.
     */
    private function _validateForeignTable(mixed $entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (!$this->tableExists($entry, $config)) {
            throw new Exception(
                "'Category' field '$identifier' configuration 'foreign_table' table '$entry' does not exist and will not " .
                "be created by the builder. Fix: Check for typos, choose another table, create the table manually or add " .
                "a Collection with an identifier identical to the table name."
            );
        }
    }


        /**
     * Validate the item groups configuration.
     * 
     * @param mixed $entry The item groups value.
     * @param array $config The configuration.
     * 
     * @throws Exception If validation fails.
     */
    private function _validateItemGroups(mixed $entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_array($entry)) {
            throw new Exception(
                "'Category' field '$identifier' configuration 'itemGroups' must be of type array."
            );
        }
        $i = 0;
        foreach ($entry as $key => $value) {
            if (!is_string($key) && !is_int($key)) {
                throw new Exception(
                    "'Category' field '$identifier' configuration 'itemGroups[$i]' key must be of type string or integer."
                );
            }
            if (!is_string($value)) {
                throw new Exception(
                    "'Category' field '$identifier' configuration 'itemGroups[$i]' value must be of type string."
                );
            }
            $i++;
        }
    }

    /**
     * Validate the relationship configuration.
     * 
     * @param mixed $entry The relationship value.
     * @param array $config The configuration.
     * 
     * @throws Exception If validation fails.
     */
    private function _validateRelationship(mixed $entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_string($entry)) {
            throw new Exception(
                "'Category' field '$identifier' configuration 'relationship' must be of type string."
            );
        }

        if (!in_array($entry, self::RELATIONSHIP_KEYWORDS)) {
            throw new Exception(
                "'Category' field '$identifier' configuration 'relationship' must be one " .
                "of the following keywords: " . implode(', ', self::RELATIONSHIP_KEYWORDS)
            );
        }
    }

    /**
     * Validate the tree configuration.
     * 
     * @param mixed $entry The tree configuration value.
     * @param array $config The configuration.
     * 
     * @throws Exception If validation fails.
     */
    private function _validateTreeConfig(mixed $entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_array($entry)) {
            throw new Exception(
                "'Category' field '$identifier' configuration 'treeConfig' must be of type array."
            );
        }

        $i = 0;
        foreach ($entry as $key => $value) {
            if (!array_key_exists($key, self::TREECONFIG_KEYWORDS)) {
                throw new Exception(
                    "'Category' field '$identifier' configuration 'treeConfig[$i]' $key is no valid keyword. " .
                    "Valid keywords are: " . implode(', ', array_keys(self::TREECONFIG_KEYWORDS))
                );
            }

            switch (self::TREECONFIG_KEYWORDS[$key]) {
                case parent::STRING_TYPE:
                    if (!is_string($value)) {
                        throw new Exception(
                            "'Category' field '$identifier' configuration 'treeConfig['$key']' value must be of type " .
                            "string."
                        );
                    }
                    break;
                case parent::FUNCTION:
                    $function = '_validateTreeConfigOption_' . $key;
                    call_user_func([$this, $function], $value, $identifier, $config);
                    break;
            }

            switch ($key) {
                case 'parentField':
                    $this->_validateField($value, $config, $identifier, 'parentField');
                    break;
                case 'childrenField':
                    $this->_validateField($value, $config, $identifier, 'childrenField');
                    break;
            }
            $i++;
        }
    }

    /**
     * Convert an array configuration to the object's properties.
     * 
     * @param array $config The configuration array.
     * @param array $fieldProperties Additional field properties.
     * 
     * @throws Exception If a required property is missing or if validation fails.
     */
    public function arrayToConfig(array $config, array $fieldProperties, ?array $globalConf = NULL, mixed $misc = NULL): void
    {
        $globalConf = $globalConf ?? $config;
        $this->checkRequirements($globalConf, ['identifier'], 'Category');
        $properties = get_object_vars($this);
        foreach ($config as $configKey => $value) {
            if ($this->isValidConfig($properties, $configKey)) {
                if ($GLOBALS['CbBuilder']['config']['Strict'] === true) {
                    switch ($configKey) {
                        case 'exclusiveKeys':
                            $this->_validateExclusiveKeys($value, $globalConf);
                            $this->exclusiveKeys = $value;
                            break;
                        case 'foreign_table':
                            $this->_validateForeignTable($value, $globalConf);
                            $this->foreign_table = $value;
                            break;
                        case 'itemGroups':
                            $this->_validateItemGroups($value, $globalConf);
                            $this->itemGroups = $value;
                            break;
                        case 'maxitems':
                            $this->validateInteger($value, $globalConf, 'maxitems', 'Category', 1, PHP_INT_MAX, true, true, 'minitems');
                            $this->maxitems = intval($value);
                            break;
                        case 'minitems':
                            $this->validateInteger($value, $globalConf, 'minitems', 'Category', 1, PHP_INT_MAX, true, false, 'maxitems');
                            $this->minitems = intval($value);
                            break;
                        case 'relationship':
                            $this->_validateRelationship($value, $globalConf);
                            $this->relationship = $value;
                            break;
                        case 'size':
                            $this->validateInteger($value, $globalConf, 'size', 'Category', 1, PHP_INT_MAX);
                            $this->size = intval($value);
                            break;
                        case 'treeConfig':
                            $this->_validateTreeConfig($value, $globalConf);
                            $this->treeConfig = $value;
                            break;
                        default:
                            $this->addGenericConfig($configKey, $value, $globalConf, $this, $fieldProperties);
                            break;
                    }
                } else {
                    $this->$configKey = $value;
                }
            } else if (!in_array($configKey, $fieldProperties)) {
                $identifier = $config['identifier'];
                throw new Exception(
                    "'Category' field '$identifier' configuration '$configKey' is not valid.\n" .
                    "Valid settings are: " . implode(', ', array_keys($properties))
                );
            }
        }
    }
}

final class CategoryField extends Field
{
    /**
     * The configuration for this category field.
     */
    protected CategoryFieldConfig $config;

    /**
     * Get the configuration of this category field.
     * 
     * @return CategoryFieldConfig The configuration.
     */
    public function getConfig(): CategoryFieldConfig
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
        $this->__arrayToField('category', $field);
        $field['table'] = $this->table;
        $field['useExistingField'] = $this->useExistingField;
        $field['identifier'] = $this->identifier;
        $config = new CategoryFieldConfig();
        $config->arrayToConfig($field, array_keys(get_object_vars($this)));
        $this->config = $config;
    }

    /**
     * Merge the configuration of another category field into this one.
     * 
     * @param CategoryField $foreign The field to merge.
     */
    public function mergeField(CategoryField $foreign): void
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
     * Constructor for the CategoryField class.
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