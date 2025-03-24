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
use DS\CbBuilder\Utility\MathematicalExpressions;
use Exception;
use InvalidArgumentException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Configuration class for image field crop variants.
 */
final class ImageFieldCropVariants extends Config
{
    /**
     * Array of allowed aspect ratios.
     */
    protected array $allowedAspectRatios = [];

    /**
     * Array of cover areas.
     */
    protected array $coverAreas = [];

    /**
     * Array representing the crop area.
     */
    protected array $cropArea = [];

    /**
     * Array representing the focus area.
     */
    protected array $focusArea = [];

    /**
     * Unique identifier for the crop variant.
     */
    protected string $identifier = '';

    /**
     * Selected aspect ratio.
     */
    protected string $selectedRatio = '';

    /**
     * Title of the crop variant.
     */
    protected string $title = '';

    /**
     * Get the array of allowed aspect ratios.
     *
     * @return array The list of allowed aspect ratios.
     */
    public function getAllowedAspectRatios(): array
    {
        return $this->allowedAspectRatios;
    }

    /**
     * Get the array of cover areas.
     *
     * @return array The list of cover areas.
     */
    public function getCoverAreas(): array
    {
        return $this->coverAreas;
    }

    /**
     * Get the array representing the crop area.
     *
     * @return array The crop area settings.
     */
    public function getCropArea(): array
    {
        return $this->cropArea;
    }

    /**
     * Get the array representing the focus area.
     *
     * @return array The focus area settings.
     */
    public function getFocusArea(): array
    {
        return $this->focusArea;
    }

    /**
     * Get the unique identifier for the crop variant.
     *
     * @return string The identifier.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Get the selected aspect ratio.
     *
     * @return string The selected ratio.
     */
    public function getSelectedRatio(): string
    {
        return $this->selectedRatio;
    }

    /**
     * Get the title of the crop variant.
     *
     * @return string The title.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the array of allowed aspect ratios.
     *
     * @param array $allowedAspectRatios The list of allowed aspect ratios.
     */
    public function setAllowedAspectRatios(array $allowedAspectRatios): void
    {
        $this->allowedAspectRatios = $allowedAspectRatios;
    }

    /**
     * Set the array of cover areas.
     *
     * @param array $coverAreas The list of cover areas.
     */
    public function setCoverAreas(array $coverAreas): void
    {
        $this->coverAreas = $coverAreas;
    }

    /**
     * Set the array representing the crop area.
     *
     * @param array $cropArea The crop area settings.
     */
    public function setCropArea(array $cropArea): void
    {
        $this->cropArea = $cropArea;
    }

    /**
     * Set the array representing the focus area.
     *
     * @param array $focusArea The focus area settings.
     */
    public function setFocusArea(array $focusArea): void
    {
        $this->focusArea = $focusArea;
    }

    /**
     * Set the unique identifier for the crop variant.
     *
     * @param string $identifier The identifier.
     */
    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    /**
     * Set the selected aspect ratio.
     *
     * @param string $selectedRatio The selected ratio.
     */
    public function setSelectedRatio(string $selectedRatio): void
    {
        $this->selectedRatio = $selectedRatio;
    }

    /**
     * Set the title of the crop variant.
     *
     * @param string $title The title.
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
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

        if (!empty($foreign->getAllowedAspectRatios())) {
            $this->allowedAspectRatios = $foreign->getAllowedAspectRatios();
        }

        if (!empty($foreign->getCoverAreas())) {
            $this->coverAreas = $foreign->getCoverAreas();
        }

        if (!empty($foreign->getCropArea())) {
            $this->cropArea = $foreign->getCropArea();
        }

        if (!empty($foreign->getFocusArea())) {
            $this->focusArea = $foreign->getFocusArea();
        }

        if ($foreign->getIdentifier() !== '') {
            $this->identifier = $foreign->getIdentifier();
        }

        if ($foreign->getSelectedRatio() !== '') {
            $this->selectedRatio = $foreign->getSelectedRatio();
        }

        if ($foreign->getTitle() !== '') {
            $this->title = $foreign->getTitle();
        }
    }

    /**
     * Keywords for aspect ratio configuration.
     */
    const ASPECT_RATIO_KEYWORDS = [
        'title' => self::STRING_TYPE, 'value' => self::FUNCTION, 'disabled' => self::BOOL_TYPE
    ];

    /**
     * Keywords for area configuration.
     */
    const AREA_KEYWORDS = [
        'x', 'y', 'width', 'height'
    ];

    /**
     * Validates the 'value' keyword for an aspect ratio configuration.
     *
     * Ensures the value is either a float or a string that can be evaluated as a float.
     *
     * @param mixed $value The value to validate.
     * @param string $setting The setting being validated.
     * @throws Exception If the value is not of type float or string.
     */
    private function _validateAspectRatioKeyword_value(mixed &$value, string $setting): void
    {
        if (!is_float($value) && !is_string($value)) {
            throw new Exception(
                "'Image' field '$this->identifier' configuration '$setting' " .
                "must be of type float or string, representing a float."
            );
        }
        $float = 0.0;
        if (is_string($value)) {
            $me = new MathematicalExpressions();
            $float = $me->compileExpression($value, [], MathematicalExpressions::RETURN_FLOAT);
            $value = $float;
        }
    }

    /**
     * Validates and sets an area configuration (e.g., focusArea, cropArea).
     *
     * Ensures the area is an array with valid keys (x, y, width, height) and values.
     *
     * @param mixed $entry The area configuration to validate and set.
     * @param string $setting The setting being validated (e.g., focusArea).
     * @throws Exception If the area configuration is invalid.
     */
    private function _validateAndSetArea(mixed $entry, string $setting): void
    {
        $fixMsg =   "\nFix:\ncropVariants:\n  desktop:\n    title: 'desktop'\n    focusArea:\n" .
                    "      x: '1 / 3'\n      y: '0.5'\n      width: '1 / 2.5'\n      height: 0.25\n";

        if (!is_array($entry)) {
            throw new Exception(
                "'Image' field '$this->identifier' configuration 'cropVariants['$this->identifier']['$setting']' " .
                "must be of type array.$fixMsg"
            );
        }

        $i = 0;
        foreach ($entry as $key => $value) {
            if (!is_string($key)) {
                throw new Exception(
                    "'Image' field '$this->identifier' configuration 'cropVariants['$this->identifier']['$setting'][$i]' " .
                    "key must be of type string.$fixMsg"
                );
            }

            if (!in_array($key, self::AREA_KEYWORDS)) {
                throw new Exception(
                    "'Image' field '$this->identifier' configuration 'cropVariants['$this->identifier']['$setting']['$key']' " .
                    "invalid key '$key'. Valid keywords are: " . implode(', ', self::AREA_KEYWORDS)
                );
            }
            /** We are reusing this function, as it performs the desired validation. */
            $this->_validateAspectRatioKeyword_value($value, "cropVariants['$this->identifier']['$setting']['$key']");
            $entry[$key] = $value;
            $i++;
        }

        $this->$setting = $entry;
    }

    /**
     * Validates and sets the allowed aspect ratios configuration.
     *
     * Ensures each aspect ratio is an array with valid keywords (title, value, disabled).
     *
     * @param mixed $entry The allowed aspect ratios configuration to validate and set.
     * @throws Exception If the configuration is invalid.
     */
    private function _validateAndSetAllowedAspectRatios(mixed $entry): void
    {
        $fixMsg =   "\nFix:\ncropVariants:\n  mobile:\n    title: 'mobile'\n    allowedAspectRatios:" .
                    "\n      4by3:\n" .
                    "        title: '4/3'\n        value: '4/3'\n  desktop:\n    title: 'desktop'\n    allowedAspectRatios:\n" .
                    "      4by3:\n        title: '4/3'\n        value: '4/3'";

        if (!is_array($entry)) {
            throw new Exception(
                "'Image' field '$this->identifier' configuration 'cropVariants['$this->identifier']['allowedAspectRatios']' " .
                "must be of type array.$fixMsg"
            );
        }

        $i = 0;
        foreach ($entry as $key => $value) {
            if (!is_string($key)) {
                throw new Exception(
                    "'Image' field '$this->identifier' configuration 'cropVariants['$this->identifier']['allowedAspectRatios'][$i]' " .
                    "key must be of type string.$fixMsg"
                );
            }

            if (!is_array($value)) {
                throw new Exception(
                    "'Image' field '$this->identifier' configuration 'cropVariants['$this->identifier']['allowedAspectRatios'][$key]' " .
                    "value must be of type array.$fixMsg"
                );
            }

            $j = 0;
            foreach ($value as $_key => $_value) {
                if (!is_string($_key)) {
                    throw new Exception(
                        "'Image' field '$this->identifier' configuration " .
                        "'cropVariants['$this->identifier']['allowedAspectRatios'][$key][$j]' " .
                        "key must be of type string.$fixMsg"
                    );
                }
                if (!in_array($_key, array_keys(self::ASPECT_RATIO_KEYWORDS))) {
                    throw new Exception(
                        "'Image' field '$this->identifier' configuration " .
                        "'cropVariants['$this->identifier']['allowedAspectRatios'][$key][$_key]' " .
                        "invalid keyword '$_key'. Valid keywords are: " . implode(', ', array_keys(self::ASPECT_RATIO_KEYWORDS))
                    );
                }
                switch (self::ASPECT_RATIO_KEYWORDS[$_key]) {
                    case self::STRING_TYPE:
                        if (!is_string($_value)) {
                            throw new Exception(
                                "'Image' field '$this->identifier' configuration " .
                                "'cropVariants['$this->identifier']['allowedAspectRatios'][$key][$_key]' " .
                                "value must be of type string."
                            );
                        }
                        break;
                    case self::BOOL_TYPE:
                        if (!is_bool($_value)) {
                            throw new Exception(
                                "'Image' field '$this->identifier' configuration " .
                                "'cropVariants['$this->identifier']['allowedAspectRatios'][$key][$_key]' " .
                                "value must be of type boolean."
                            );
                        }
                        break;
                    case self::FUNCTION:
                        $function = '_validateAspectRatioKeyword_' . $_key;
                        $this->$function($_value, "cropVariants['$this->identifier']['allowedAspectRatios'][$key][$_key]");
                        $value[$_key] = $_value;
                        break;
                }
                $j++;
            }
            $i++;
            $entry[$key] = $value;
        }
        
        $this->allowedAspectRatios = $entry;
    }

    /**
     * Constructor for ImageFieldCropVariants.
     *
     * Initializes the object with a given configuration and identifier.
     *
     * @param array $config The configuration array.
     * @param string $identifier The unique identifier for the crop variant.
     * @param string $setting The setting being configured.
     * @throws Exception If the configuration contains invalid settings.
     */
    public function __construct(array $config, string $identifier, string $setting)
    {
        $this->checkRequirements($config, ['allowedAspectRatios'], 'Image', 'cropVariants');
        $this->identifier = $identifier;
        $properties = get_object_vars($this);
        foreach ($config as $configKey => $value) {
            if ($this->isValidConfig($properties, $configKey)) {
                switch ($configKey) {
                    case 'allowedAspectRatios':
                        $this->_validateAndSetAllowedAspectRatios($value);
                        break;
                    case 'coverAreas':
                        $this->_validateAndSetArea($value, 'coverAreas');
                        break;
                    case 'cropArea':
                        $this->_validateAndSetArea($value, 'cropArea');
                        break;
                    case 'focusArea':
                        $this->_validateAndSetArea($value, 'focusArea');
                        break;
                    default:
                        $this->$configKey = $value;
                        break;
                }
            } else {
                throw new Exception(
                    "'Image' field '$identifier' configuration 'cropVariants['$setting']' setting '$configKey' is not valid. " .
                    "Valid settings are: " . implode(', ', array_keys($properties))
                );
            }
        }
    }
}

/**
 * Configuration class for image fields.
 */
final class ImageFieldConfig extends Config
{
    /**
     * Comma-separated list of allowed file extensions.
     */
    protected string $allowedExtensions = '';

    /**
     * Name of the database field that contains the uid of the file record. By default set to uid_local.
     */
    protected string $file_field = '';

    /**
     * Unique identifier for the field.
     */
    protected string $identifier = '';

    /**
     * Array of crop variants.
     */
    protected array $cropVariants = [];

    /**
     * Whether the field is read-only.
     */
    protected ?bool $readOnly = null;

    /**
     * Get the allowed file extensions.
     *
     * @return string The allowed extensions.
     */
    public function getAllowedExtensions(): string
    {
        return $this->allowedExtensions;
    }

    /**
     * Get the field name where the uid of the file record is saved.
     *
     * @return string The field name.
     */
    public function getFileField(): string
    {
        return $this->file_field;
    }

    /**
     * Get the unique identifier for the field.
     *
     * @return string The identifier.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Get the array of crop variants.
     *
     * @return array The list of crop variants.
     */
    public function getCropVariants(): array
    {
        return $this->cropVariants;
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
     * Set the allowed file extensions.
     *
     * @param string $allowedExtensions The allowed extensions.
     */
    public function setAllowedExtensions(string $allowedExtensions): void
    {
        $this->allowedExtensions = $allowedExtensions;
    }

    /**
     * Set the field name where the uid of the file record is saved.
     *
     * @param string $fileField The field name.
     */
    public function setFileField(string $fileField): void
    {
        $this->file_field = $fileField;
    }

    /**
     * Set the unique identifier for the field.
     *
     * @param string $identifier The identifier.
     */
    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    /**
     * Set the array of crop variants.
     *
     * @param array $cropVariants The list of crop variants.
     */
    public function setCropVariants(array $cropVariants): void
    {
        $this->cropVariants = $cropVariants;
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

        if ($foreign->getAllowedExtensions() !== '') {
            $this->allowedExtensions = $foreign->getAllowedExtensions();
        }

        if ($foreign->getFileField() !== '') {
            $this->file_field = $foreign->getFileField();
        }

        if ($foreign->getIdentifier() !== '') {
            $this->identifier = $foreign->getIdentifier();
        }

        if (!empty($foreign->getCropVariants())) {
            $this->cropVariants = $foreign->getCropVariants();
        }

        if ($foreign->isReadOnly() !== null) {
            $this->readOnly = $foreign->isReadOnly();
        }
    }

    /**
     * Valid file extensions for images.
     */
    const VALID_TYPES = [
        'gif', 'jpg', 'jpeg', 'png'
    ];

    /**
     * Validates the allowed file extensions configuration.
     *
     * Ensures the extensions are provided as a string and checks if they are supported.
     *
     * @param mixed $entry The allowed extensions configuration to validate.
     * @param array $config The full configuration.
     * @throws Exception If the extensions are not valid.
     */
    private function _validateAllowedExtensions(mixed $entry, array $config): void
    {
        if (!is_string($entry)) {
            throw new Exception(
                "'Image' field '$this->identifier' configuration 'allowedExtensions' must be of type string.\n" .
                "Fix:\nallowedExtensions: 'jpg, jpeg'"
            );
        }

        if (!FieldBuilder::isSurpressedWarning(307226409)) {
            $entry = GeneralUtility::trimExplode(',', $entry);
            $i = 0;
            foreach ($entry as $value) {
                $value = strtolower($value);
                if (!in_array($value, self::VALID_TYPES, true)) {
                    throw new Exception(
                        "WARNING: 'Image' field '$this->identifier' configuration 'allowedExtensions[$i]' contains a file extension " .
                        "'$value' that might not be supported by default. Supported types are: " . implode(', ', self::VALID_TYPES) .
                        "\nIf your server's ImageMagick/GraphicsMagick installation supports other types, you can suppress this warning " .
                        "by adding '307226409' to 'surpressedWarnings' in 'cbconfigyaml'."
                    );
                }
                $i++;
            }
        }
    }

    /**
     * Validates and sets the crop variants configuration.
     *
     * Ensures each crop variant is an array and creates an ImageFieldCropVariants object for it.
     *
     * @param mixed $entry The crop variants configuration to validate and set.
     * @param array $config The full configuration.
     * @throws Exception If the crop variants configuration is invalid.
     */
    private function _validateCropVariants(mixed $entry, array $config): void
    {
        $fixMsg = "\nFix:\ncropVariants:\n  mobile:\n    ...\n  desktop:\n    ...";
        if (!is_array($entry)) {
            throw new Exception(
                "'Image' field '$this->identifier' configuration 'cropVariants' " .
                "must be of type array.$fixMsg"
            );
        }
        $i = 0;
        foreach ($entry as $key => $value) {
            if (!is_string($key)) {
                throw new Exception(
                    "'Image' field '$this->identifier' configuration 'cropVariants[$i]' " .
                    "key must be of type string.$fixMsg"
                );
            }
            if (!is_array($value)) {
                throw new Exception(
                    "'Image' field '$this->identifier' configuration 'cropVariants[$key]' " .
                    "value must be of type array."
                );
            }
            $this->cropVariants[$key] = new ImageFieldCropVariants($value, $this->identifier, $key);
            $i++;
        }
        
    }

    /**
     * Converts an array configuration to the object's properties.
     *
     * Validates and sets each property based on the configuration.
     *
     * @param array $config The configuration array.
     * @param string $table The table name.
     * @param array $fieldProperties The properties of the field.
     */
    public function arrayToConfig(array $config, array $fieldProperties, ?array $globalConf = NULL, mixed $misc = NULL): void
    {
        $globalConf = $globalConf ?? $config;
        $this->checkRequirements($globalConf, ['identifier'], 'Image');
        if (!is_string($misc)) {
            throw new InvalidArgumentException (
                "Parameter 'misc' must be of type string"
            );
        }
        $properties = get_object_vars($this);
        foreach ($config as $configKey => $value) {
            if ($this->isValidConfig($properties, $configKey)) {
                if ($GLOBALS['CbBuilder']['config']['Strict'] === true) {
                    switch ($configKey) {
                        case 'allowedExtensions':
                            $this->_validateAllowedExtensions($value, $globalConf);
                            $this->allowedExtensions = $value;
                            break;
                        case 'cropVariants':
                            $this->_validateCropVariants($value, $globalConf);
                            break;
                        case 'file_field':
                            $this->validateField($value, $globalConf, 'file_field', 'Image', [$misc]);
                            $this->file_field = $value;
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
                    "'Image' field '$identifier' configuration '$configKey' is not valid.\n" .
                    "Valid settings are: " . implode(', ', array_keys($properties))
                );
            }
        }
    }
}

/**
 * Class representing an image field.
 */
final class ImageField extends Field
{
    /**
     * Configuration object for the image field.
     */
    protected ImageFieldConfig $config;

    /**
     * Get the configuration object for the image field.
     *
     * @return ImageFieldConfig The configuration object.
     */
    public function getConfig(): ImageFieldConfig
    {
        return $this->config;    
    }

    /**
     * Initializes the field from an array configuration.
     *
     * @param array $field The field configuration array.
     */
    private function _arrayToField(array $field): void
    {
        $this->__arrayToField('imageManipulation', $field);
        $field['table'] = $this->table;
        $field['useExistingField'] = $this->useExistingField;
        $field['identifier'] = $this->identifier;
        $config = new ImageFieldConfig();
        $config->arrayToConfig($field, array_keys(get_object_vars($this)), NULL, $this->table);
        $this->config = $config;
        
    }

    /**
     * Merges the current field with another field.
     *
     * @param ImageField $foreign The foreign field to merge.
     */
    public function mergeField(ImageField $foreign): void
    {
        $this->config->mergeConfig($foreign->getConfig());
        $this->mergeFields($foreign);
    }

    /**
     * Parses the field for rendering.
     *
     * @param int $mode The rendering mode.
     * @param int $level The rendering level.
     * @return string The parsed field.
     */
    public function parseField(int $mode, int $level): string
    {
        return $this->__parseField($this->config, $mode, $level);
    }

    /**
     * Converts the field to an array configuration.
     *
     * @return array The field configuration array.
     */
    public function fieldToArray(): array
    {
        return $this->__fieldToArray($this->config);
    }

    /**
     * Constructor for ImageField.
     *
     * Initializes the field with a given configuration and table name.
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