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

use DS\CbBuilder\FieldBuilder\Tables\CollectionTable;
use DS\CbBuilder\FieldBuilder\Tables\Table;
use Exception;
use InvalidArgumentException;

/**
 * Class CollectionContainerAppearance
 * 
 * Represents the appearance configuration for a collection container.
 */
final class CollectionContainerAppearance extends Config
{
    /**
     * Whether to collapse all levels by default.
     */
    protected ?bool $collapseAll = NULL;

    /**
     * Whether to expand a single level by default.
     */
    protected ?bool $expandSingle = NULL;

    /**
     * Whether to show the new record link.
     */
    protected ?bool $showNewRecordLink = NULL;

    /**
     * Whether to add the title to the new record link.
     */
    protected ?bool $newRecordLinkAddTitle = NULL;

    /**
     * The title for the new record link.
     */
    protected string $newRecordLinkTitle = '';

    /**
     * The title for creating a new relation link.
     */
    protected string $createNewRelationLinkTitle = '';

    /**
     * The position of level links.
     */
    protected string $levelLinksPosition = '';

    /**
     * Whether to use combinations.
     */
    protected ?bool $useCombination = NULL;

    /**
     * Whether to suppress combination warnings.
     */
    protected ?bool $suppressCombinationWarning = NULL;

    /**
     * Whether to use sortable functionality.
     */
    protected ?bool $useSortable = NULL;

    /**
     * Whether to show possible localization records.
     */
    protected ?bool $showPossibleLocalizationRecords = NULL;

    /**
     * Whether to show the all localization link.
     */
    protected ?bool $showAllLocalizationLink = NULL;

    /**
     * Whether to show the synchronization link.
     */
    protected ?bool $showSynchronizationLink = NULL;

    /**
     * The enabled controls for the container.
     */
    protected array $enabledControls = [];

    /**
     * Whether to show the possible records selector.
     */
    protected ?bool $showPossibleRecordsSelector = NULL;

    /**
     * Whether the element browser is enabled.
     */
    protected ?bool $elementBrowserEnabled = NULL;

    /**
     * Valid positions for level links.
     */
    const LINKS_POSITIONS = ['top', 'bottom', 'both'];

    /**
     * Valid controls for the container.
     */
    const CONTROLS = ['info', 'new', 'dragdrop', 'sort', 'hide', 'delete', 'localize'];

    /**
     * Get whether to collapse all levels by default.
     * 
     * @return bool|null Whether to collapse all levels.
     */
    public function isCollapseAll(): ?bool
    {
        return $this->collapseAll;
    }

    /**
     * Get whether to expand a single level by default.
     * 
     * @return bool|null Whether to expand a single level.
     */
    public function isExpandSingle(): ?bool
    {
        return $this->expandSingle;
    }

    /**
     * Get whether to show the new record link.
     * 
     * @return bool|null Whether to show the new record link.
     */
    public function isShowNewRecordLink(): ?bool
    {
        return $this->showNewRecordLink;
    }

    /**
     * Get whether to add the title to the new record link.
     * 
     * @return bool|null Whether to add the title.
     */
    public function isNewRecordLinkAddTitle(): ?bool
    {
        return $this->newRecordLinkAddTitle;
    }

    /**
     * Get the title for the new record link.
     * 
     * @return string The title.
     */
    public function getNewRecordLinkTitle(): string
    {
        return $this->newRecordLinkTitle;
    }

    /**
     * Get the title for creating a new relation link.
     * 
     * @return string The title.
     */
    public function getCreateNewRelationLinkTitle(): string
    {
        return $this->createNewRelationLinkTitle;
    }

    /**
     * Get the position of level links.
     * 
     * @return string The position.
     */
    public function getLevelLinksPosition(): string
    {
        return $this->levelLinksPosition;
    }

    /**
     * Get whether to use combinations.
     * 
     * @return bool|null Whether to use combinations.
     */
    public function isUseCombination(): ?bool
    {
        return $this->useCombination;
    }

    /**
     * Get whether to suppress combination warnings.
     * 
     * @return bool|null Whether to suppress warnings.
     */
    public function isSuppressCombinationWarning(): ?bool
    {
        return $this->suppressCombinationWarning;
    }

    /**
     * Get whether to use sortable functionality.
     * 
     * @return bool|null Whether to use sortable.
     */
    public function isUseSortable(): ?bool
    {
        return $this->useSortable;
    }

    /**
     * Get whether to show possible localization records.
     * 
     * @return bool|null Whether to show localization records.
     */
    public function isShowPossibleLocalizationRecords(): ?bool
    {
        return $this->showPossibleLocalizationRecords;
    }

    /**
     * Get whether to show the all localization link.
     * 
     * @return bool|null Whether to show the all localization link.
     */
    public function isShowAllLocalizationLink(): ?bool
    {
        return $this->showAllLocalizationLink;
    }

    /**
     * Get whether to show the synchronization link.
     * 
     * @return bool|null Whether to show the synchronization link.
     */
    public function isShowSynchronizationLink(): ?bool
    {
        return $this->showSynchronizationLink;
    }

    /**
     * Get the enabled controls for the container.
     * 
     * @return array The enabled controls.
     */
    public function getEnabledControls(): array
    {
        return $this->enabledControls;
    }

    /**
     * Get whether to show the possible records selector.
     * 
     * @return bool|null Whether to show the records selector.
     */
    public function isShowPossibleRecordsSelector(): ?bool
    {
        return $this->showPossibleRecordsSelector;
    }

    /**
     * Get whether the element browser is enabled.
     * 
     * @return bool|null Whether the element browser is enabled.
     */
    public function isElementBrowserEnabled(): ?bool
    {
        return $this->elementBrowserEnabled;
    }

    /**
     * Set whether to collapse all levels by default.
     * 
     * @param bool|null $collapseAll Whether to collapse all levels.
     */
    public function setCollapseAll(?bool $collapseAll): void
    {
        $this->collapseAll = $collapseAll;
    }

    /**
     * Set whether to expand a single level by default.
     * 
     * @param bool|null $expandSingle Whether to expand a single level.
     */
    public function setExpandSingle(?bool $expandSingle): void
    {
        $this->expandSingle = $expandSingle;
    }

    /**
     * Set whether to show the new record link.
     * 
     * @param bool|null $showNewRecordLink Whether to show the new record link.
     */
    public function setShowNewRecordLink(?bool $showNewRecordLink): void
    {
        $this->showNewRecordLink = $showNewRecordLink;
    }

    /**
     * Set whether to add the title to the new record link.
     * 
     * @param bool|null $newRecordLinkAddTitle Whether to add the title.
     */
    public function setNewRecordLinkAddTitle(?bool $newRecordLinkAddTitle): void
    {
        $this->newRecordLinkAddTitle = $newRecordLinkAddTitle;
    }

    /**
     * Set the title for the new record link.
     * 
     * @param string $newRecordLinkTitle The title.
     */
    public function setNewRecordLinkTitle(string $newRecordLinkTitle): void
    {
        $this->newRecordLinkTitle = $newRecordLinkTitle;
    }

    /**
     * Set the title for creating a new relation link.
     * 
     * @param string $createNewRelationLinkTitle The title.
     */
    public function setCreateNewRelationLinkTitle(string $createNewRelationLinkTitle): void
    {
        $this->createNewRelationLinkTitle = $createNewRelationLinkTitle;
    }

    /**
     * Set the position of level links.
     * 
     * @param string $levelLinksPosition The position.
     */
    public function setLevelLinksPosition(string $levelLinksPosition): void
    {
        $this->levelLinksPosition = $levelLinksPosition;
    }

    /**
     * Set whether to use combinations.
     * 
     * @param bool|null $useCombination Whether to use combinations.
     */
    public function setUseCombination(?bool $useCombination): void
    {
        $this->useCombination = $useCombination;
    }

    /**
     * Set whether to suppress combination warnings.
     * 
     * @param bool|null $suppressCombinationWarning Whether to suppress warnings.
     */
    public function setSuppressCombinationWarning(?bool $suppressCombinationWarning): void
    {
        $this->suppressCombinationWarning = $suppressCombinationWarning;
    }

    /**
     * Set whether to use sortable functionality.
     * 
     * @param bool|null $useSortable Whether to use sortable.
     */
    public function setUseSortable(?bool $useSortable): void
    {
        $this->useSortable = $useSortable;
    }

    /**
     * Set whether to show possible localization records.
     * 
     * @param bool|null $showPossibleLocalizationRecords Whether to show localization records.
     */
    public function setShowPossibleLocalizationRecords(?bool $showPossibleLocalizationRecords): void
    {
        $this->showPossibleLocalizationRecords = $showPossibleLocalizationRecords;
    }

    /**
     * Set whether to show the all localization link.
     * 
     * @param bool|null $showAllLocalizationLink Whether to show the all localization link.
     */
    public function setShowAllLocalizationLink(?bool $showAllLocalizationLink): void
    {
        $this->showAllLocalizationLink = $showAllLocalizationLink;
    }

    /**
     * Set whether to show the synchronization link.
     * 
     * @param bool|null $showSynchronizationLink Whether to show the synchronization link.
     */
    public function setShowSynchronizationLink(?bool $showSynchronizationLink): void
    {
        $this->showSynchronizationLink = $showSynchronizationLink;
    }

    /**
     * Set the enabled controls for the container.
     * 
     * @param array $enabledControls The enabled controls.
     */
    public function setEnabledControls(array $enabledControls): void
    {
        $this->enabledControls = $enabledControls;
    }

    /**
     * Set whether to show the possible records selector.
     * 
     * @param bool|null $showPossibleRecordsSelector Whether to show the records selector.
     */
    public function setShowPossibleRecordsSelector(?bool $showPossibleRecordsSelector): void
    {
        $this->showPossibleRecordsSelector = $showPossibleRecordsSelector;
    }

    /**
     * Set whether the element browser is enabled.
     * 
     * @param bool|null $elementBrowserEnabled Whether the element browser is enabled.
     */
    public function setElementBrowserEnabled(?bool $elementBrowserEnabled): void
    {
        $this->elementBrowserEnabled = $elementBrowserEnabled;
    }

    /**
     * Merge the configuration from another CollectionContainerAppearance instance.
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
        if ($foreign->isCollapseAll() !== null) {
            $this->collapseAll = $foreign->isCollapseAll();
        }
        if ($foreign->isExpandSingle() !== null) {
            $this->expandSingle = $foreign->isExpandSingle();
        }
        if ($foreign->isShowNewRecordLink() !== null) {
            $this->showNewRecordLink = $foreign->isShowNewRecordLink();
        }
        if ($foreign->isNewRecordLinkAddTitle() !== null) {
            $this->newRecordLinkAddTitle = $foreign->isNewRecordLinkAddTitle();
        }
        if ($foreign->getNewRecordLinkTitle() !== '') {
            $this->newRecordLinkTitle = $foreign->getNewRecordLinkTitle();
        }
        if ($foreign->getCreateNewRelationLinkTitle() !== '') {
            $this->createNewRelationLinkTitle = $foreign->getCreateNewRelationLinkTitle();
        }
        if ($foreign->getLevelLinksPosition() !== '') {
            $this->levelLinksPosition = $foreign->getLevelLinksPosition();
        }
        if ($foreign->isUseCombination() !== null) {
            $this->useCombination = $foreign->isUseCombination();
        }
        if ($foreign->isSuppressCombinationWarning() !== null) {
            $this->suppressCombinationWarning = $foreign->isSuppressCombinationWarning();
        }
        if ($foreign->isUseSortable() !== null) {
            $this->useSortable = $foreign->isUseSortable();
        }
        if ($foreign->isShowPossibleLocalizationRecords() !== null) {
            $this->showPossibleLocalizationRecords = $foreign->isShowPossibleLocalizationRecords();
        }
        if ($foreign->isShowAllLocalizationLink() !== null) {
            $this->showAllLocalizationLink = $foreign->isShowAllLocalizationLink();
        }
        if ($foreign->isShowSynchronizationLink() !== null) {
            $this->showSynchronizationLink = $foreign->isShowSynchronizationLink();
        }
        if (!empty($foreign->getEnabledControls())) {
            $this->enabledControls = $foreign->getEnabledControls();
        }
        if ($foreign->isShowPossibleRecordsSelector() !== null) {
            $this->showPossibleRecordsSelector = $foreign->isShowPossibleRecordsSelector();
        }
        if ($foreign->isElementBrowserEnabled() !== null) {
            $this->elementBrowserEnabled = $foreign->isElementBrowserEnabled();
        }
    }

    /**
     * Validate the 'newRecordLinkTitle' configuration.
     * 
     * @param mixed $value The 'newRecordLinkTitle' value.
     * @param array $config The configuration.
     * 
     * @throws Exception If validation fails.
     */
    private function _validateNewRecordLinkTitle(mixed $value, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_string($value)) {
            throw new Exception(
                "'Collection' field '$identifier' configuration 'appearance['newRecordLinkTitle']' must be of type string."
            );
        }
        if (isset($config['newRecordLinkTitle']) && $config['newRecordLinkTitle'] === true) {
            throw new Exception(
                "'Collection' field '$identifier' configuration 'appearance['newRecordLinkTitle']' has no effect if " .
                "'appearance['newRecordLinkAddTitle']' is set to true."
            );
        }
    }

    /**
     * Validate the 'levelLinksPosition' configuration.
     * 
     * @param mixed $value The 'levelLinksPosition' value.
     * @param array $config The configuration.
     * 
     * @throws Exception If validation fails.
     */
    private function _validateLevelLinksPosition(mixed $value, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_string($value)) {
            throw new Exception(
                "'Collection' field '$identifier' configuration 'appearance['levelLinksPosition']' must be of type string."
            );
        }
        if (!in_array($value, self::LINKS_POSITIONS)) {
            throw new Exception(
                "'Collection' field '$identifier' configuration 'appearance['levelLinksPosition']' '$value' is no valid keyword.\n" .
                "Valid keywords are: " . implode(', ', self::LINKS_POSITIONS)
            );
        }
    }

    /**
     * Validate the 'useCombination' configuration.
     * 
     * @param mixed $value The 'useCombination' value.
     * @param array $config The configuration.
     * 
     * @throws Exception If validation fails.
     */
    private function _validateUseCombination(mixed $value, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_bool($value)) {
            throw new Exception(
                "'Collection' field '$identifier' configuration 'appearance['useCombination']' must be of type boolean."
            );
        }

        if (!isset($config['foreign_table'])) {
            throw new Exception(
                "'Collection' field '$identifier' configuration 'appearance['useCombination']' needs 'foreign_table' to be set " .
                "to take effect."
            );
        }

        if (
            isset($config['foreign_selector'])
            && isset($config['foreign_unique'])
            && $config['foreign_selector'] !== $config['foreign_unique']
        ) {
            throw new Exception(
                "'Collection' field '$identifier' configuration 'appearance['useCombination']' needs 'foreign_selector' and " .
                "'foreign_unique' point to the same field."
            );
        }
    }

    /**
     * Validate the 'suppressCombinationWarning' configuration.
     * 
     * @param mixed $value The 'suppressCombinationWarning' value.
     * @param array $config The configuration.
     * 
     * @throws Exception If validation fails.
     */
    private function _validateSuppressCombinationWarning(mixed $value, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_bool($value)) {
            throw new Exception(
                "'Collection' field '$identifier' configuration 'appearance['suppressCombinationWarning']' must be of type boolean."
            );
        }

        if (!isset($config['useCombination']) || $config['useCombination'] === false) {
            throw new Exception(
                "'Collection' field '$identifier' configuration 'appearance['suppressCombinationWarning']' needs 'useCombination' to be set " .
                "to take effect."
            );
        }
    }

    /**
     * Validate the 'enabledControls' configuration.
     * 
     * @param mixed $controls The 'enabledControls' value.
     * @param array $config The configuration.
     * 
     * @throws Exception If validation fails.
     */
    private function _validateEnabledControls(mixed $controls, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_array($controls)) {
            throw new Exception(
                "'Collection' field '$identifier' configuration 'appearance['enabledControls']' must be of type array."
            );
        }

        foreach ($controls as $control => $value) {
            if (!in_array($control, self::CONTROLS)) {
                throw new Exception(
                    "'Collection' field '$identifier' configuration 'appearance['enabledControls']' '$control' is no valid keyword.\n" .
                    "Valid keywords are: " . implode(', ', self::CONTROLS)
                );
            }
            if (!is_bool($value)) {
                throw new Exception(
                    "'Collection' field '$identifier' configuration 'appearance['enabledControls']['$control']' " .
                    "must be of type boolean"
                );
            }
        }
    }

    /**
     * Constructor for the CollectionContainerAppearance class.
     * 
     * @param array $config The configuration array.
     * @param array $globalConfig The global configuration.
     */
    public function __construct(array $config, array $globalConfig)
    {
        $properties = get_object_vars($this);
        foreach ($config as $configKey => $setting) {
            if (array_key_exists($configKey, $properties)) {
                switch ($configKey) {
                    case 'newRecordLinkTitle':
                        $this->_validateNewRecordLinkTitle($setting, $globalConfig);
                        $this->newRecordLinkTitle = $setting;
                        break;
                    case 'levelLinksPosition':
                        $this->_validateLevelLinksPosition($setting, $globalConfig);
                        $this->levelLinksPosition = $setting;
                        break;
                    case 'useCombination':
                        $this->_validateUseCombination($setting, $globalConfig);
                        $this->useCombination = $setting;
                        break;
                    case 'suppressCombinationWarning':
                        $this->_validateSuppressCombinationWarning($setting, $globalConfig);
                        $this->suppressCombinationWarning = $setting;
                        break;
                    case 'enabledControls':
                        $this->_validateEnabledControls($setting, $globalConfig);
                        $this->enabledControls = $setting;
                        break;

                    default:
                        $this->$configKey = $setting;
                        break;
                }

            } else {
                $identifier = $globalConfig['identifier'];
                throw new Exception(
                    "'Select' field '$identifier' configuration 'appearance' setting '$configKey' is not valid.\n" .
                    "Valid settings are: " . implode(', ', array_keys($properties))
                );
            }
        }
    }
}

/**
 * Class CollectionContainerConfig
 * 
 * Represents the configuration for a collection container.
 */
final class CollectionContainerConfig extends Config
{
    /**
     * The appearance configuration for the collection container.
     */
    protected ?CollectionContainerAppearance $appearance = NULL;

    /**
     * The maximum auto-size value.
     */
    protected int $autoSizeMax = -1;

    /**
     * Custom controls for the container.
     */
    protected array $customControls = [];

    /**
     * The fields within the container.
     */
    protected array $fields = [];

    /**
     * Filter configuration for the container.
     */
    protected array $filter = [];

    /**
     * Default sortby field for the foreign table.
     */
    protected string $foreign_default_sortby = '';

    /**
     * Foreign field for the container.
     */
    protected string $foreign_field = '';

    /**
     * Foreign label for the container.
     */
    protected string $foreign_label = '';

    /**
     * Match fields for the foreign table.
     */
    protected array $foreign_match_fields = [];

    /**
     * Foreign selector field for the container.
     */
    protected string $foreign_selector = '';

    /**
     * Sortby field for the foreign table.
     */
    protected string $foreign_sortby = '';

    /**
     * Foreign table name.
     */
    protected string $foreign_table = '';

    /**
     * Foreign table field for the container.
     */
    protected string $foreign_table_field = '';

    /**
     * Foreign unique field for the container.
     */
    protected string $foreign_unique = '';

    /**
     * Identifier for the container.
     */
    protected string $identifier = '';

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
     * MM opposite field for the container.
     */
    protected string $MM_opposite_field = '';

    /**
     * Override child TCA configuration.
     */
    protected array $overrideChildTca = [];

    /**
     * Size of the container.
     */
    protected int $size = -1;

    /**
     * Symmetric field for the container.
     */
    protected string $symmetric_field = '';

    /**
     * Symmetric label for the container.
     */
    protected string $symmetric_label = '';

    /**
     * Symmetric sortby field for the container.
     */
    protected string $symmetric_sortby = '';

    /**
     * Excluded settings for the container.
     */
    const EXCLUDED_SETTINGS = [
        'fields'
    ];

    /**
     * Get the appearance configuration.
     * 
     * @return CollectionContainerAppearance|null The appearance configuration.
     */
    public function getAppearance(): ?CollectionContainerAppearance
    {
        return $this->appearance;
    }

    /**
     * Get the maximum auto-size value.
     * 
     * @return int The maximum auto-size value.
     */
    public function getAutoSizeMax(): int
    {
        return $this->autoSizeMax;
    }

    /**
     * Get the custom controls for the container.
     * 
     * @return array The custom controls.
     */
    public function getCustomControls(): array
    {
        return $this->customControls;
    }

    /**
     * Get the fields within the container.
     * 
     * @return array The fields.
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Get the filter configuration for the container.
     * 
     * @return array The filter configuration.
     */
    public function getFilter(): array
    {
        return $this->filter;
    }

    /**
     * Get the default sortby field for the foreign table.
     * 
     * @return string The default sortby field.
     */
    public function getForeignDefaultSortby(): string
    {
        return $this->foreign_default_sortby;
    }

    /**
     * Get the foreign field for the container.
     * 
     * @return string The foreign field.
     */
    public function getForeignField(): string
    {
        return $this->foreign_field;
    }

    /**
     * Get the foreign label for the container.
     * 
     * @return string The foreign label.
     */
    public function getForeignLabel(): string
    {
        return $this->foreign_label;
    }

    /**
     * Get the foreign match fields for the container.
     * 
     * @return array The foreign match fields.
     */
    public function getForeignMatchFields(): array
    {
        return $this->foreign_match_fields;
    }

    /**
     * Get the foreign selector field for the container.
     * 
     * @return string The foreign selector field.
     */
    public function getForeignSelector(): string
    {
        return $this->foreign_selector;
    }

    /**
     * Get the foreign sortby field for the container.
     * 
     * @return string The foreign sortby field.
     */
    public function getForeignSortby(): string
    {
        return $this->foreign_sortby;
    }

    /**
     * Get the foreign table name for the container.
     * 
     * @return string The foreign table name.
     */
    public function getForeignTable(): string
    {
        return $this->foreign_table;
    }

    /**
     * Get the foreign table field for the container.
     * 
     * @return string The foreign table field.
     */
    public function getForeignTableField(): string
    {
        return $this->foreign_table_field;
    }

    /**
     * Get the foreign unique field for the container.
     * 
     * @return string The foreign unique field.
     */
    public function getForeignUnique(): string
    {
        return $this->foreign_unique;
    }

    /**
     * Get the identifier for the container.
     * 
     * @return string The identifier.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
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
     * Get the MM (Match Multiple) configuration.
     * 
     * @return string The MM configuration.
     */
    public function getMM(): string
    {
        return $this->MM;
    }

    /**
     * Get the MM opposite field for the container.
     * 
     * @return string The MM opposite field.
     */
    public function getMMOppositeField(): string
    {
        return $this->MM_opposite_field;
    }

    /**
     * Get the override child TCA configuration.
     * 
     * @return array The override child TCA configuration.
     */
    public function getOverrideChildTca(): array
    {
        return $this->overrideChildTca;
    }

    /**
     * Get the size of the container.
     * 
     * @return int The size.
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Get the symmetric field for the container.
     * 
     * @return string The symmetric field.
     */
    public function getSymmetricField(): string
    {
        return $this->symmetric_field;
    }

    /**
     * Get the symmetric label for the container.
     * 
     * @return string The symmetric label.
     */
    public function getSymmetricLabel(): string
    {
        return $this->symmetric_label;
    }

    /**
     * Get the symmetric sortby field for the container.
     * 
     * @return string The symmetric sortby field.
     */
    public function getSymmetricSortby(): string
    {
        return $this->symmetric_sortby;
    }

    /**
     * Set the appearance configuration for the container.
     * 
     * @param CollectionContainerAppearance|null $appearance The appearance configuration.
     */
    public function setAppearance(?CollectionContainerAppearance $appearance): void
    {
        $this->appearance = $appearance;
    }

    /**
     * Set the maximum auto-size value.
     * 
     * @param int $autoSizeMax The maximum auto-size value.
     */
    public function setAutoSizeMax(int $autoSizeMax): void
    {
        $this->autoSizeMax = $autoSizeMax;
    }

    /**
     * Set the custom controls for the container.
     * 
     * @param array $customControls The custom controls.
     */
    public function setCustomControls(array $customControls): void
    {
        $this->customControls = $customControls;
    }

    /**
     * Set the fields within the container.
     * 
     * @param array $fields The fields.
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    /**
     * Set the filter configuration for the container.
     * 
     * @param array $filter The filter configuration.
     */
    public function setFilter(array $filter): void
    {
        $this->filter = $filter;
    }

    /**
     * Set the default sortby field for the foreign table.
     * 
     * @param string $foreign_default_sortby The default sortby field.
     */
    public function setForeignDefaultSortby(string $foreign_default_sortby): void
    {
        $this->foreign_default_sortby = $foreign_default_sortby;
    }

    /**
     * Set the foreign field for the container.
     * 
     * @param string $foreign_field The foreign field.
     */
    public function setForeignField(string $foreign_field): void
    {
        $this->foreign_field = $foreign_field;
    }

    /**
     * Set the foreign label for the container.
     * 
     * @param string $foreign_label The foreign label.
     */
    public function setForeignLabel(string $foreign_label): void
    {
        $this->foreign_label = $foreign_label;
    }

    /**
     * Set the foreign match fields for the container.
     * 
     * @param array $foreign_match_fields The foreign match fields.
     */
    public function setForeignMatchFields(array $foreign_match_fields): void
    {
        $this->foreign_match_fields = $foreign_match_fields;
    }

    /**
     * Set the foreign selector field for the container.
     * 
     * @param string $foreign_selector The foreign selector field.
     */
    public function setForeignSelector(string $foreign_selector): void
    {
        $this->foreign_selector = $foreign_selector;
    }

    /**
     * Set the foreign sortby field for the container.
     * 
     * @param string $foreign_sortby The foreign sortby field.
     */
    public function setForeignSortby(string $foreign_sortby): void
    {
        $this->foreign_sortby = $foreign_sortby;
    }

    /**
     * Set the foreign table name for the container.
     * 
     * @param string $foreign_table The foreign table name.
     */
    public function setForeignTable(string $foreign_table): void
    {
        $this->foreign_table = $foreign_table;
    }

    /**
     * Set the foreign table field for the container.
     * 
     * @param string $foreign_table_field The foreign table field.
     */
    public function setForeignTableField(string $foreign_table_field): void
    {
        $this->foreign_table_field = $foreign_table_field;
    }

    /**
     * Set the foreign unique field for the container.
     * 
     * @param string $foreign_unique The foreign unique field.
     */
    public function setForeignUnique(string $foreign_unique): void
    {
        $this->foreign_unique = $foreign_unique;
    }

    /**
     * Set the identifier for the container.
     * 
     * @param string $identifier The identifier.
     */
    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
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
     * Set the MM (Match Multiple) configuration.
     * 
     * @param string $MM The MM configuration.
     */
    public function setMM(string $MM): void
    {
        $this->MM = $MM;
    }

    /**
     * Set the MM opposite field for the container.
     * 
     * @param string $MM_opposite_field The MM opposite field.
     */
    public function setMMOppositeField(string $MM_opposite_field): void
    {
        $this->MM_opposite_field = $MM_opposite_field;
    }

    /**
     * Set the override child TCA configuration.
     * 
     * @param array $overrideChildTca The override child TCA configuration.
     */
    public function setOverrideChildTca(array $overrideChildTca): void
    {
        $this->overrideChildTca = $overrideChildTca;
    }

    /**
     * Set the size of the container.
     * 
     * @param int $size The size.
     */
    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    /**
     * Set the symmetric field for the container.
     * 
     * @param string $symmetric_field The symmetric field.
     */
    public function setSymmetricField(string $symmetric_field): void
    {
        $this->symmetric_field = $symmetric_field;
    }

    /**
     * Set the symmetric label for the container.
     * 
     * @param string $symmetric_label The symmetric label.
     */
    public function setSymmetricLabel(string $symmetric_label): void
    {
        $this->symmetric_label = $symmetric_label;
    }

    /**
     * Set the symmetric sortby field for the container.
     * 
     * @param string $symmetric_sortby The symmetric sortby field.
     */
    public function setSymmetricSortby(string $symmetric_sortby): void
    {
        $this->symmetric_sortby = $symmetric_sortby;
    }

    /**
     * Merge the configuration from another CollectionContainerConfig instance.
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

        if ($foreign->getAppearance() !== NULL) {
            if ($this->appearance !== NULL) {
                $this->appearance->mergeConfig($foreign->getAppearance());
            } else {
                $this->appearance = $foreign->getAppearance();
            }
        }
        if ($foreign->getAutoSizeMax() !== -1) {
            $this->autoSizeMax = $foreign->getAutoSizeMax();
        }
        if (!empty($foreign->getCustomControls())) {
            $this->customControls = $foreign->getCustomControls();
        }
        if (!empty($foreign->getFields())) {
            $this->fields = $foreign->getFields();
        }
        if (!empty($foreign->getFilter())) {
            $this->filter = $foreign->getFilter();
        }
        if ($foreign->getForeignDefaultSortby() !== '') {
            $this->foreign_default_sortby = $foreign->getForeignDefaultSortby();
        }
        if ($foreign->getForeignField() !== '') {
            $this->foreign_field = $foreign->getForeignField();
        }
        if ($foreign->getForeignLabel() !== '') {
            $this->foreign_label = $foreign->getForeignLabel();
        }
        if (!empty($foreign->getForeignMatchFields())) {
            $this->foreign_match_fields = $foreign->getForeignMatchFields();
        }
        if ($foreign->getForeignSelector() !== '') {
            $this->foreign_selector = $foreign->getForeignSelector();
        }
        if ($foreign->getForeignSortby() !== '') {
            $this->foreign_sortby = $foreign->getForeignSortby();
        }
        if ($foreign->getForeignTable() !== '') {
            $this->foreign_table = $foreign->getForeignTable();
        }
        if ($foreign->getForeignTableField() !== '') {
            $this->foreign_table_field = $foreign->getForeignTableField();
        }
        if ($foreign->getForeignUnique() !== '') {
            $this->foreign_unique = $foreign->getForeignUnique();
        }
        if ($foreign->getIdentifier() !== '') {
            $this->identifier = $foreign->getIdentifier();
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
        if ($foreign->getMMOppositeField() !== '') {
            $this->MM_opposite_field = $foreign->getMMOppositeField();
        }
        if (!empty($foreign->getOverrideChildTca())) {
            $this->overrideChildTca = $foreign->getOverrideChildTca();
        }
        if ($foreign->getSize() !== -1) {
            $this->size = $foreign->getSize();
        }
        if ($foreign->getSymmetricField() !== '') {
            $this->symmetric_field = $foreign->getSymmetricField();
        }
        if ($foreign->getSymmetricLabel() !== '') {
            $this->symmetric_label = $foreign->getSymmetricLabel();
        }
        if ($foreign->getSymmetricSortby() !== '') {
            $this->symmetric_sortby = $foreign->getSymmetricSortby();
        }
    }

    /**
     * Validate the 'foreign_default_sortby' configuration.
     * 
     * @param mixed $value The 'foreign_default_sortby' value.
     * @param array $config The configuration.
     * 
     * @throws Exception If validation fails.
     */
    private function _validateForeignDefaultSortby(mixed $value, array $config): void
    {
        if (!is_string($value)) {
            throw new Exception(
                "'Collection' field '$this->identifier' configuration 'foreign_default_sortby' must be of type string."
            );
        }

        if (isset($config['foreign_sortby'])) {
            throw new Exception(
                "'Collection' field '$this->identifier' configuration 'foreign_default_sortby' has no effect when " .
                "'foreign_sortby' is set."
            );
        }
    }

    /**
     * Validate the 'customControls' configuration.
     * 
     * @param mixed $controls The 'customControls' value.
     * @param array $config The configuration.
     * 
     * @throws Exception If validation fails.
     */
    private function _validateCustomControls(mixed $controls, array $config): void
    {
        if (!is_array($controls)) {
            throw new Exception(
                "'Collection' field '$this->identifier' configuration 'appearance['customControls']' must be of type array."
            );
        }

        $i = 0;
        foreach ($controls as $element) {
            if (count($element) !== 1) {
                throw new Exception(
                    "'Collection' field '$this->identifier' configuration 'appearance['customControls'][$i]' must be of type array " .
                    "with exactly one entry.\nFix; customControls:\n  - userFunc: 'class1->method1'\n  - userFunc: 'class2->method2'"
                );
            }
            if (!isset($element['userFunc'])) {
                throw new Exception(
                    "'Collection' field '$this->identifier' configuration 'appearance['customControls'][$i]' must be of type array " .
                    "with exactly one entry 'userFunc' each.\nFix; customControls:\n  - userFunc: 'class1->method1'\n  " .
                    "- userFunc: 'class2->method2'"
                );
            }
            $i++;
        }
    }

    /**
     * Validate a foreign configuration setting.
     * 
     * @param mixed $value The value to validate.
     * @param array $config The configuration.
     * @param string $setting The specific setting being validated.
     * 
     * @throws Exception If validation fails.
     */
    private function _validateForeign(mixed $value, array $config, string $setting): void
    {
        if (!is_string($value)) {
            throw new Exception(
                "'Collection' field '$this->identifier' configuration '$setting' must be of type string."
            );
        }

        if (!isset($config['foreign_table'])) {
            throw new Exception(
                "'Collection' field '$this->identifier' configuration '$setting' needs 'foreign_table' to be set " .
                "to take effect."
            );
        }

        $this->validateField($value, $config, $setting, 'Collection', [$config['foreign_table']]);
    }

    /**
     * Validate the 'foreign_match_fields' configuration.
     * 
     * @param mixed $matchFields The 'foreign_match_fields' value.
     * @param array $config The configuration.
     * 
     * @throws Exception If validation fails.
     */
    private function _validateForeignMatchFields(mixed $matchFields, array $config): void
    {
        if (!is_array($matchFields)) {
            throw new Exception(
                "'Collection' field '$this->identifier' configuration 'foreign_match_fields' must be of type array."
            );
        }

        if (!isset($config['foreign_table'])) {
            throw new Exception(
                "'Collection' field '$this->identifier' configuration 'foreign_match_fields' needs 'foreign_table' to be set " .
                "to take effect."
            );
        }

        $i = 0;
        foreach ($matchFields as $key => $value) {
            if (!is_string($key)) {
                throw new Exception(
                    "'Collection' field '$this->identifier' configuration 'foreign_match_fields[$i]' key '$key' must be of type string."
                );
            }

            if (!is_string($value)) {
                throw new Exception(
                    "'Collection' field '$this->identifier' configuration 'foreign_match_fields[$i]' value '$value' must be of type string."
                );
            }
            $this->validateField($key, $config, "foreign_match_fields['$key']", 'Collection', [$config['foreign_table']]);
            $i++;
        }
    }

    /**
     * Validate and set the 'appearance' configuration.
     * 
     * @param mixed $appearance The 'appearance' value.
     * @param array $config The configuration.
     * 
     * @throws Exception If validation fails.
     */
    private function _validateAndSetAppearance(mixed $appearance, array $config): void
    {
        if (!is_array($appearance)) {
            throw new Exception(
                "'Collection' field '$this->identifier' configuration 'appearance' must be of type array."
            );
        }

        $this->appearance = new CollectionContainerAppearance($appearance, $config);
    }

    /**
     * Convert an array configuration to the object's properties.
     * 
     * @param array $config The configuration array.
     * @param array $fieldProperties Additional field properties.
     * @param string $table The table name.
     * 
     * @throws Exception If a required property is missing or if validation fails.
     */
    public function arrayToConfig(array $config, array $fieldProperties, ?array $globalConf = NULL, mixed $misc = NULL): void
    {
        $globalConf = $globalConf ?? $config;
        $this->checkRequirements($globalConf, ['identifier'], 'Collection');
        $this->identifier = $globalConf['identifier'];
        $properties = get_object_vars($this);
        foreach ($config as $configKey => $value) {
            if ($this->isValidConfig($properties, $configKey)) {
                if ($GLOBALS['CbBuilder']['config']['Strict'] === true) {
                    switch ($configKey) {
                        case 'appearance':
                            $this->_validateAndSetAppearance($value, $globalConf);
                            break;
                        case 'autoSizeMax':
                            $this->validateAutoSizeMax($value, $globalConf, 'Collection');
                            $this->autoSizeMax = $value;
                            break;
                        case 'customControls':
                            $this->_validateCustomControls($value, $globalConf);
                            $this->customControls = $value;
                            break;
                        case 'filter':
                            $this->validateFilter($value, $globalConf, 'Collection');
                            $this->filter = $value;
                            break;
                        case 'foreign_default_sortby':
                            $this->_validateForeignDefaultSortby($value, $globalConf);
                            $this->foreign_default_sortby = $value;
                            break;
                        case 'foreign_field':
                            $this->_validateForeign($value, $globalConf, 'foreign_field');
                            $this->foreign_field = $value;
                            break;
                        case 'foreign_match_fields':
                            $this->_validateForeignMatchFields($value, $globalConf);
                            $this->foreign_match_fields = $value;
                            break;
                        case 'foreign_selector':
                            $this->_validateForeign($value, $globalConf, 'foreign_selector');
                            $this->foreign_selector = $value;
                            break;
                        case 'foreign_sortby':
                            $this->_validateForeign($value, $globalConf, 'foreign_sortby');
                            $this->foreign_sortby = $value;
                            break;
                        case 'foreign_table':
                            $this->validateTable($value, $globalConf, 'foreign_table', 'Collection');
                            $this->foreign_table = $value;
                            break;
                        case 'foreign_table_field':
                            $this->_validateForeign($value, $globalConf, 'foreign_table_field');
                            $this->foreign_table_field = $value;
                            break;
                        case 'foreign_unique':
                            $this->_validateForeign($value, $globalConf, 'foreign_unique');
                            $this->foreign_unique = $value;
                            break;
                        case 'maxitems':
                            $this->validateInteger($value, $globalConf, 'maxitems', 'Collection', 1, PHP_INT_MAX, true, true, 'minitems');
                            $this->maxitems = intval($value);
                            break;
                        case 'minitems':
                            $this->validateInteger($value, $globalConf, 'minitems', 'Collection', 1, PHP_INT_MAX, true, false, 'maxitems');
                            $this->minitems = intval($value);
                            break;
                        case 'MM':
                            $this->validateTable($value, $globalConf, 'MM', 'Collection');
                            $this->MM = $value;
                            break;
                        case 'MM_opposite_field':
                            $this->validateMmOppositeField($value, $globalConf, 'Collection');
                            $this->MM_opposite_field = $value;
                            break;
                        case 'size':
                            if (!isset($config['foreign_selector'])) {
                                throw new Exception(
                                    "'Collection' field '$this->identifier' configuration 'size' needs 'foreign_selector' " .
                                    "to be set to take effect."
                                );
                            }
                            $this->validateInteger($value, $globalConf, 'size', 'Collection', 1, PHP_INT_MAX, true, false, 'autoSizeMax');
                            $this->size = intval($value);
                            break;
                        case 'symmetric_field':
                            $this->_validateForeign($value, $globalConf, 'symmetric_field');
                            $this->symmetric_field = $value;
                            break;
                        case 'symmetric_sortby':
                            $this->_validateForeign($value, $globalConf, 'symmetric_sortby');
                            $this->symmetric_sortby = $value;
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
                    "'Collection' field '$identifier' configuration setting '$configKey' is not valid.\n" .
                    "Valid settings are: " . implode(', ', array_keys($properties))
                );
            }
        }
        if ($this->foreign_table === '') {
            if (is_string($misc)) {
                $this->foreign_table = $misc;
            }
        }

        if ($this->foreign_field === '') {
            if (is_string($misc)) {
                $this->foreign_field = $misc;
            }
        }
    }
}

/**
 * Class CollectionContainer
 * 
 * Represents a collection container field.
 */
final class CollectionContainer extends Field
{
    /**
     * The configuration for this collection container.
     */
    protected CollectionContainerConfig $config;

    /**
     * The parent table for the collection.
     */
    protected ?CollectionTable $parentTable = NULL;

    /**
     * The child table name.
     */
    protected string $childTable = '';

    /**
     * Get the configuration of this collection container.
     * 
     * @return CollectionContainerConfig The configuration.
     */
    public function getConfig(): CollectionContainerConfig
    {
        return $this->config;
    }

    /**
     * Get the parent table for the collection.
     * 
     * @return CollectionTable The parent table.
     */
    public function getParentTable(): CollectionTable
    {
        return $this->parentTable;
    }

    /**
     * Convert an array representation of a field into the object's properties.
     * 
     * @param array $field The field array.
     */
    private function _arrayToField(array $field): void
    {
        $config = new CollectionContainerConfig();
        $config->arrayToConfig($field, array_keys(get_object_vars($this)), NULL, $this->childTable);
        $this->config = $config;
        $this->__arrayToField('inline', $field);
    }

    /**
     * Merge the configuration of another collection container into this one.
     * 
     * @param self $foreign The field to merge.
     */
    public function mergeField(self $foreign): void
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
        $excludes = array_merge(['childTable'], CollectionContainerConfig::EXCLUDED_SETTINGS);
        return $this->__parseField($this->config, $mode, $level, $excludes);
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
     * Constructor for the CollectionContainer class.
     * 
     * @param array $field The field configuration.
     * @param string $table The table name.
     */
    public function __construct(array $field, string $table)
    {
        $this->table = $table;
        $this->childTable = $field['identifier'];
        $this->_arrayToField($field);
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
}