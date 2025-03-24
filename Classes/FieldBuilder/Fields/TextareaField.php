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
use Exception;
use InvalidArgumentException;

/**
 * Configuration class for textarea fields.
 */
final class TextareaFieldConfig extends Config
{
    /**
     * Whether autocomplete is enabled for this field.
     */
    protected ?bool $autocomplete = null;

    /**
     * The number of columns for the textarea.
     */
    protected int $cols = -1;

    /**
     * The default value for the textarea.
     */
    protected string $default = '';

    /**
     * Whether rich text editing is enabled for this field.
     */
    protected ?bool $enableRichtext = null;

    /**
     * Whether the tabulator key is enabled for this field.
     */
    protected ?bool $enableTabulator = null;

    /**
     * Evaluation type for the field (e.g., date, email).
     */
    protected string $eval = '';

    /**
     * Whether a fixed font should be used for this field.
     */
    protected ?bool $fixedFont = null;

    /**
     * Format for the field (e.g., date format).
     */
    protected string $format = '';

    /**
     * Unique identifier for the field.
     */
    protected string $identifier = '';

    /**
     * "is_in" condition for the field.
     */
    protected string $is_in = '';

    /**
     * Maximum allowed value for the field.
     */
    protected int $max = -1;

    /**
     * Minimum allowed value for the field.
     */
    protected int $min = -1;

    /**
     * Whether the field can be null.
     */
    protected ?bool $nullable = null;

    /**
     * Placeholder text for the field.
     */
    protected string $placeholder = '';

    /**
     * Whether the field is read-only.
     */
    protected ?bool $readOnly = null;

    /**
     * Whether the field is required.
     */
    protected ?bool $required = null;

    /**
     * The number of rows for the textarea.
     */
    protected int $rows = -1;

    /**
     * Configuration for rich text editing.
     */
    protected string $richtextConfiguration = '';

    /**
     * Search configuration for the field.
     */
    protected array $search = [];

    /**
     * Size of the field (e.g., character limit).
     */
    protected int $size = -1;

    /**
     * Soft reference configuration for the field.
     */
    protected string $softref = '';

    /**
     * Wrap type for the field (e.g., soft, hard).
     */
    protected string $wrap = '';

    /**
     * Get whether autocomplete is enabled for this field.
     *
     * @return bool|null Whether autocomplete is enabled.
     */
    public function getAutocomplete(): ?bool
    {
        return $this->autocomplete;
    }

    /**
     * Get the number of columns for the textarea.
     *
     * @return int The number of columns.
     */
    public function getCols(): int
    {
        return $this->cols;
    }

    /**
     * Get the default value for the textarea.
     *
     * @return string The default value.
     */
    public function getDefault(): string
    {
        return $this->default;
    }

    /**
     * Get whether rich text editing is enabled for this field.
     *
     * @return bool|null Whether rich text editing is enabled.
     */
    public function getEnableRichtext(): ?bool
    {
        return $this->enableRichtext;
    }

    /**
     * Get whether the tabulator key is enabled for this field.
     *
     * @return bool|null Whether the tabulator key is enabled.
     */
    public function getEnableTabulator(): ?bool
    {
        return $this->enableTabulator;
    }

    /**
     * Get the evaluation type for the field.
     *
     * @return string The evaluation type.
     */
    public function getEval(): string
    {
        return $this->eval;
    }

    /**
     * Get whether a fixed font should be used for this field.
     *
     * @return bool|null Whether a fixed font is used.
     */
    public function getFixedFont(): ?bool
    {
        return $this->fixedFont;
    }

    /**
     * Get the format for the field.
     *
     * @return string The format.
     */
    public function getFormat(): string
    {
        return $this->format;
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
     * Get the "is_in" condition for the field.
     *
     * @return string The "is_in" condition.
     */
    public function getIsIn(): string
    {
        return $this->is_in;
    }

    /**
     * Get the maximum allowed value for the field.
     *
     * @return int The maximum value.
     */
    public function getMax(): int
    {
        return $this->max;
    }

    /**
     * Get the minimum allowed value for the field.
     *
     * @return int The minimum value.
     */
    public function getMin(): int
    {
        return $this->min;
    }

    /**
     * Get whether the field can be null.
     *
     * @return bool|null Whether the field can be null.
     */
    public function getNullable(): ?bool
    {
        return $this->nullable;
    }

    /**
     * Get the placeholder text for the field.
     *
     * @return string The placeholder text.
     */
    public function getPlaceholder(): string
    {
        return $this->placeholder;
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
     * Get whether the field is required.
     *
     * @return bool|null Whether the field is required.
     */
    public function getRequired(): ?bool
    {
        return $this->required;
    }

    /**
     * Get the number of rows for the textarea.
     *
     * @return int The number of rows.
     */
    public function getRows(): int
    {
        return $this->rows;
    }

    /**
     * Get the configuration for rich text editing.
     *
     * @return string The rich text configuration.
     */
    public function getRichtextConfiguration(): string
    {
        return $this->richtextConfiguration;
    }

    /**
     * Get the search configuration for the field.
     *
     * @return array The search configuration.
     */
    public function getSearch(): array
    {
        return $this->search;
    }

    /**
     * Get the size of the field.
     *
     * @return int The size.
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Get the soft reference configuration for the field.
     *
     * @return string The soft reference configuration.
     */
    public function getSoftref(): string
    {
        return $this->softref;
    }

    /**
     * Get the wrap type for the field.
     *
     * @return string The wrap type.
     */
    public function getWrap(): string
    {
        return $this->wrap;
    }

    /**
     * Set whether autocomplete is enabled for this field.
     *
     * @param bool|null $autocomplete Whether autocomplete is enabled.
     */
    public function setAutocomplete(?bool $autocomplete): void
    {
        $this->autocomplete = $autocomplete;
    }

    /**
     * Set the number of columns for the textarea.
     *
     * @param int $cols The number of columns.
     */
    public function setCols(int $cols): void
    {
        $this->cols = $cols;
    }

    /**
     * Set the default value for the textarea.
     *
     * @param string $default The default value.
     */
    public function setDefault(string $default): void
    {
        $this->default = $default;
    }

    /**
     * Set whether rich text editing is enabled for this field.
     *
     * @param bool|null $enableRichtext Whether rich text editing is enabled.
     */
    public function setEnableRichtext(?bool $enableRichtext): void
    {
        $this->enableRichtext = $enableRichtext;
    }

    /**
     * Set whether the tabulator key is enabled for this field.
     *
     * @param bool|null $enableTabulator Whether the tabulator key is enabled.
     */
    public function setEnableTabulator(?bool $enableTabulator): void
    {
        $this->enableTabulator = $enableTabulator;
    }

    /**
     * Set the evaluation type for the field.
     *
     * @param string $eval The evaluation type.
     */
    public function setEval(string $eval): void
    {
        $this->eval = $eval;
    }

    /**
     * Set whether a fixed font should be used for this field.
     *
     * @param bool|null $fixedFont Whether a fixed font is used.
     */
    public function setFixedFont(?bool $fixedFont): void
    {
        $this->fixedFont = $fixedFont;
    }

    /**
     * Set the format for the field.
     *
     * @param string $format The format.
     */
    public function setFormat(string $format): void
    {
        $this->format = $format;
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
     * Set the "is_in" condition for the field.
     *
     * @param string $isIn The "is_in" condition.
     */
    public function setIsIn(string $isIn): void
    {
        $this->is_in = $isIn;
    }

    /**
     * Set the maximum allowed value for the field.
     *
     * @param int $max The maximum value.
     */
    public function setMax(int $max): void
    {
        $this->max = $max;
    }

    /**
     * Set the minimum allowed value for the field.
     *
     * @param int $min The minimum value.
     */
    public function setMin(int $min): void
    {
        $this->min = $min;
    }

    /**
     * Set whether the field can be null.
     *
     * @param bool|null $nullable Whether the field can be null.
     */
    public function setNullable(?bool $nullable): void
    {
        $this->nullable = $nullable;
    }

    /**
     * Set the placeholder text for the field.
     *
     * @param string $placeholder The placeholder text.
     */
    public function setPlaceholder(string $placeholder): void
    {
        $this->placeholder = $placeholder;
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
     * Set whether the field is required.
     *
     * @param bool|null $required Whether the field is required.
     */
    public function setRequired(?bool $required): void
    {
        $this->required = $required;
    }

    /**
     * Set the number of rows for the textarea.
     *
     * @param int $rows The number of rows.
     */
    public function setRows(int $rows): void
    {
        $this->rows = $rows;
    }

    /**
     * Set the configuration for rich text editing.
     *
     * @param string $richtextConfiguration The rich text configuration.
     */
    public function setRichtextConfiguration(string $richtextConfiguration): void
    {
        $this->richtextConfiguration = $richtextConfiguration;
    }

    /**
     * Set the search configuration for the field.
     *
     * @param array $search The search configuration.
     */
    public function setSearch(array $search): void
    {
        $this->search = $search;
    }

    /**
     * Set the size of the field.
     *
     * @param int $size The size.
     */
    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    /**
     * Set the soft reference configuration for the field.
     *
     * @param string $softref The soft reference configuration.
     */
    public function setSoftref(string $softref): void
    {
        $this->softref = $softref;
    }

    /**
     * Set the wrap type for the field.
     *
     * @param string $wrap The wrap type.
     */
    public function setWrap(string $wrap): void
    {
        $this->wrap = $wrap;
    }

    /**
     * Merge the configuration from another TextareaFieldConfig instance.
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

        if ($foreign->getAutocomplete() !== null) {
            $this->autocomplete = $foreign->getAutocomplete();
        }

        if ($foreign->getCols() !== -1) {
            $this->cols = $foreign->getCols();
        }

        if ($foreign->getDefault() !== '') {
            $this->default = $foreign->getDefault();
        }

        if ($foreign->getEnableRichtext() !== null) {
            $this->enableRichtext = $foreign->getEnableRichtext();
        }

        if ($foreign->getEnableTabulator() !== null) {
            $this->enableTabulator = $foreign->getEnableTabulator();
        }

        if ($foreign->getEval() !== '') {
            $this->eval = $foreign->getEval();
        }

        if ($foreign->getFixedFont() !== null) {
            $this->fixedFont = $foreign->getFixedFont();
        }

        if ($foreign->getFormat() !== '') {
            $this->format = $foreign->getFormat();
        }

        if ($foreign->getIdentifier() !== '') {
            $this->identifier = $foreign->getIdentifier();
        }

        if ($foreign->getIsIn() !== '') {
            $this->is_in = $foreign->getIsIn();
        }

        if ($foreign->getMax() !== -1) {
            $this->max = $foreign->getMax();
        }

        if ($foreign->getMin() !== -1) {
            $this->min = $foreign->getMin();
        }

        if ($foreign->getNullable() !== null) {
            $this->nullable = $foreign->getNullable();
        }

        if ($foreign->getPlaceholder() !== '') {
            $this->placeholder = $foreign->getPlaceholder();
        }

        if ($foreign->getReadOnly() !== null) {
            $this->readOnly = $foreign->getReadOnly();
        }

        if ($foreign->getRequired() !== null) {
            $this->required = $foreign->getRequired();
        }

        if ($foreign->getRows() !== -1) {
            $this->rows = $foreign->getRows();
        }

        if ($foreign->getRichtextConfiguration() !== '') {
            $this->richtextConfiguration = $foreign->getRichtextConfiguration();
        }

        if ($foreign->getSearch() !== []) {
            $this->search = $foreign->getSearch();
        }

        if ($foreign->getSize() !== -1) {
            $this->size = $foreign->getSize();
        }

        if ($foreign->getSoftref() !== '') {
            $this->softref = $foreign->getSoftref();
        }

        if ($foreign->getWrap() !== '') {
            $this->wrap = $foreign->getWrap();
        }
    }

    /**
     * Keywords for rich text configuration.
     */
    const RICHTEXT_CONF_KEYWORDS = [
        'default', 'minimal', 'full'
    ];

    /**
     * Keywords for format configuration.
     */
    const FORMAT_KEYWORDS = [
        'css', 'html', 'javascript', 'php', 'typoscript', 'xml'
    ];

    /**
     * Keywords for wrap configuration.
     */
    const WRAP_KEYWORDS = [
        'virtual', 'off'
    ];

    /**
     * Validate the rich text configuration.
     *
     * @param mixed $entry The configuration entry to validate.
     * @param array $config The full configuration array.
     *
     * @throws Exception If validation fails.
     */
    private function _validateRichtextConfiguration(mixed $entry, array $config): void
    {
        $enableRichtext = $config['enableRichtext'] ?? null;

        if ($enableRichtext !== true) {
            throw new Exception(
                "'Textarea' field '$this->identifier' configuration 'richtextConfiguration' needs 'enableRichtext' set to true."
            );
        }

        if (!is_string($entry)) {
            throw new Exception(
                "'Textarea' field '$this->identifier' configuration 'richtextConfiguration' must be of type string."
            );
        }

        if (!FieldBuilder::isSurpressedWarning(179842832)) {
            if (!in_array($entry, self::RICHTEXT_CONF_KEYWORDS)) {
                throw new Exception(
                    "WARNING: 'Textarea' field '$this->identifier' configuration 'richtextConfiguration' usually contains " .
                    "one of the keywords: " .
                    implode(', ', self::RICHTEXT_CONF_KEYWORDS) . ".\n" .
                    "You can suppress this warning by adding '179842832' to 'surpressedWarnings' in 'cbconfigyaml'."
                );
            }
        }
    }

    /**
     * Validate the format configuration.
     *
     * @param mixed $entry The configuration entry to validate.
     * @param array $config The full configuration array.
     *
     * @throws Exception If validation fails.
     */
    private function _validateFormat(mixed $entry, array $config): void
    {
        $renderType = $config['renderType'];
        if ($renderType !== 'codeEditor') {
            throw new Exception(
                "'Textarea' field '$this->identifier' configuration 'format' needs 'renderType' to be set to 'codeEditor'."
            );
        }
        $this->validateKeyword($entry, self::FORMAT_KEYWORDS, $this->identifier, 'format', 'Textarea');
    }

    /**
     * Convert an array configuration into the object's properties.
     *
     * @param array $config The configuration array.
     * @param string $identifier The identifier for the field.
     * @param array $fieldProperties Additional field properties.
     */
    public function arrayToConfig(array $config, array $fieldProperties, ?array $globalConf = NULL, mixed $misc = NULL): void
    {
        $globalConf = $globalConf ?? $config;
        $this->checkRequirements($globalConf, ['identifier'], 'Textarea');
        $this->identifier = $globalConf['identifier'];
        $properties = get_object_vars($this);
        foreach ($config as $configKey => $value) {
            if ($this->isValidConfig($properties, $configKey)) {
                if ($GLOBALS['CbBuilder']['config']['Strict'] === true) {
                    switch ($configKey) {
                        case 'cols':
                            $this->validateInteger($value, $globalConf, 'cols', 'Textarea', 1, 50);
                            $this->cols = intval($value);
                            break;
                        case 'format':
                            $this->_validateFormat($value, $globalConf);
                            $this->format = $value;
                            break;
                        case 'max':
                            $this->validateInteger($value, $globalConf, 'max', 'Textarea', 1, PHP_INT_MAX, true, true, 'min');
                            $this->max = intval($value);
                            break;
                        case 'min':
                            $this->validateInteger($value, $globalConf, 'min', 'Textarea', 1, PHP_INT_MAX, true, false, 'max');
                            $this->min = intval($value);
                            break;
                        case 'richtextConfiguration':
                            $this->_validateRichtextConfiguration($value, $globalConf);
                            $this->richtextConfiguration = $value;
                            break;
                        case 'rows':
                            $this->validateInteger($value, $globalConf, 'rows', 'Textarea', 1, 20);
                            $this->rows = intval($value);
                            break;
                        case 'search':
                            $this->validateSearch($value, $this->identifier, 'Textarea');
                            $this->search = $value;
                            break;
                        case 'softref':
                            $this->validateSoftRef($value, $globalConf, 'Textarea');
                            $this->softref = $value;
                            break;
                        case 'wrap':
                            $this->validateKeyword($value, self::WRAP_KEYWORDS, $this->identifier, 'wrap', 'Textarea');
                            $this->wrap = $value;
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
                    "'Textarea' field '$identifier' configuration '$configKey' is not valid.\n" .
                    "Valid settings are: " . implode(', ', array_keys($properties))
                );
            }
        }
    }
}

/**
 * Represents a textarea field.
 */
final class TextareaField extends Field
{
    /**
     * Configuration for the textarea field.
     */
    protected TextareaFieldConfig $config;

    /**
     * Get the configuration for this textarea field.
     *
     * @return TextareaFieldConfig The field configuration.
     */
    public function getConfig(): TextareaFieldConfig
    {
        return $this->config;
    }

    /**
     * Initialize the field from an array configuration.
     *
     * @param array $field The field configuration array.
     */
    private function _arrayToField(array $field): void
    {
        $this->__arrayToField('text', $field);
        $field['table'] = $this->table;
        $field['renderType'] = $this->renderType;
        $field['useExistingField'] = $this->useExistingField;
        $field['identifier'] = $this->identifier;
        $config = new TextareaFieldConfig();
        $config->arrayToConfig($field, array_keys(get_object_vars($this)));
        $this->config = $config;
    }

    /**
     * Merge the configuration from another TextareaField instance.
     *
     * @param TextareaField $foreign The foreign field to merge.
     */
    public function mergeField(TextareaField $foreign): void
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
     * @return array The field as an array.
     */
    public function fieldToArray(): array
    {
        return $this->__fieldToArray($this->config);
    }

    /**
     * Constructor for the TextareaField.
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