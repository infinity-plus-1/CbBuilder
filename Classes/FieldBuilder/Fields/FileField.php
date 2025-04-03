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
 * Configuration class for file field appearance.
 */
final class FileFieldAppearanceConfig extends Config
{
    /**
     * Whether to collapse all sections by default.
     */
    protected ?bool $collapseAll = null;

    /**
     * Whether to expand a single section by default.
     */
    protected ?bool $expandSingle = null;

    /**
     * Title for the link to create a new relation.
     */
    protected string $createNewRelationLinkTitle = '';

    /**
     * Title for the link to add media.
     */
    protected string $addMediaLinkTitle = '';

    /**
     * Title for the link to upload files.
     */
    protected string $uploadFilesLinkTitle = '';

    /**
     * Whether to use sortable functionality.
     */
    protected ?bool $useSortable = null;

    /**
     * Whether to show possible localization records.
     */
    protected ?bool $showPossibleLocalizationRecords = null;

    /**
     * Whether to show the link for all localizations.
     */
    protected ?bool $showAllLocalizationLink = null;

    /**
     * Whether to show the synchronization link.
     */
    protected ?bool $showSynchronizationLink = null;

    /**
     * Array of enabled controls.
     */
    protected array $enabledControls = [];

    /**
     * Array of header thumbnail settings.
     */
    protected array $headerThumbnail = [];

    /**
     * Whether file upload is allowed.
     */
    protected ?bool $fileUploadAllowed = null;

    /**
     * Whether file upload by URL is allowed.
     */
    protected ?bool $fileByUrlAllowed = null;

    /**
     * Whether the element browser is enabled.
     */
    protected ?bool $elementBrowserEnabled = null;

    /**
     * Get whether all sections should be collapsed by default.
     *
     * @return bool|null Whether to collapse all sections.
     */
    public function isCollapseAll(): ?bool
    {
        return $this->collapseAll;
    }

    /**
     * Get whether a single section should be expanded by default.
     *
     * @return bool|null Whether to expand a single section.
     */
    public function isExpandSingle(): ?bool
    {
        return $this->expandSingle;
    }

    /**
     * Get the title for the link to create a new relation.
     *
     * @return string The title for creating a new relation link.
     */
    public function getCreateNewRelationLinkTitle(): string
    {
        return $this->createNewRelationLinkTitle;
    }

    /**
     * Get the title for the link to add media.
     *
     * @return string The title for adding media link.
     */
    public function getAddMediaLinkTitle(): string
    {
        return $this->addMediaLinkTitle;
    }

    /**
     * Get the title for the link to upload files.
     *
     * @return string The title for uploading files link.
     */
    public function getUploadFilesLinkTitle(): string
    {
        return $this->uploadFilesLinkTitle;
    }

    /**
     * Get whether sortable functionality is enabled.
     *
     * @return bool|null Whether sortable functionality is enabled.
     */
    public function isUseSortable(): ?bool
    {
        return $this->useSortable;
    }

    /**
     * Get whether possible localization records should be shown.
     *
     * @return bool|null Whether to show possible localization records.
     */
    public function isShowPossibleLocalizationRecords(): ?bool
    {
        return $this->showPossibleLocalizationRecords;
    }

    /**
     * Get whether the link for all localizations should be shown.
     *
     * @return bool|null Whether to show the link for all localizations.
     */
    public function isShowAllLocalizationLink(): ?bool
    {
        return $this->showAllLocalizationLink;
    }

    /**
     * Get whether the synchronization link should be shown.
     *
     * @return bool|null Whether to show the synchronization link.
     */
    public function isShowSynchronizationLink(): ?bool
    {
        return $this->showSynchronizationLink;
    }

    /**
     * Get the array of enabled controls.
     *
     * @return array The list of enabled controls.
     */
    public function getEnabledControls(): array
    {
        return $this->enabledControls;
    }

    /**
     * Get the array of header thumbnail settings.
     *
     * @return array The settings for the header thumbnail.
     */
    public function getHeaderThumbnail(): array
    {
        return $this->headerThumbnail;
    }

    /**
     * Get whether file upload is allowed.
     *
     * @return bool|null Whether file upload is allowed.
     */
    public function isFileUploadAllowed(): ?bool
    {
        return $this->fileUploadAllowed;
    }

    /**
     * Get whether file upload by URL is allowed.
     *
     * @return bool|null Whether file upload by URL is allowed.
     */
    public function isFileByUrlAllowed(): ?bool
    {
        return $this->fileByUrlAllowed;
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
     * Set whether to collapse all sections by default.
     *
     * @param bool|null $collapseAll Whether to collapse all sections.
     */
    public function setCollapseAll(?bool $collapseAll): void
    {
        $this->collapseAll = $collapseAll;
    }

    /**
     * Set whether to expand a single section by default.
     *
     * @param bool|null $expandSingle Whether to expand a single section.
     */
    public function setExpandSingle(?bool $expandSingle): void
    {
        $this->expandSingle = $expandSingle;
    }

    /**
     * Set the title for the link to create a new relation.
     *
     * @param string $title The title for creating a new relation link.
     */
    public function setCreateNewRelationLinkTitle(string $title): void
    {
        $this->createNewRelationLinkTitle = $title;
    }

    /**
     * Set the title for the link to add media.
     *
     * @param string $title The title for adding media link.
     */
    public function setAddMediaLinkTitle(string $title): void
    {
        $this->addMediaLinkTitle = $title;
    }

    /**
     * Set the title for the link to upload files.
     *
     * @param string $title The title for uploading files link.
     */
    public function setUploadFilesLinkTitle(string $title): void
    {
        $this->uploadFilesLinkTitle = $title;
    }

    /**
     * Set whether to use sortable functionality.
     *
     * @param bool|null $useSortable Whether to use sortable functionality.
     */
    public function setUseSortable(?bool $useSortable): void
    {
        $this->useSortable = $useSortable;
    }

    /**
     * Set whether to show possible localization records.
     *
     * @param bool|null $show Whether to show possible localization records.
     */
    public function setShowPossibleLocalizationRecords(?bool $show): void
    {
        $this->showPossibleLocalizationRecords = $show;
    }

    /**
     * Set whether to show the link for all localizations.
     *
     * @param bool|null $show Whether to show the link for all localizations.
     */
    public function setShowAllLocalizationLink(?bool $show): void
    {
        $this->showAllLocalizationLink = $show;
    }

    /**
     * Set whether to show the synchronization link.
     *
     * @param bool|null $show Whether to show the synchronization link.
     */
    public function setShowSynchronizationLink(?bool $show): void
    {
        $this->showSynchronizationLink = $show;
    }

    /**
     * Set the array of enabled controls.
     *
     * @param array $controls The list of enabled controls.
     */
    public function setEnabledControls(array $controls): void
    {
        $this->enabledControls = $controls;
    }

    /**
     * Set the array of header thumbnail settings.
     *
     * @param array $thumbnail The settings for the header thumbnail.
     */
    public function setHeaderThumbnail(array $thumbnail): void
    {
        $this->headerThumbnail = $thumbnail;
    }

    /**
     * Set whether file upload is allowed.
     *
     * @param bool|null $allowed Whether file upload is allowed.
     */
    public function setFileUploadAllowed(?bool $allowed): void
    {
        $this->fileUploadAllowed = $allowed;
    }

    /**
     * Set whether file upload by URL is allowed.
     *
     * @param bool|null $allowed Whether file upload by URL is allowed.
     */
    public function setFileByUrlAllowed(?bool $allowed): void
    {
        $this->fileByUrlAllowed = $allowed;
    }

    /**
     * Set whether the element browser is enabled.
     *
     * @param bool|null $enabled Whether the element browser is enabled.
     */
    public function setElementBrowserEnabled(?bool $enabled): void
    {
        $this->elementBrowserEnabled = $enabled;
    }

    /**
     * Merge configuration from another instance.
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

        // Merge boolean properties only if they are explicitly set in the foreign config.
        if ($foreign->isCollapseAll() !== null) {
            $this->collapseAll = $foreign->isCollapseAll();
        }

        if ($foreign->isExpandSingle() !== null) {
            $this->expandSingle = $foreign->isExpandSingle();
        }

        // Merge string properties only if they are not empty in the foreign config.
        if ($foreign->getCreateNewRelationLinkTitle() !== '') {
            $this->createNewRelationLinkTitle = $foreign->getCreateNewRelationLinkTitle();
        }

        if ($foreign->getAddMediaLinkTitle() !== '') {
            $this->addMediaLinkTitle = $foreign->getAddMediaLinkTitle();
        }

        if ($foreign->getUploadFilesLinkTitle() !== '') {
            $this->uploadFilesLinkTitle = $foreign->getUploadFilesLinkTitle();
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

        // Merge array properties only if they are not empty in the foreign config.
        if (!empty($foreign->getEnabledControls())) {
            $this->enabledControls = $foreign->getEnabledControls();
        }

        if (!empty($foreign->getHeaderThumbnail())) {
            $this->headerThumbnail = $foreign->getHeaderThumbnail();
        }

        if ($foreign->isFileUploadAllowed() !== null) {
            $this->fileUploadAllowed = $foreign->isFileUploadAllowed();
        }

        if ($foreign->isFileByUrlAllowed() !== null) {
            $this->fileByUrlAllowed = $foreign->isFileByUrlAllowed();
        }

        if ($foreign->isElementBrowserEnabled() !== null) {
            $this->elementBrowserEnabled = $foreign->isElementBrowserEnabled();
        }
    }

    /**
     * List of available controls for file fields.
     */
    const ENABLED_CONTROLS = [
        'info', 'new', 'dragdrop', 'sort', 'hide', 'delete', 'localize'
    ];

    /**
     * List of available header thumbnail settings.
     */
    const HEADER_THUMBNAILS = [
        'field', 'width', 'height'
    ];

    private $headerThumbnailFunctions;

    /**
     * Constructor to initialize header thumbnail validation functions.
     */
    public function __construct()
    {
        $this->headerThumbnailFunctions = [
            'field' => function ($value, $identifier) {
                if (!is_string($value)) {
                    throw new Exception(
                        "'File' field '$identifier' configuration 'appearance['headerThumbnail']['field']' value must be of type string."
                    );
                }
            },
            'width' => function ($value, $identifier, $config) {
                self::_validateThumbnailWidthOrHeight($value, $identifier, $config);
            },
            'height' => function ($value, $identifier, $config) {
                self::_validateThumbnailWidthOrHeight($value, $identifier, $config);
            }
        ];
    }

    /**
     * Validates the thumbnail width or height value.
     *
     * @param mixed  $value      The value to validate.
     * @param string $identifier The identifier of the file field.
     * @param string $config     The configuration key being validated.
     *
     * @throws Exception If the value is not a string or integer, or if it does not represent an integer with optional 'c' for cropping.
     */
    private function _validateThumbnailWidthOrHeight($value, $identifier, $config): void
    {
        if (!is_string($value) && !is_int($value)) {
            throw new Exception(
                "'File' field '$identifier' configuration 'appearance['headerThumbnail']['$config']' value must be either of type string or type integer."
            );
        }
        if (is_string($value)) {
            $match = [];
            preg_match("/\\d*c?/", $value, $match);
            if ($match[0] !== $value) {
                throw new Exception(
                    "'File' field '$identifier' configuration 'appearance['headerThumbnail']['$config']' value must be an integer or a string that represents an integer. A 'c' can be appended if the element should be cropped. Fix: appearance['headerThumbnail']['$config'] => '100c'"
                );
            }
        }
    }

    /**
     * Validates whether the element browser is enabled for a specific configuration.
     *
     * @param mixed  $entry  The value to validate.
     * @param array  $config The configuration array.
     * @param string $_config The specific configuration key being validated.
     *
     * @throws Exception If the value is not a string or if the element browser is not enabled.
     */
    private function _validateIsElementBrowserEnabled($entry, $config, string $_config): void
    {
        $identifier = $config['identifier'];
        if (!is_string($entry)) {
            throw new Exception(
                "'File' field '$identifier' configuration '$_config' must be of type string."
            );
        }

        if (isset($config['appearance']['elementBrowserEnabled']) && $config['appearance']['elementBrowserEnabled'] === false) {
            throw new Exception(
                "'File' field '$identifier' configuration '$_config' takes effect only if ['config']['appearance']['elementBrowserEnabled'] is true. Fix: Set ['config']['appearance']['elementBrowserEnabled'] to true."
            );
        }
    }

    /**
     * Validates the enabled controls configuration.
     *
     * @param mixed  $entry  The value to validate.
     * @param array  $config The configuration array.
     *
     * @throws Exception If the value is not an array or if it contains invalid keys or non-boolean values.
     */
    private function _validateEnabledControls($entry, $config): void
    {
        $identifier = $config['identifier'];
        if (!is_array($entry)) {
            throw new Exception(
                "'File' field '$identifier' configuration 'enabledControls' must be of type array."
            );
        }

        $i = 0;
        foreach ($entry as $key => $value) {
            if (!in_array($key, self::ENABLED_CONTROLS)) {
                throw new Exception(
                    "'File' field '$identifier' configuration 'appearance['enabledControls'][$i]' key must be one of the following keywords: " . implode(', ', self::ENABLED_CONTROLS)
                );
            }
            if (!is_bool($value)) {
                throw new Exception(
                    "'File' field '$identifier' configuration 'appearance['enabledControls'][$i]' value must be of type boolean."
                );
            }
            $i++;
        }
    }

    /**
     * Validates the header thumbnail configuration.
     *
     * @param mixed  $entry  The value to validate.
     * @param array  $config The configuration array.
     *
     * @throws Exception If the value is not an array or if it contains invalid keys.
     */
    private function _validateHeaderThumbnail($entry, $config): void
    {
        $identifier = $config['identifier'];
        if (!is_array($entry)) {
            throw new Exception(
                "'File' field '$identifier' configuration 'headerThumbnail' must be of type array."
            );
        }

        foreach ($entry as $key => $value) {
            if (!in_array($key, self::HEADER_THUMBNAILS)) {
                throw new Exception(
                    "'File' field '$identifier' configuration 'appearance['headerThumbnail'][$key]' key must be one of the following keywords: " . implode(', ', self::HEADER_THUMBNAILS)
                );
            }
            call_user_func($this->headerThumbnailFunctions[$key], $value, $identifier, $key);
        }
    }

    /**
     * Converts an array configuration into the object's properties.
     *
     * @param array $config The configuration array.
     * @param array $_config Additional configuration data.
     */
    public function arrayToConfig(array $config, array $fieldProperties, ?array $globalConf = NULL, mixed $misc = NULL): void
    {
        $this->checkRequirements($config, [], 'Checkbox');
        $properties = get_object_vars($this);
        foreach ($config as $configKey => $value) {
            if ($this->isValidConfig($properties, $configKey)) {
                switch ($configKey) {
                    case 'enabledControls':
                        $this->_validateEnabledControls($value, $globalConf);
                        $this->enabledControls = $value;
                        break;
                    case 'headerThumbnail':
                        $this->_validateHeaderThumbnail($value, $globalConf);
                        $this->headerThumbnail = $value;
                        break;
                    case 'createNewRelationLinkTitle':
                        $this->_validateIsElementBrowserEnabled($value, $globalConf, 'createNewRelationLinkTitle');
                        $this->createNewRelationLinkTitle = $value;
                        break;
                    case 'addMediaLinkTitle':
                        $this->_validateIsElementBrowserEnabled($value, $globalConf, 'addMediaLinkTitle');
                        $this->addMediaLinkTitle = $value;
                        break;
                    case 'uploadFilesLinkTitle':
                        $this->_validateIsElementBrowserEnabled($value, $globalConf, 'uploadFilesLinkTitle');
                        $this->uploadFilesLinkTitle = $value;
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
 * Configuration class for file fields.
 */
final class FileFieldConfig extends Config
{
    /**
     * Comma-separated list or array of allowed file types or extensions.
     */
    protected string|array $allowed = '';

    /**
     * Appearance configuration for the file field.
     */
    protected ?FileFieldAppearanceConfig $appearance = null;

    /**
     * Comma-separated list or array of disallowed file types or extensions.
     */
    protected string|array $disallowed = '';

    /**
     * Maximum number of items allowed. -1 means no limit.
     */
    protected int $maxitems = -1;

    /**
     * Minimum number of items required.
     */
    protected int $minitems = -1;

    /**
     * Array of TCA overrides for child records.
     */
    protected array $overrideChildTca = [];

    /**
     * Whether the field is read-only.
     */
    protected ?bool $readOnly = NULL;

    /**
     * Get the array of allowed file types or extensions.
     *
     * @return array The list of allowed file types or extensions.
     */
    public function getAllowed(): string|array
    {
        return $this->allowed;
    }

    /**
     * Get the appearance configuration for the file field.
     *
     * @return FileFieldAppearanceConfig|null The appearance configuration, or null if not set.
     */
    public function getAppearance(): ?FileFieldAppearanceConfig
    {
        return $this->appearance;
    }

    /**
     * Get the array of disallowed file types or extensions.
     *
     * @return array The list of disallowed file types or extensions.
     */
    public function getDisallowed(): string|array
    {
        return $this->disallowed;
    }

    /**
     * Get the maximum number of items allowed.
     *
     * @return int The maximum number of items, or -1 if no limit.
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
     * Get the array of TCA overrides for child records.
     *
     * @return array The list of TCA overrides.
     */
    public function getOverrideChildTca(): array
    {
        return $this->overrideChildTca;
    }

    /**
     * Check if the field is read-only.
     *
     * @return bool Whether the field is read-only.
     */
    public function isReadOnly(): ?bool
    {
        return $this->readOnly;
    }

    /**
     * Set the array of allowed file types or extensions.
     *
     * @param array $allowed The list of allowed file types or extensions.
     */
    public function setAllowed(array $allowed): void
    {
        $this->allowed = $allowed;
    }

    /**
     * Set the appearance configuration for the file field.
     *
     * @param FileFieldAppearanceConfig|null $appearance The appearance configuration, or null to unset.
     */
    public function setAppearance(?FileFieldAppearanceConfig $appearance): void
    {
        $this->appearance = $appearance;
    }

    /**
     * Set the array of disallowed file types or extensions.
     *
     * @param array $disallowed The list of disallowed file types or extensions.
     */
    public function setDisallowed(array $disallowed): void
    {
        $this->disallowed = $disallowed;
    }

    /**
     * Set the maximum number of items allowed.
     *
     * @param int $maxitems The maximum number of items, or -1 for no limit.
     */
    public function setMaxItems(int $maxitems): void
    {
        $this->maxitems = $maxitems;
    }

    /**
     * Set the minimum number of items required.
     *
     * @param int $minitems The minimum number of items.
     */
    public function setMinItems(int $minitems): void
    {
        $this->minitems = $minitems;
    }

    /**
     * Set the array of TCA overrides for child records.
     *
     * @param array $overrideChildTca The list of TCA overrides.
     */
    public function setOverrideChildTca(array $overrideChildTca): void
    {
        $this->overrideChildTca = $overrideChildTca;
    }

    /**
     * Set whether the field is read-only.
     *
     * @param bool $readOnly Whether the field should be read-only.
     */
    public function setReadOnly(bool $readOnly): void
    {
        $this->readOnly = $readOnly;
    }

    /**
     * Merge configuration from another instance.
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

        if (!empty($foreign->getAllowed())) {
            $this->allowed = $foreign->getAllowed();
        }

        if ($foreign->getAppearance() !== null) {
            if ($this->appearance !== null) {
                $this->appearance->mergeConfig($foreign->getAppearance());
            } else {
                $this->appearance = $foreign->getAppearance();
            }
        }

        if (!empty($foreign->getDisallowed())) {
            $this->disallowed = $foreign->getDisallowed();
        }

        if ($foreign->getMaxItems() >= 0) {
            $this->maxitems = $foreign->getMaxItems();
        }

        if ($foreign->getMinItems() >= 0) {
            $this->minitems = $foreign->getMinItems();
        }

        if (!empty($foreign->getOverrideChildTca())) {
            $this->overrideChildTca = $foreign->getOverrideChildTca();
        }

        if ($foreign->isReadOnly() !== null) {
            $this->readOnly = $foreign->isReadOnly();
        }
    }

    /**
     * Validates the allowed or disallowed configuration.
     *
     * @param mixed  $entry  The value to validate.
     * @param array  $config The configuration array.
     * @param string $_config The specific configuration key being validated.
     *
     * @throws Exception If the value is not an array or if it contains non-string keys or values.
     */
    private function _validateAllowedOrDisallowed(mixed $entry, $config, string $_config): void
    {
        $identifier = $config['identifier'];
        if (!is_array($entry) && !is_string($entry)) {
            throw new Exception(
                "'File' field '$identifier' configuration '$_config' value must be of type array or string. Fix\n" .
                "allowed: 'common-image-types'\nor\nallowed:\n  - 'common-image-types'\n  - 'gz'\n  - 'zip'"
            );
        }
        if (is_array($entry)) {
            $i = 0;
            foreach ($entry as $key => $value) {
                if (!is_string($key)) {
                    throw new Exception(
                        "'File' field '$identifier' configuration '$_config[$i]' key must be of type string."
                    );
                }
                if (!is_string($value)) {
                    throw new Exception(
                        "'File' field '$identifier' configuration '$_config[$i]' value must be of type string."
                    );
                }
                $i++;
            }
        }
    }

    /**
     * Validates the minimum or maximum items configuration.
     *
     * @param mixed  $entry  The value to validate.
     * @param array  $config The configuration array.
     * @param string $_config The specific configuration key being validated.
     *
     * @throws Exception If the value is not an integer or if it is not greater than zero.
     */
    private function _validateMinOrMaxItems($entry, $config, $_config): void
    {
        $identifier = $config['identifier'];
        if (!is_int($entry)) {
            throw new Exception(
                "'File' field '$identifier' configuration '$_config' value must be of type integer."
            );
        }

        if ($entry <= 0) {
            throw new Exception(
                "'File' field '$identifier' configuration '$_config' value must be greater than zero."
            );
        }
    }

    /**
     * Converts an array configuration into the object's properties.
     *
     * @param array $config The configuration array.
     * @param array $fieldProperties Additional field properties.
     */
    public function arrayToConfig(array $config, array $fieldProperties, ?array $globalConf = NULL, mixed $misc = NULL): void
    {
        $globalConf = $globalConf ?? $config;
        $this->checkRequirements($globalConf, [], 'File');
        $properties = get_object_vars($this);
        foreach ($config as $configKey => $value) {
            if ($this->isValidConfig($properties, $configKey)) {
                if ($GLOBALS['CbBuilder']['config']['Strict'] === true) {
                    switch ($configKey) {
                        case 'appearance':
                            $this->appearance = new FileFieldAppearanceConfig();
                            $this->appearance->arrayToConfig($value, $fieldProperties, $globalConf);
                            break;
                        case 'disallowed':
                            $this->_validateAllowedOrDisallowed($value, $globalConf, 'disallowed');
                            $this->disallowed = $value;
                            break;
                        case 'allowed':
                            $this->_validateAllowedOrDisallowed($value, $globalConf, 'allowed');
                            $this->allowed = $value;
                            break;
                        case 'maxitems':
                            $this->_validateMinOrMaxItems($value, $globalConf, 'maxitems');
                            $this->maxitems = $value;
                            break;
                        case 'minitems':
                            $this->_validateMinOrMaxItems($value, $globalConf, 'minitems');
                            $this->minitems = $value;
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
                    "'File' field '$identifier' configuration '$configKey' is not valid.\n" .
                    "Valid settings are: " . implode(', ', array_keys($properties))
                );
            }
        }
    }
}

/**
 * Represents a file field in the builder.
 */
final class FileField extends Field
{
    /**
     * Configuration for the file field.
     */
    protected FileFieldConfig $config;

    /**
     * Get the configuration for the file field.
     *
     * @return FileFieldConfig The configuration object.
     */
    public function getConfig(): FileFieldConfig
    {
        return $this->config;
    }

    /**
     * Initializes the field from an array configuration.
     *
     * @param array $field The array configuration for the field.
     */
    private function _arrayToField(array $field): void
    {
        $this->__arrayToField('file', $field);
        $field['table'] = $this->table;
        $field['useExistingField'] = $this->useExistingField;
        $field['identifier'] = $this->identifier;
        $config = new FileFieldConfig();
        $config->arrayToConfig($field, array_keys(get_object_vars($this)));
        $this->config = $config;
    }

    /**
     * Merge configuration from another file field.
     *
     * @param FileField $foreign The foreign file field to merge.
     */
    public function mergeField(FileField $foreign): void
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
     * @return string The parsed field representation.
     */
    public function parseField(int $mode, int $level): string
    {
        return $this->__parseField($this->config, $mode, $level);
    }

    /**
     * Convert the field into an array representation.
     *
     * @return array The array representation of the field.
     */
    public function fieldToArray(): array
    {
        return $this->__fieldToArray($this->config);
    }

    /**
     * Constructor for the file field.
     *
     * @param array  $field The array configuration for the field.
     * @param string $table The table name associated with the field.
     */
    public function __construct(array $field, string $table)
    {
        $this->table = $table;
        $this->_arrayToField($field);
    }
}