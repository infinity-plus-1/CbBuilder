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
use DS\CbBuilder\FieldBuilder\Tables\Table;
use DS\CbBuilder\Utility\ArrayParser;
use Exception;
use InvalidArgumentException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Custom exception class for field-related errors.
 */
class FieldException extends Exception {}

/**
 * Base class for fields in the field builder.
 */
class Field
{
    /**
     * The type of the field. Always starts with an uppercase letter.
     */
    protected string $type = '';

    /**
     * Unique identifier for the field, which will be used as the column name in the database.
     */
    protected string $identifier = '';

    /**
     * Whether to reuse an existing column from the tt_content table.
     * If set to true and no existing column is found, an error will be thrown if 'Strict' mode is enabled,
     * otherwise it will be treated as false.
     */
    protected bool $useExistingField = false;

    /**
     * The table this field belongs to. Defaults to 'tt_content' or the identifier of the current 'Collection'.
     */
    protected string $table = '';

    /**
     * Label for the field, serving as a short identifier for backend users.
     */
    protected string $label = '';

    /**
     * Short description for the field.
     */
    protected string $description = '';

    /**
     * Localization mode for the field.
     */
    protected string $l10n_mode = '';

    /**
     * Display condition for the field.
     */
    protected array|string $displayCond = [];

    /**
     * Whether the field should be excluded.
     */
    protected ?bool $exclude = null;

    /**
     * Localization display settings for the field.
     */
    protected string $l10n_display = '';

    /**
     * On-change event handler for the field.
     */
    protected string $onChange = '';

    /**
     * Render type for the field.
     */
    protected string $renderType = '';

    /**
     * Array of CSS classes applied to the field.
     */
    protected array $classes = [];

    /**
     * Keywords for localization mode settings.
     */
    const L10_N_MODE_KEYWORDS = [
        'exclude', 'prefixLangTitle'
    ];

    /**
     * Keywords for localization display settings.
     */
    const L10_N_DISPLAY_KEYWORDS = [
        'hideDiff', 'defaultAsReadonly'
    ];

    /**
     * Keywords for on-change event handlers.
     */
    const ON_CHANGE_KEYWORDS = [
        'reload'
    ];

    /**
     * Mode for parsing with keys.
     */
    const PARSE_WITH_KEY_MODE = 1;

    /**
     * Mode for parsing without keys.
     */
    const PARSE_WITHOUT_KEY_MODE = 2;

    /**
     * Mapping of render types to their respective subtypes.
     */
    const RENDER_TYPE_MAP = [
        'select' => [
            'selectSingle', 'selectSingleBox', 'selectCheckBox', 'selectMultipleSideBySide', 'selectTree'
        ],
        'textarea' => [
            'belayoutwizard', 'textTable', 'codeEditor'
        ],
        'checkbox' => [
            'checkboxToggle', 'checkboxLabeledToggle'
        ],
        'custom' => [
            '*'
        ],
        'password' => [
            'passwordGenerator'
        ]
    ];

    /**
     * Merge settings from another field into this field.
     * 
     * @param Field $foreign The field to merge settings from.
     */
    protected function mergeFields(Field $foreign): void
    {
        $this->label = $foreign->getLabel() !== '' ? $foreign->getLabel() : $this->label;
        $this->description = $foreign->getDescription() !== '' ? $foreign->getDescription() : $this->description;
        $this->l10n_mode = $foreign->getL10nMode() !== '' ? $foreign->getL10nMode() : $this->l10n_mode;
        $this->l10n_display = $foreign->getL10nDisplay() !== '' ? $foreign->getL10nDisplay() : $this->l10n_display;
        $this->displayCond = $foreign->getDisplayCond() !== '' ? $foreign->getDisplayCond() : $this->displayCond;
        $this->exclude = $foreign->getExclude() !== null ? $foreign->getExclude() : $this->exclude;
        $this->renderType = $foreign->getRenderType() !== '' ? $foreign->getRenderType() : $this->renderType;
        $this->onChange = $foreign->getOnchange() !== '' ? $foreign->getOnchange() : $this->onChange;
        if ($foreign->getClasses() !== []) {
            $this->classes = array_merge($this->classes, $foreign->getClasses());
        }
    }

    /**
     * Validates the render type for a field.
     * 
     * @param mixed $renderType The render type to validate.
     * 
     * @throws Exception If the render type is invalid.
     */
    private function _validateRenderType(mixed $renderType): void
    {
        if (!is_string($renderType)) {
            throw new Exception (
                "'$this->identifier' 'renderType' must be of type string."
            );
        }

        if (isset(self::RENDER_TYPE_MAP[strtolower($this->type)])) {
            if (in_array($renderType, self::RENDER_TYPE_MAP[strtolower($this->type)])) {
                return;
            }
        }

        $customRenderTypesString = $GLOBALS['CbBuilder']['config']['customRenderTypes'] ?? '';
        if ($customRenderTypesString !== '') {
            $customRenderTypesArray = GeneralUtility::trimExplode(',', $customRenderTypesString);
            $i = 0;
            foreach ($customRenderTypesArray as $customRenderType) {
                $customRenderTypeArray = explode('->', $customRenderType);
                if (count($customRenderTypeArray) !== 2) {
                    throw new Exception (
                        "'$this->identifier' 'renderType' 'cbConfig.yaml['customRenderTypes'][$i]' has invalid syntax.\n" .
                        "The syntax should be: customRenderTypes: Fieldtype->renderTypeIdentifier, Select->myFancyRenderType, ..."
                    );
                }
                if ($customRenderTypeArray[0] === $this->type && $customRenderTypeArray[1] === $renderType) {
                    return;
                }
                $i++;
            }
        }

        throw new Exception (
            "WARNING: '$this->identifier' 'renderType' '$renderType' is not a valid render type for the field type '$this->type'.\n" .
            "To fix this, add an entry in the format 'Fieldtype->renderTypeIdentifier' to 'customRenderTypes' in 'cbConfig.yaml'. For example:\n" .
            "customRenderTypes: Fieldtype->renderTypeIdentifier, Select->myFancyRenderType, ...\n" .
            "IMPORTANT: The custom render type must be registered in the 'ext_localconf.php' already.\n" .
            "Alternatively, use one of the default render types: " . implode(', ', self::RENDER_TYPE_MAP[strtolower($this->type)]) . "\n" .
            "You can suppress this warning by adding '763767178' to 'suppressedWarnings' in 'cbConfig.yaml'."
        );
    }

    /**
     * Validates a string value against a list of allowed keywords.
     * 
     * @param mixed $value The value to validate.
     * @param array $keywords The list of allowed keywords.
     * @param string $setting The name of the setting being validated.
     * 
     * @throws Exception If the value is not a string or not in the list of keywords.
     */
    private function _validateStringKeywords(mixed $value, array $keywords, string $setting)
    {
        if (!is_string($value)) {
            throw new Exception (
                "'$this->identifier' '$setting' must be of type string."
            );
        }

        if (!in_array($value, $keywords)) {
            throw new Exception (
                "'$this->identifier' '$setting' must be one of the following keywords: " .
                implode(', ', $keywords)
            );
        }
    }

    /**
     * Creates a new field instance based on the provided configuration.
     * 
     * @param array $field The field configuration.
     * @param string $table The table the field belongs to.
     * @param Table $parentElement The parent table element.
     * 
     * @return Field|PaletteContainer|CollectionContainer The created field instance.
     * 
     * @throws Exception If the field configuration is invalid.
     */
    public static function createField(array $field, string $table, Table $parentElement)
    {
        if (!array_key_exists('identifier', $field)) {
            throw new Exception (
                "A field in table '$table' has no identifier. Every field must have an identifier."
            );
        }
        $identifier = $field['identifier'];
        if (!array_key_exists('type', $field)) {
            throw new Exception (
                "Field '$identifier' in table '$table' has no type set. Every field must have a type."
            );
        }
        $type = $field['type'];
        switch ($type) {
            case 'Text':
                return new TextField($field, $table);
                break;
            case 'Textarea':
                return new TextareaField($field, $table);
                break;
            case 'File':
                return new FileField($field, $table);
                break;
            case 'Select':
                return new SelectField($field, $table);
                break;
            case 'Number':
                return new NumberField($field, $table);
                break;
            case 'Checkbox':
                return new CheckboxField($field, $table);
                break;
            case 'Palette':
                $palette = new PaletteContainer();
                $palette->injectFieldPalette($field, $table, $parentElement);
                return $palette;
                break;
            case 'Link':
                return new LinkField($field, $table);
                break;
            case 'Json':
                return new JsonField($field, $table);
                break;
            case 'Color':
                return new ColorField($field, $table);
                break;
            case 'Datetime':
                return new DatetimeField($field, $table);
                break;
            case 'Email':
                return new EmailField($field, $table);
                break;
            case 'Folder':
                return new FolderField($field, $table);
                break;
            case 'None':
                return new NoneField($field, $table);
                break;
            case 'Pass':
                return new PassField($field, $table);
                break;
            case 'Image':
                return new ImageField($field, $table);
                break;
            case 'Category':
                return new CategoryField($field, $table);
                break;
            case 'Password':
                return new PasswordField($field, $table);
                break;
            case 'Radio':
                return new RadioField($field, $table);
                break;
            case 'Slug':
                return new SlugField($field, $table);
                break;
            case 'Uuid':
                return new UuidField($field, $table);
                break;
            case 'Flex':
                return new FlexField($field, $table);
                break;
            case 'Group':
                return new GroupField($field, $table);
                break;
            case 'Collection':
                return new CollectionContainer($field, $table, $parentElement);
                break;
            case 'Custom':
                return new CustomField($field, $table);
                break;
            case 'Linebreak':
                return new LinebreakField($field, $table);
                break;
            default:
                throw new Exception (
                    "Unknown field type '$type' with identifier '$identifier' in table '$table'."
                );
                break;
        }
    }

    /**
     * Renders and validates the classes for a field.
     * 
     * @param mixed $value The classes to render. Can be a string or an array.
     * 
     * @throws InvalidArgumentException If the classes are not of type string or array.
     */
    private function _renderClasses(mixed $value): void
    {
        if (!is_string($value) && !is_array($value)) {
            throw new InvalidArgumentException (
                "'$this->type' field '$this->identifier' configuration 'classes' must be of type string or array.\n" .
                "If multiple classes are needed, the string should contain a comma-separated list of class names."
            );
        }
        if (is_string($value)) {
            $value = GeneralUtility::trimExplode(',', $value);
        }
        $this->classes = $value;
    }

    /**
     * Converts an array of field settings into the corresponding field properties.
     * 
     * @param string $type The type of the field.
     * @param array $fields The array of field settings.
     * @param array|null $excludes Optional list of properties to exclude.
     */
    protected function __arrayToField(string $type, array $fields, ?array $excludes = []): void
    {
        $this->type = $type;
        $properties = get_object_vars($this);
        foreach ($fields as $key => $value) {
            if (array_key_exists($key, $properties)) {
                switch ($key) {
                    case 'l10n_mode':
                        $this->_validateStringKeywords($value, self::L10_N_MODE_KEYWORDS, 'l10n_mode');
                        $this->$key = $value;
                        break;
                    case 'l10n_display':
                        $this->_validateStringKeywords($value, self::L10_N_DISPLAY_KEYWORDS, 'l10n_display');
                        $this->$key = $value;
                        break;
                    case 'onChange':
                        $this->_validateStringKeywords($value, self::ON_CHANGE_KEYWORDS, 'onChange');
                        $this->$key = $value;
                        break;
                    case 'renderType':
                        if (!FieldBuilder::isSurpressedWarning(763767178)) {
                            $this->_validateRenderType($value);
                        }
                        $this->$key = $value;
                        break;
                    case 'classes':
                        $this->_renderClasses($value);
                        break;
                    default:
                        if (!in_array($key, $excludes)) $this->$key = $value;
                        break;
                }
            }
        }
    }

    /**
     * Parses the field into a string representation.
     * 
     * @param int $mode The parsing mode.
     * @param int $level The nesting level.
     * 
     * @return string The parsed string representation of the field.
     */
    public function parseField(int $mode, int $level): string { return ''; }

    /**
     * Converts the field into an array representation.
     * 
     * @return array The array representation of the field.
     */
    public function fieldToArray(): array { return []; }

    /**
     * Returns the configuration class of the field.
     * 
     * @return Config The configuration class of the field.
     */
    public function getConfig(): ?Config { return NULL; }

    /**
     * Converts the field into an array representation, excluding certain properties.
     * 
     * @param Config $config The configuration object.
     * @param array|null $exclude Optional list of properties to exclude.
     * 
     * @return array The array representation of the field.
     */
    protected function __fieldToArray(Config $config, ?array $exclude = []): array
    {
        $array = [];
        $properties = array_keys(get_object_vars($this));
        foreach ($properties as $property) {
            switch ($property) {
                case 'useExistingField':
                case 'table':
                case 'type':
                case 'identifier':
                case 'renderType':
                case 'classes':
                    break;
                default:
                    if (
                        ((is_string($this->$property) && $this->$property !== '')
                        || (is_int($this->$property) && $this->$property >= 0)
                        || (is_float($this->$property) && $this->$property >= 0.0)
                        || (is_array($this->$property) && !empty($this->$property))
                        || (is_bool($this->$property) && $this->$property !== NULL))
                        && !in_array($property, $exclude)
                    ) {
                        $array[$property] = $this->$property;
                    }
                    break;
            }
        }
        if ($this->label === '') $array['label'] = $this->identifier;
        $array['config'] = $config->parseConfig($this->type, $exclude);
        return $array;
    }

    /**
     * Converts the field into an array representation, excluding certain properties.
     * 
     * @param Config $config The configuration object.
     * @param array|null $exclude Optional list of properties to exclude.
     * 
     * @return array The array representation of the field.
     */
    public function fieldToYamlArray(?Config $config = NULL, ?array $exclude = []): array
    {
        $array = [];
        $properties = array_keys(get_object_vars($this));
        foreach ($properties as $property) {
            switch ($property) {
                case 'table':
                    break;
                case 'classes':
                    $array[$property] = implode(', ', $this->$property);
                    break;
                default:
                    if (
                        ((is_string($this->$property) && $this->$property !== '')
                        || (is_int($this->$property) && $this->$property >= 0)
                        || (is_float($this->$property) && $this->$property >= 0.0)
                        || (is_array($this->$property) && !empty($this->$property))
                        || (is_bool($this->$property) && $this->$property !== NULL))
                        && !in_array($property, $exclude)
                    ) {
                        $array[$property] = $this->$property;
                    }
                    break;
            }
        }
        if ($this->label === '') $array['label'] = $this->identifier;
        if ($config) {
            $configs = $config->parseConfig($this->type, $exclude);
            foreach ($configs as $key => $value) {
                switch ($key) {
                    case 'fields':
                        break;
                    case 'type':
                        $array[$key] = FieldBuilder::convertTypeColumnToField($value);
                        break;
                    
                    default:
                        $array[$key] = $value;
                        break;
                }
            }
        }
        return $array;
    }

    /**
     * Parses the field into a string representation based on the provided configuration and mode.
     * 
     * @param Config $config The configuration object.
     * @param int $mode The parsing mode.
     * @param int $level The nesting level.
     * @param array|null $exclude Optional list of properties to exclude.
     * 
     * @return string The parsed string representation of the field.
     */
    protected function __parseField(Config $config, int $mode, int $level, ?array $exclude = []): string
    {
        $array = $this->__fieldToArray($config, $exclude);
        
        return  ($mode === self::PARSE_WITH_KEY_MODE)
                ? ArrayParser::arrayToString($array, $this->identifier, ($level+1), true)
                : ($mode === self::PARSE_WITHOUT_KEY_MODE
                ? ArrayParser::arrayToString($array, '', ($level+1), false)
                : '');
    }

    /**
     * Gets the type of the field.
     * 
     * @return string The type of the field.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Gets the identifier of the field.
     * 
     * @return string The identifier of the field.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Gets the description of the field.
     * 
     * @return string The description of the field.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Gets the table the field belongs to.
     * 
     * @return string The table the field belongs to.
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Gets the label of the field.
     * 
     * @return string The label of the field.
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Gets the localization mode of the field.
     * 
     * @return string The localization mode of the field.
     */
    public function getL10nMode(): string
    {
        return $this->l10n_mode;
    }

    /**
     * Gets the localization display settings of the field.
     * 
     * @return string The localization display settings of the field.
     */
    public function getL10nDisplay(): string
    {
        return $this->l10n_display;
    }

    /**
     * Gets the display condition of the field.
     * 
     * @return string|array The display condition of the field.
     */
    public function getDisplayCond(): string|array
    {
        return $this->displayCond;
    }

    /**
     * Gets whether the field should be excluded.
     * 
     * @return bool|null Whether the field should be excluded.
     */
    public function getExclude(): bool|null
    {
        return $this->exclude;
    }

    /**
     * Gets the on-change event handler of the field.
     * 
     * @return string The on-change event handler of the field.
     */
    public function getOnchange(): string
    {
        return $this->onChange;
    }

    /**
     * Checks if the field uses an existing field.
     * 
     * @return bool|null Whether the field uses an existing field.
     */
    public function isUseExistingField(): bool|null
    {
        return $this->useExistingField;    
    }

    /**
     * Gets the render type of the field.
     * 
     * @return string The render type of the field.
     */
    public function getRenderType(): string
    {
        return $this->renderType;
    }

    /**
     * Gets the classes applied to the field.
     * 
     * @return array The classes applied to the field.
     */
    public function getClasses(): array
    {
        return $this->classes;
    }
}