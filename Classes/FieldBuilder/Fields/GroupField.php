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

use DS\CbBuilder\FieldBuilder\FieldBuilder;
use DS\CbBuilder\Utility\SimpleDatabaseQuery;
use Exception;
use InvalidArgumentException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Configuration class for group field suggest options.
 */
final class GroupFieldSuggestOptionsConfig extends Config
{
    /**
     * Key for the suggest options.
     */
    protected string $key = '';

    /**
     * Additional fields to include in the search.
     */
    protected string $additionalSearchFields = '';

    /**
     * Additional WHERE clause conditions for the query.
     */
    protected string $addWhere = '';

    /**
     * CSS class for styling the suggest options.
     */
    protected string $cssClass = '';

    /**
     * Maximum number of items to display in the result list.
     */
    protected int $maxItemsInResultList = -1;

    /**
     * Maximum length of the path title.
     */
    protected int $maxPathTitleLength = -1;

    /**
     * Minimum number of characters required for the search.
     */
    protected int $minimumCharacters = -1;

    /**
     * ORDER BY clause for sorting the results.
     */
    protected string $orderBy = '';

    /**
     * List of page IDs to restrict the search to.
     */
    protected string $pidList = '';

    /**
     * Depth of page IDs to consider.
     */
    protected int $pidDepth = -1;

    /**
     * Class responsible for receiving the search results.
     */
    protected string $receiverClass = '';

    /**
     * Function to render the search results.
     */
    protected string $renderFunc = '';

    /**
     * Condition for the search query.
     */
    protected string $searchCondition = '';

    /**
     * Whether to search for the whole phrase.
     */
    protected ?bool $searchWholePhrase = null;

    // Getters
    /**
     * Get the key for the suggest options.
     *
     * @return string The key.
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Get the additional fields to include in the search.
     *
     * @return string The additional search fields.
     */
    public function getAdditionalSearchFields(): string
    {
        return $this->additionalSearchFields;
    }

    /**
     * Get the additional WHERE clause conditions for the query.
     *
     * @return string The additional WHERE conditions.
     */
    public function getAddWhere(): string
    {
        return $this->addWhere;
    }

    /**
     * Get the CSS class for styling the suggest options.
     *
     * @return string The CSS class.
     */
    public function getCssClass(): string
    {
        return $this->cssClass;
    }

    /**
     * Get the maximum number of items to display in the result list.
     *
     * @return int The maximum number of items.
     */
    public function getMaxItemsInResultList(): int
    {
        return $this->maxItemsInResultList;
    }

    /**
     * Get the maximum length of the path title.
     *
     * @return int The maximum path title length.
     */
    public function getMaxPathTitleLength(): int
    {
        return $this->maxPathTitleLength;
    }

    /**
     * Get the minimum number of characters required for the search.
     *
     * @return int The minimum number of characters.
     */
    public function getMinimumCharacters(): int
    {
        return $this->minimumCharacters;
    }

    /**
     * Get the ORDER BY clause for sorting the results.
     *
     * @return string The ORDER BY clause.
     */
    public function getOrderBy(): string
    {
        return $this->orderBy;
    }

    /**
     * Get the list of page IDs to restrict the search to.
     *
     * @return string The list of page IDs.
     */
    public function getPidList(): string
    {
        return $this->pidList;
    }

    /**
     * Get the depth of page IDs to consider.
     *
     * @return int The depth of page IDs.
     */
    public function getPidDepth(): int
    {
        return $this->pidDepth;
    }

    /**
     * Get the class responsible for receiving the search results.
     *
     * @return string The receiver class.
     */
    public function getReceiverClass(): string
    {
        return $this->receiverClass;
    }

    /**
     * Get the function to render the search results.
     *
     * @return string The render function.
     */
    public function getRenderFunc(): string
    {
        return $this->renderFunc;
    }

    /**
     * Get the condition for the search query.
     *
     * @return string The search condition.
     */
    public function getSearchCondition(): string
    {
        return $this->searchCondition;
    }

    /**
     * Get whether to search for the whole phrase.
     *
     * @return bool|null Whether to search for the whole phrase.
     */
    public function isSearchWholePhrase(): ?bool
    {
        return $this->searchWholePhrase;
    }

    // Setters
    /**
     * Set the key for the suggest options.
     *
     * @param string $key The key to set.
     */
    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    /**
     * Set the additional fields to include in the search.
     *
     * @param string $additionalSearchFields The additional search fields to set.
     */
    public function setAdditionalSearchFields(string $additionalSearchFields): void
    {
        $this->additionalSearchFields = $additionalSearchFields;
    }

    /**
     * Set the additional WHERE clause conditions for the query.
     *
     * @param string $addWhere The additional WHERE conditions to set.
     */
    public function setAddWhere(string $addWhere): void
    {
        $this->addWhere = $addWhere;
    }

    /**
     * Set the CSS class for styling the suggest options.
     *
     * @param string $cssClass The CSS class to set.
     */
    public function setCssClass(string $cssClass): void
    {
        $this->cssClass = $cssClass;
    }

    /**
     * Set the maximum number of items to display in the result list.
     *
     * @param int $maxItemsInResultList The maximum number of items to set.
     */
    public function setMaxItemsInResultList(int $maxItemsInResultList): void
    {
        $this->maxItemsInResultList = $maxItemsInResultList;
    }

    /**
     * Set the maximum length of the path title.
     *
     * @param int $maxPathTitleLength The maximum path title length to set.
     */
    public function setMaxPathTitleLength(int $maxPathTitleLength): void
    {
        $this->maxPathTitleLength = $maxPathTitleLength;
    }

    /**
     * Set the minimum number of characters required for the search.
     *
     * @param int $minimumCharacters The minimum number of characters to set.
     */
    public function setMinimumCharacters(int $minimumCharacters): void
    {
        $this->minimumCharacters = $minimumCharacters;
    }

    /**
     * Set the ORDER BY clause for sorting the results.
     *
     * @param string $orderBy The ORDER BY clause to set.
     */
    public function setOrderBy(string $orderBy): void
    {
        $this->orderBy = $orderBy;
    }

    /**
     * Set the list of page IDs to restrict the search to.
     *
     * @param string $pidList The list of page IDs to set.
     */
    public function setPidList(string $pidList): void
    {
        $this->pidList = $pidList;
    }

    /**
     * Set the depth of page IDs to consider.
     *
     * @param int $pidDepth The depth of page IDs to set.
     */
    public function setPidDepth(int $pidDepth): void
    {
        $this->pidDepth = $pidDepth;
    }

    /**
     * Set the class responsible for receiving the search results.
     *
     * @param string $receiverClass The receiver class to set.
     */
    public function setReceiverClass(string $receiverClass): void
    {
        $this->receiverClass = $receiverClass;
    }

    /**
     * Set the function to render the search results.
     *
     * @param string $renderFunc The render function to set.
     */
    public function setRenderFunc(string $renderFunc): void
    {
        $this->renderFunc = $renderFunc;
    }

    /**
     * Set the condition for the search query.
     *
     * @param string $searchCondition The search condition to set.
     */
    public function setSearchCondition(string $searchCondition): void
    {
        $this->searchCondition = $searchCondition;
    }

    /**
     * Set whether to search for the whole phrase.
     *
     * @param bool|null $searchWholePhrase Whether to search for the whole phrase.
     */
    public function setSearchWholePhrase(?bool $searchWholePhrase): void
    {
        $this->searchWholePhrase = $searchWholePhrase;
    }

    /**
     * Merge the configuration with another instance.
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

        if ($foreign->getKey() !== '') {
            $this->key = $foreign->getKey();
        }

        if ($foreign->getAdditionalSearchFields() !== '') {
            $this->additionalSearchFields = $foreign->getAdditionalSearchFields();
        }

        if ($foreign->getAddWhere() !== '') {
            $this->addWhere = $foreign->getAddWhere();
        }

        if ($foreign->getCssClass() !== '') {
            $this->cssClass = $foreign->getCssClass();
        }

        if ($foreign->getMaxItemsInResultList() >= 0) {
            $this->maxItemsInResultList = $foreign->getMaxItemsInResultList();
        }

        if ($foreign->getMaxPathTitleLength() >= 0) {
            $this->maxPathTitleLength = $foreign->getMaxPathTitleLength();
        }

        if ($foreign->getMinimumCharacters() >= 0) {
            $this->minimumCharacters = $foreign->getMinimumCharacters();
        }

        if ($foreign->getOrderBy() !== '') {
            $this->orderBy = $foreign->getOrderBy();
        }

        if ($foreign->getPidList() !== '') {
            $this->pidList = $foreign->getPidList();
        }

        if ($foreign->getPidDepth() >= 0) {
            $this->pidDepth = $foreign->getPidDepth();
        }

        if ($foreign->getReceiverClass() !== '') {
            $this->receiverClass = $foreign->getReceiverClass();
        }

        if ($foreign->getRenderFunc() !== '') {
            $this->renderFunc = $foreign->getRenderFunc();
        }

        if ($foreign->getSearchCondition() !== '') {
            $this->searchCondition = $foreign->getSearchCondition();
        }

        if ($foreign->isSearchWholePhrase() !== null) {
            $this->searchWholePhrase = $foreign->isSearchWholePhrase();
        }
    }

    /**
     * Validate additional search fields.
     *
     * @param mixed $fields The fields to validate.
     * @param array $config The configuration array.
     *
     * @throws Exception If the fields are not valid.
     */
    private function _validateAdditionalSearchFields(mixed $fields, array $config): void
    {
        $identifier = $config['identifier'];
        $key = $this->key;

        if (!is_string($fields)) {
            throw new Exception(
                "'Group' field '$identifier' configuration 'suggestOptions['$key']['additionalSearchFields']' must be a string if set."
            );
        }

        $fields = GeneralUtility::trimExplode(',', $fields);

        $i = 0;
        foreach ($fields as $field) {
            try {
                $this->validateField($field, $config, "suggestOptions['$key']['additionalSearchFields'][$i]", 'Group');
            } catch (\Throwable $th) {
                throw new Exception(
                    $th->getMessage() . "\nFix: suggestOptions:\n  $key:\n    additionalSearchFields: 'nav_title, url, or_any_valid_table_field'"
                );
            }
            $i++;
        }
    }

    /**
     * Validate the minimum number of characters.
     *
     * @param mixed $value The value to validate.
     * @param array $config The configuration array.
     *
     * @throws Exception If the value is not valid.
     */
    private function _validateMinimumCharacters(mixed $value, array $config): void
    {
        $identifier = $config['identifier'];
        $key = $this->key;

        $this->validateInteger($value, $config, "suggestOptions['$key']['minimumCharacters']", 'Group', 1, PHP_INT_MAX);

        if ($key !== 'default') {
            throw new Exception(
                "'Group' field '$identifier' configuration 'suggestOptions['$key']['minimumCharacters']' only works in the default configuration array suggestOptions['default']['minimumCharacters'].\n" .
                "Fix: suggestOptions:\n  default:\n    minimumCharacters: 3"
            );
        }
    }

    /**
     * Validate and set the page ID list.
     *
     * @param mixed $value The value to validate and set.
     * @param array $config The configuration array.
     *
     * @throws Exception If the value is not valid.
     */
    private function _validateAndSetPidList(mixed $value, array $config): void
    {
        $identifier = $config['identifier'];
        $key = $this->key;

        if (!is_string($value)) {
            throw new Exception(
                "'Group' field '$identifier' configuration 'suggestOptions['$key']['pidList']' must be a string if set."
            );
        }

        $splitted = GeneralUtility::trimExplode(',', $value);
        $pids = [];
        $i = 0;
        foreach ($splitted as $pid) {
            if (($pid = $this->handleIntegers($pid)) === null) {
                throw new Exception(
                    "'Group' field '$identifier' configuration 'suggestOptions['$key']['pidList'][$i]' must be an integer or a string representing an integer."
                );
            }
            $pids[$i++] = $pid;
        }
        $this->pidList = implode(',', $pids);
    }

    /**
     * Convert an array to configuration.
     *
     * @param array $config The configuration array.
     * @param array $globalConf The global configuration.
     * @param string $key The key for the configuration.
     */
    public function arrayToConfig(array $config, array $fieldProperties, ?array $globalConf = NULL, mixed $misc = NULL): void
    {
        $properties = get_object_vars($this);
        if (!is_string($misc)) {
            throw new InvalidArgumentException (
                "Parameter 'misc' must be of type string"
            );
        }
        //We just abuse the table parameter to gain the key
        $this->key = $misc;
        foreach ($config as $configKey => $value) {
            if ($this->isValidConfig($properties, $configKey)) {
                switch ($configKey) {
                    case 'additionalSearchFields':
                        $this->_validateAdditionalSearchFields($value, $globalConf);
                        $this->additionalSearchFields = $value;
                        break;
                    case 'maxItemsInResultList':
                        $this->validateInteger($value, $globalConf, "suggestOptions['$misc']['maxItemsInResultList']", 'Group', 1, PHP_INT_MAX);
                        $this->maxItemsInResultList = intval($value);
                        break;
                    case 'maxPathTitleLength':
                        $this->validateInteger($value, $globalConf, "suggestOptions['$misc']['maxPathTitleLength']", 'Group', 1, PHP_INT_MAX);
                        $this->maxPathTitleLength = intval($value);
                        break;
                    case 'minimumCharacters':
                        $this->_validateMinimumCharacters($value, $globalConf);
                        $this->minimumCharacters = intval($value);
                        break;
                    case 'pidList':
                        $this->_validateAndSetPidList($value, $globalConf);
                        break;
                    case 'pidDepth':
                        $this->validateInteger($value, $globalConf, "suggestOptions['$misc']['pidDepth']", 'Group', 1, PHP_INT_MAX);
                        $this->pidDepth = intval($value);
                        break;
                    case 'renderFunc':
                        $this->validateUserFunc($value, $globalConf, "suggestOptions['$misc']['renderFunc']", 'Group');
                        $this->renderFunc = $value;
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
 * Configuration class for group fields.
 */
final class GroupFieldConfig extends Config
{
    /**
     * Allowed values for the group field.
     */
    protected string $allowed = '';

    /**
     * Maximum size for auto-sizing.
     */
    protected int $autoSizeMax = -1;

    /**
     * Default value for the group field.
     */
    protected string $default = '';

    /**
     * Entry points for the element browser.
     */
    protected array $elementBrowserEntryPoints = [];

    /**
     * Filter settings for the group field.
     */
    protected array $filter = [];

    /**
     * Foreign table for the group field.
     */
    protected string $foreign_table = '';

    /**
     * Whether to hide the delete icon.
     */
    protected ?bool $hideDeleteIcon = null;

    /**
     * Whether to hide move icons.
     */
    protected ?bool $hideMoveIcons = null;

    /**
     * Whether to hide the suggest functionality.
     */
    protected ?bool $hideSuggest = null;

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
     * MM (Many-to-Many) table for the group field.
     */
    protected string $MM = '';

    /**
     * Match fields for the MM table.
     */
    protected array $MM_match_fields = [];

    /**
     * Opposite field for the MM table.
     */
    protected string $MM_opposite_field = '';

    /**
     * Opposite usage settings for the MM table.
     */
    protected array $MM_oppositeUsage = [];

    /**
     * WHERE clause for the MM table.
     */
    protected string $MM_table_where = '';

    /**
     * Whether multiple values are allowed.
     */
    protected ?bool $multiple = null;

    /**
     * Whether to prepend the table name.
     */
    protected ?bool $prepend_tname = null;

    /**
     * Whether the field is read-only.
     */
    protected ?bool $readOnly = null;

    /**
     * Size of the group field.
     */
    protected int $size = -1;

    /**
     * Suggest options for the group field.
     */
    protected array $suggestOptions = [];

    // Getters

    /**
     * Get the allowed values for the group field.
     *
     * @return string The allowed values.
     */
    public function getAllowed(): string
    {
        return $this->allowed;
    }

    /**
     * Get the maximum size for auto-sizing.
     *
     * @return int The maximum size.
     */
    public function getAutoSizeMax(): int
    {
        return $this->autoSizeMax;
    }

    /**
     * Get the default value for the group field.
     *
     * @return string The default value.
     */
    public function getDefault(): string
    {
        return $this->default;
    }

    /**
     * Get the entry points for the element browser.
     *
     * @return array The entry points.
     */
    public function getElementBrowserEntryPoints(): array
    {
        return $this->elementBrowserEntryPoints;
    }

    /**
     * Get the filter settings for the group field.
     *
     * @return array The filter settings.
     */
    public function getFilter(): array
    {
        return $this->filter;
    }

    /**
     * Get the foreign table for the group field.
     *
     * @return string The foreign table.
     */
    public function getForeignTable(): string
    {
        return $this->foreign_table;
    }

    /**
     * Get whether to hide the delete icon.
     *
     * @return bool|null Whether to hide the delete icon.
     */
    public function isHideDeleteIcon(): ?bool
    {
        return $this->hideDeleteIcon;
    }

    /**
     * Get whether to hide move icons.
     *
     * @return bool|null Whether to hide move icons.
     */
    public function isHideMoveIcons(): ?bool
    {
        return $this->hideMoveIcons;
    }

    /**
     * Get whether to hide the suggest functionality.
     *
     * @return bool|null Whether to hide the suggest functionality.
     */
    public function isHideSuggest(): ?bool
    {
        return $this->hideSuggest;
    }

    /**
     * Get whether to localize references at parent localization.
     *
     * @return bool|null Whether to localize references.
     */
    public function isLocalizeReferencesAtParentLocalization(): ?bool
    {
        return $this->localizeReferencesAtParentLocalization;
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
     * Get the MM (Many-to-Many) table for the group field.
     *
     * @return string The MM table.
     */
    public function getMM(): string
    {
        return $this->MM;
    }

    /**
     * Get the match fields for the MM table.
     *
     * @return array The match fields.
     */
    public function getMMMatchFields(): array
    {
        return $this->MM_match_fields;
    }

    /**
     * Get the opposite field for the MM table.
     *
     * @return string The opposite field.
     */
    public function getMMOppositeField(): string
    {
        return $this->MM_opposite_field;
    }

    /**
     * Get the opposite usage settings for the MM table.
     *
     * @return array The opposite usage settings.
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
     * Get whether multiple values are allowed.
     *
     * @return bool|null Whether multiple values are allowed.
     */
    public function isMultiple(): ?bool
    {
        return $this->multiple;
    }

    /**
     * Get whether to prepend the table name.
     *
     * @return bool|null Whether to prepend the table name.
     */
    public function isPrependTname(): ?bool
    {
        return $this->prepend_tname;
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
     * Get the size of the group field.
     *
     * @return int The size.
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Get the suggest options for the group field.
     *
     * @return array The suggest options.
     */
    public function getSuggestOptions(): array
    {
        return $this->suggestOptions;
    }

    /**
     * Set the allowed values for the group field.
     *
     * @param string $allowed The allowed values to set.
     */
    public function setAllowed(string $allowed): void
    {
        $this->allowed = $allowed;
    }

    /**
     * Set the maximum size for auto-sizing.
     *
     * @param int $autoSizeMax The maximum size to set.
     */
    public function setAutoSizeMax(int $autoSizeMax): void
    {
        $this->autoSizeMax = $autoSizeMax;
    }

    /**
     * Set the default value for the group field.
     *
     * @param string $default The default value to set.
     */
    public function setDefault(string $default): void
    {
        $this->default = $default;
    }

    /**
     * Set the entry points for the element browser.
     *
     * @param array $elementBrowserEntryPoints The entry points to set.
     */
    public function setElementBrowserEntryPoints(array $elementBrowserEntryPoints): void
    {
        $this->elementBrowserEntryPoints = $elementBrowserEntryPoints;
    }

    /**
     * Set the filter settings for the group field.
     *
     * @param array $filter The filter settings to set.
     */
    public function setFilter(array $filter): void
    {
        $this->filter = $filter;
    }

    /**
     * Set the foreign table for the group field.
     *
     * @param string $foreign_table The foreign table to set.
     */
    public function setForeignTable(string $foreign_table): void
    {
        $this->foreign_table = $foreign_table;
    }

    /**
     * Set whether to hide the delete icon.
     *
     * @param bool|null $hideDeleteIcon Whether to hide the delete icon.
     */
    public function setHideDeleteIcon(?bool $hideDeleteIcon): void
    {
        $this->hideDeleteIcon = $hideDeleteIcon;
    }

    /**
     * Set whether to hide move icons.
     *
     * @param bool|null $hideMoveIcons Whether to hide move icons.
     */
    public function setHideMoveIcons(?bool $hideMoveIcons): void
    {
        $this->hideMoveIcons = $hideMoveIcons;
    }

    /**
     * Set whether to hide the suggest functionality.
     *
     * @param bool|null $hideSuggest Whether to hide the suggest functionality.
     */
    public function setHideSuggest(?bool $hideSuggest): void
    {
        $this->hideSuggest = $hideSuggest;
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
     * @param int $maxitems The maximum number of items to set.
     */
    public function setMaxItems(int $maxitems): void
    {
        $this->maxitems = $maxitems;
    }

    /**
     * Set the minimum number of items required.
     *
     * @param int $minitems The minimum number of items to set.
     */
    public function setMinItems(int $minitems): void
    {
        $this->minitems = $minitems;
    }

    /**
     * Set the MM (Many-to-Many) table for the group field.
     *
     * @param string $MM The MM table to set.
     */
    public function setMM(string $MM): void
    {
        $this->MM = $MM;
    }

    /**
     * Set the match fields for the MM table.
     *
     * @param array $MM_match_fields The match fields to set.
     */
    public function setMMMatchFields(array $MM_match_fields): void
    {
        $this->MM_match_fields = $MM_match_fields;
    }

    /**
     * Set the opposite field for the MM table.
     *
     * @param string $MM_opposite_field The opposite field to set.
     */
    public function setMMOppositeField(string $MM_opposite_field): void
    {
        $this->MM_opposite_field = $MM_opposite_field;
    }

    /**
     * Set the opposite usage settings for the MM table.
     *
     * @param array $MM_oppositeUsage The opposite usage settings to set.
     */
    public function setMMOppositeUsage(array $MM_oppositeUsage): void
    {
        $this->MM_oppositeUsage = $MM_oppositeUsage;
    }

    /**
     * Set the WHERE clause for the MM table.
     *
     * @param string $MM_table_where The WHERE clause to set.
     */
    public function setMMTableWhere(string $MM_table_where): void
    {
        $this->MM_table_where = $MM_table_where;
    }

    /**
     * Set whether multiple values are allowed.
     *
     * @param bool|null $multiple Whether multiple values are allowed.
     */
    public function setMultiple(?bool $multiple): void
    {
        $this->multiple = $multiple;
    }

    /**
     * Set whether to prepend the table name.
     *
     * @param bool|null $prepend_tname Whether to prepend the table name.
     */
    public function setPrependTname(?bool $prepend_tname): void
    {
        $this->prepend_tname = $prepend_tname;
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
     * Set the size of the group field.
     *
     * @param int $size The size to set.
     */
    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    /**
     * Set the suggest options for the group field.
     *
     * @param array $suggestOptions The suggest options to set.
     */
    public function setSuggestOptions(array $suggestOptions): void
    {
        $this->suggestOptions = $suggestOptions;
    }

    /**
     * Merge the configuration with another instance.
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

        if ($foreign->getAllowed() !== '') {
            $this->allowed = $foreign->getAllowed();
        }

        if ($foreign->getAutoSizeMax() >= 0) {
            $this->autoSizeMax = $foreign->getAutoSizeMax();
        }

        if ($foreign->getDefault() !== '') {
            $this->default = $foreign->getDefault();
        }

        if (!empty($foreign->getElementBrowserEntryPoints())) {
            $this->elementBrowserEntryPoints = $foreign->getElementBrowserEntryPoints();
        }

        if (!empty($foreign->getFilter())) {
            $this->filter = $foreign->getFilter();
        }

        if ($foreign->getForeignTable() !== '') {
            $this->foreign_table = $foreign->getForeignTable();
        }

        if ($foreign->isHideDeleteIcon() !== null) {
            $this->hideDeleteIcon = $foreign->isHideDeleteIcon();
        }

        if ($foreign->isHideMoveIcons() !== null) {
            $this->hideMoveIcons = $foreign->isHideMoveIcons();
        }

        if ($foreign->isHideSuggest() !== null) {
            $this->hideSuggest = $foreign->isHideSuggest();
        }

        if ($foreign->isLocalizeReferencesAtParentLocalization() !== null) {
            $this->localizeReferencesAtParentLocalization = $foreign->isLocalizeReferencesAtParentLocalization();
        }

        if ($foreign->getMaxItems() >= 0) {
            $this->maxitems = $foreign->getMaxItems();
        }

        if ($foreign->getMinItems() >= 0) {
            $this->minitems = $foreign->getMinItems();
        }

        if ($foreign->getMM() !== '') {
            $this->MM = $foreign->getMM();
        }

        if (!empty($foreign->getSuggestOptions())) {
            $this->suggestOptions = $foreign->getSuggestOptions();
        }
    }

    /**
     * Validate a table list.
     *
     * @param mixed $entry The table list to validate.
     * @param array $config The configuration array.
     * @param string $setting The setting being validated.
     *
     * @throws Exception If the table list is not valid.
     */
    private function _validateTableList(mixed $entry, array $config, string $setting): void
    {
        $identifier = $config['identifier'];

        if (!is_string($entry)) {
            throw new Exception(
                "'Group' field '$identifier' configuration 'allowed' must be of type string."
            );
        }

        $splitted = GeneralUtility::trimExplode(',', $entry);
        $sdq = new SimpleDatabaseQuery();

        foreach ($splitted as $field) {
            if (!FieldBuilder::isSurpressedWarning(152082917) && !$sdq->tableExists($field) && !FieldBuilder::fieldExists($field, 'Collection')) {
                throw new Exception(
                    "WARNING: 'Group' field '$identifier' configuration '$setting' field '$field' does neither exist in the db nor " .
                    "it will be created in this process.\n" .
                    "You can surpress this warning in the cbConfig.yaml by adding the code 152082917 to surpressWarning."
                );
            }
        }
    }

    /**
     * Validate element browser entry points.
     *
     * @param mixed $entry The entry points to validate.
     * @param array $config The configuration array.
     *
     * @throws Exception If the entry points are not valid.
     */
    private function _validateElementBrowserEntryPoints(mixed $entry, array $config): void
    {
        $identifier = $config['identifier'];

        if (!is_array($entry)) {
            throw new Exception(
                "'Group' field '$identifier' configuration 'elementBrowserEntryPoints' must be of type array."
            );
        }

        $i = 0;
        foreach ($entry as $key => $value) {
            if (!is_string($key)) {
                throw new Exception(
                    "'Group' field '$identifier' configuration 'elementBrowserEntryPoints[$i][$key]' key must be of type string."
                );
            }

            if ($key === '_default' && (!is_string($value) && !is_int($value))) {
                throw new Exception(
                    "'Group' field '$identifier' configuration 'elementBrowserEntryPoints[$i][$key]' value must be of type string that " .
                    "represents an entry point if the key is set to '_default'. Fix: _default: '1:/styleguide/', " .
                    "_default: '###CURRENT_PID###', _default: '###PAGE_TSCONFIG_ID###', _default: '###SITEROOT###', ..."
                );
            }
            $i++;
        }
    }

    /**
     * Validate a user function configuration.
     *
     * @param mixed $value The value to validate.
     * @param array $config The configuration array.
     * @param string $setting The setting being validated.
     */
    private function _validateConfig_userFunc(mixed $value, array $config, string $setting): void
    {
        $this->validateUserFunc($value, $config, $setting, 'Group');
    }

    /**
     * Validate parameters configuration.
     *
     * @param mixed $value The value to validate.
     * @param array $config The configuration array.
     * @param string $setting The setting being validated.
     */
    private function _validateConfig_parameters(mixed $value, array $config, string $setting): void
    {
        $this->validateArrayStringString($value, $config, $setting, 'Group');
    }

    /**
     * Validate and set suggest options.
     *
     * @param mixed $value The suggest options to validate and set.
     * @param array $config The configuration array.
     *
     * @throws Exception If the suggest options are not valid.
     */
    private function _validateAndSetSuggestOptions(mixed $value, array $config): void
    {
        $identifier = $config['identifier'];

        if (!is_array($value)) {
            throw new Exception(
                "'Group' field '$identifier' configuration 'suggestOptions' must be of type array." .
                "Fix:\nsuggestOptions:\n  default:\n    additionalSearchFields: '...'\n    addWhere: '...'"
            );
        }

        $i = 0;
        foreach ($value as $key => $setting) {
            if (!is_string($key)) {
                throw new Exception(
                    "'Group' field '$identifier' configuration 'suggestOptions[$i]' key must be of type string." .
                    "Fix:\nsuggestOptions:\n  default:\n    additionalSearchFields: '...'\n    addWhere: '...'"
                );
            }

            if (!is_array($setting)) {
                throw new Exception(
                    "'Group' field '$identifier' configuration 'suggestOptions[$key]' must be of type array." .
                    "Fix:\nsuggestOptions:\n  default:\n    additionalSearchFields: '...'\n    addWhere: '...'"
                );
            }

            $this->suggestOptions[$key] = new GroupFieldSuggestOptionsConfig();
            $this->suggestOptions[$key]->arrayToConfig($setting, [], $config, $key);
        }
    }

    /**
     * Convert an array to configuration.
     *
     * @param array $config The configuration array.
     * @param array $fieldProperties The field properties.
     */
    public function arrayToConfig(array $config, array $fieldProperties, ?array $globalConf = NULL, mixed $misc = NULL): void
    {
        $globalConf = $globalConf ?? $config;
        $this->checkRequirements($globalConf, ['identifier', 'allowed'], 'Group');
        $properties = get_object_vars($this);
        foreach ($config as $configKey => $value) {
            if ($this->isValidConfig($properties, $configKey)) {
                if ($GLOBALS['CbBuilder']['config']['Strict'] === true) {
                    switch ($configKey) {
                        case 'allowed':
                            $this->_validateTableList($value, $globalConf, 'allowed');
                            $this->allowed = $value;
                            break;
                        case 'autoSizeMax':
                            $this->validateAutoSizeMax($value, $globalConf, 'Group');
                            $this->autoSizeMax = intval($value);
                            break;
                        case 'elementBrowserEntryPoints':
                            $this->_validateElementBrowserEntryPoints($value, $globalConf);
                            $this->elementBrowserEntryPoints = $value;
                            break;
                        case 'filter':
                            $this->validateFilter($value, $globalConf, 'Group');
                            $this->filter = $value;
                            break;
                        case 'foreign_table':
                            $this->validateTable($value, $globalConf, 'foreign_table', 'Group');
                            $this->foreign_table = $value;
                            break;
                        case 'maxitems':
                            $this->validateInteger($value, $globalConf, 'maxitems', 'Group', 1, PHP_INT_MAX, true, true, 'minitems');
                            $this->maxitems = intval($value);
                            break;
                        case 'minitems':
                            $this->validateInteger($value, $globalConf, 'maxitems', 'Group', 1, PHP_INT_MAX, true, false, 'maxitems');
                            $this->minitems = intval($value);
                            break;
                        case 'MM':
                            $this->validateTable($value, $globalConf, 'MM', 'Group');
                            $this->MM = $value;
                            break;
                        case 'MM_match_fields':
                            $this->validateMmMatchFields($value, $globalConf, 'Group');
                            $this->MM_match_fields = $value;
                            break;
                        case 'MM_opposite_field':
                            $this->validateField($value, $globalConf, 'MM_opposite_field', 'Group');
                            $this->MM_opposite_field = $value;
                            break;
                        case 'MM_oppositeUsage':
                            $this->validateMmOppositeUsage($value, $globalConf, 'Group');
                            $this->MM_oppositeUsage = $value;
                            break;
                        case 'size':
                            $this->validateInteger($value, $globalConf, 'size', 'Group', 1, PHP_INT_MAX, true, false, 'autoSizeMax');
                            $this->size = intval($value);
                            break;
                        case 'suggestOptions':
                            $this->_validateAndSetSuggestOptions($value, $globalConf);
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
                    "'Group' field '$identifier' configuration '$configKey' is not valid.\n" .
                    "Valid settings are: " . implode(', ', array_keys($properties))
                );
            }
        }
    }
}

/**
 * Class representing a group field.
 */
final class GroupField extends Field
{
    /**
     * Configuration for the group field.
     */
    protected GroupFieldConfig $config;

    /**
     * Get the configuration for the group field.
     *
     * @return GroupFieldConfig The configuration.
     */
    public function getConfig(): GroupFieldConfig
    {
        return $this->config;
    }

    /**
     * Convert an array to a field.
     *
     * @param array $field The array to convert.
     */
    private function _arrayToField(array $field): void
    {
        $this->__arrayToField('group', $field);
        $field['table'] = $this->table;
        $field['useExistingField'] = $this->useExistingField;
        $field['identifier'] = $this->identifier;

        $config = new GroupFieldConfig();
        $config->arrayToConfig($field, array_keys(get_object_vars($this)));
        $this->config = $config;
    }

    /**
     * Merge another group field into this one.
     *
     * @param GroupField $foreign The foreign field to merge.
     */
    public function mergeField(GroupField $foreign): void
    {
        $this->config->mergeConfig($foreign->getConfig());
        $this->mergeFields($foreign);
    }

    /**
     * Parse the field into a string.
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
     * Convert the field to an array.
     *
     * @return array The field as an array.
     */
    public function fieldToArray(): array
    {
        return $this->__fieldToArray($this->config);
    }

    /**
     * Constructor for the group field.
     *
     * @param array $field The field configuration.
     * @param string $table The table for the field.
     */
    public function __construct(array $field, string $table)
    {
        $this->table = $table;
        $this->_arrayToField($field);
    }
}