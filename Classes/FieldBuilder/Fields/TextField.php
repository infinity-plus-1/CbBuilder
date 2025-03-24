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

use DS\CbBuilder\Utility\SimpleDatabaseQuery;
use Exception;
use InvalidArgumentException;

/**
 * Configuration class for text field settings.
 */
final class TextFieldConfig extends Config
{
    /**
     * Whether autocomplete is enabled for the field.
     */
    protected ?bool $autocomplete = null;

    /**
     * Default value for the field.
     */
    protected string $default = '';

    /**
     * Evaluation rule for the field (e.g., email, url).
     */
    protected string $eval = '';

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
     * Mode of the field (e.g., text, password).
     */
    protected string $mode = '';

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
     * Search settings for the field.
     */
    protected array $search = [];

    /**
     * Size of the field.
     */
    protected int $size = -1;

    /**
     * Soft reference for the field.
     */
    protected string $softref = '';

    /**
     * Value picker settings for the field.
     */
    protected array $valuePicker = [];

    /**
     * Get whether autocomplete is enabled for the field.
     *
     * @return bool|null Whether autocomplete is enabled.
     */
    public function getAutocomplete(): ?bool
    {
        return $this->autocomplete;
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
     * Get the evaluation rule for the field.
     *
     * @return string The evaluation rule.
     */
    public function getEval(): string
    {
        return $this->eval;
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
     * @return int The maximum allowed value.
     */
    public function getMax(): int
    {
        return $this->max;
    }

    /**
     * Get the minimum allowed value for the field.
     *
     * @return int The minimum allowed value.
     */
    public function getMin(): int
    {
        return $this->min;
    }

    /**
     * Get the mode of the field.
     *
     * @return string The mode of the field.
     */
    public function getMode(): string
    {
        return $this->mode;
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
     * Get the search settings for the field.
     *
     * @return array The search settings.
     */
    public function getSearch(): array
    {
        return $this->search;
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
     * Get the soft reference for the field.
     *
     * @return string The soft reference.
     */
    public function getSoftref(): string
    {
        return $this->softref;
    }

    /**
     * Get the value picker settings for the field.
     *
     * @return array The value picker settings.
     */
    public function getValuePicker(): array
    {
        return $this->valuePicker;
    }

    /**
     * Set whether autocomplete is enabled for the field.
     *
     * @param bool|null $autocomplete Whether autocomplete is enabled.
     */
    public function setAutocomplete(?bool $autocomplete): void
    {
        $this->autocomplete = $autocomplete;
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
     * Set the evaluation rule for the field.
     *
     * @param string $eval The evaluation rule.
     */
    public function setEval(string $eval): void
    {
        $this->eval = $eval;
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
     * @param int $max The maximum allowed value.
     */
    public function setMax(int $max): void
    {
        $this->max = $max;
    }

    /**
     * Set the minimum allowed value for the field.
     *
     * @param int $min The minimum allowed value.
     */
    public function setMin(int $min): void
    {
        $this->min = $min;
    }

    /**
     * Set the mode of the field.
     *
     * @param string $mode The mode of the field.
     */
    public function setMode(string $mode): void
    {
        $this->mode = $mode;
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
     * Set the search settings for the field.
     *
     * @param array $search The search settings.
     */
    public function setSearch(array $search): void
    {
        $this->search = $search;
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
     * Set the soft reference for the field.
     *
     * @param string $softref The soft reference.
     */
    public function setSoftref(string $softref): void
    {
        $this->softref = $softref;
    }

    /**
     * Set the value picker settings for the field.
     *
     * @param array $valuePicker The value picker settings.
     */
    public function setValuePicker(array $valuePicker): void
    {
        $this->valuePicker = $valuePicker;
    }

    /**
     * Merge the configuration from another instance into this one.
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

        if ($foreign->getDefault() !== '') {
            $this->default = $foreign->getDefault();
        }

        if ($foreign->getEval() !== '') {
            $this->eval = $foreign->getEval();
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

        if ($foreign->getMode() !== '') {
            $this->mode = $foreign->getMode();
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

        if ($foreign->getSearch() !== []) {
            $this->search = $foreign->getSearch();
        }

        if ($foreign->getSize() !== 30) {
            $this->size = $foreign->getSize();
        }

        if ($foreign->getSoftref() !== '') {
            $this->softref = $foreign->getSoftref();
        }

        if ($foreign->getValuePicker() !== []) {
            $this->valuePicker = $foreign->getValuePicker();
        }
    }

    /**
     * List of valid keywords for the 'eval' field.
     */
    const VALID_EVAL_KEYWORDS = [
        'alpha', 'alphanum', 'alphanum_x', 'domainname',
        'is_in', 'lower', 'md5', 'nospace', 'num', 'trim',
        'unique', 'uniqueInPid', 'upper', 'year'
    ];

    /**
     * Validate the 'eval' field configuration.
     *
     * @param string $entry The 'eval' field value to validate.
     * @param array $config The field configuration.
     *
     * @throws Exception If the validation fails.
     */
    private function _validateEval($entry, $config): void
    {
        $identifier = $config['identifier'];
        $keywords = [];
        if (is_string($entry) && $entry !== '') {
            $keywords = array_map('trim', explode(',', $entry));
        }
        foreach ($keywords as $keyword) {
            if ($keyword === 'is_in' && !isset($config['is_in'])) {
                throw new Exception(
                    "The 'Text' field '$identifier' configuration 'eval' contains the keyword 'is_in', but 'is_in' is not declared in the Text field properties. Fix:\n...\ntype: Text\nis_in: abc123\neval: is_in\n..."
                );
            }
            if (!in_array($keyword, self::VALID_EVAL_KEYWORDS, true)) {
                if (!str_contains($keyword, '::class') || !str_contains($keyword, '->')) {
                    throw new Exception(
                        "The 'Text' field '$identifier' configuration 'eval' must contain one of these keywords: 'alpha', 'alphanum', 'alphanum_x', 'domainname', 'is_in', 'lower', 'md5', 'nospace', 'num', 'trim', 'unique', 'uniqueInPid', 'upper', 'year' or a path to a custom class."
                    );
                }
            }
        }
    }

    /**
     * Validate the length of a field against the database schema.
     *
     * @param array $config The field configuration.
     * @param int $length The length to validate.
     * @param string $field The field name.
     *
     * @throws Exception If the validation fails.
     */
    private function _validateLength(array $config, int $length, string $field): void
    {
        $identifier = $config['identifier'];
        $properties = SimpleDatabaseQuery::getFieldProperties($config['identifier'], $config['table']);
        if (!empty($properties)) { // Does not exist, will be created with suitable length later.
            if ($length > $properties[0]['CHARACTER_MAXIMUM_LENGTH'] && strtolower($properties[0]['DATA_TYPE']) === 'varchar') {
                throw new Exception(
                    "The 'Text' field '$identifier' configuration '$field' exceeds the VARCHAR's set length."
                );
            }
        }
    }

    /**
     * Validate the 'mode' field configuration.
     *
     * @param string $entry The 'mode' field value to validate.
     * @param array $config The field configuration.
     *
     * @throws Exception If the validation fails.
     */
    private function _validateMode($entry, $config): void
    {
        $identifier = $config['identifier'];
        if ($entry !== 'useOrOverridePlaceholder') {
            throw new Exception(
                "The 'Text' field '$identifier' configuration 'mode' must contain the value 'useOrOverridePlaceholder'."
            );
        }
        if (!isset($config['placeholder'])) {
            throw new Exception(
                "The 'Text' field '$identifier' configuration 'mode' needs to have 'placeholder' set."
            );
        }
    }

    /**
     * Validate the 'search' field configuration.
     *
     * @param array $entry The 'search' field value to validate.
     * @param array $config The field configuration.
     *
     * @throws Exception If the validation fails.
     */
    private function _validateSearch($entry, $config): void
    {
        $identifier = $config['identifier'];
        if (!is_array($entry)) {
            throw new Exception(
                "The 'Text' field '$identifier' configuration 'search' must be of type array."
            );
        }
        foreach ($entry as $key => $value) {
            switch ($key) {
                case 'pidonly':
                    if (!is_bool($value)) {
                        throw new Exception(
                            "The 'Text' field '$identifier' configuration 'search['pidonly']' must be of type boolean."
                        );
                    }
                    break;
                case 'case':
                    if (!is_bool($value)) {
                        throw new Exception(
                            "The 'Text' field '$identifier' configuration 'search['case']' must be of type boolean."
                        );
                    }
                    break;
                case 'andWhere':
                    if (!is_string($value)) {
                        throw new Exception(
                            "The 'Text' field '$identifier' configuration 'search['andWhere']' must be of type string."
                        );
                    }
                    break;
                default:
                    throw new Exception(
                        "The 'Text' field '$identifier' configuration 'search' must either contain 'pidonly', 'case' or 'andWhere'."
                    );
                    break;
            }
        }
    }

    /**
     * Validate the 'softref' field configuration.
     *
     * @param string $entry The 'softref' field value to validate.
     * @param array $config The field configuration.
     *
     * @throws Exception If the validation fails.
     */
    private function _validateSoftRef($entry, $config): void
    {
        $identifier = $config['identifier'];
        $matches = [];
        preg_match("/\w+(\\[\w+(;\w+)*\\])?(,\w+(\\[\w+(;\w+)*\\])?)*/", $entry, $matches);
        if ($matches[0] !== $entry) {
            throw new Exception(
                "The 'Text' field '$identifier' configuration 'softref': Error in syntax. Syntax should look like: key1,key2[parameter1;parameter2;...],..."
            );
        }
    }

    /**
     * Validate the 'valuePicker' field configuration.
     *
     * @param mixed $entry The 'valuePicker' field value to validate.
     * @param array $config The field configuration.
     *
     * @throws Exception If the validation fails.
     */
    private function _validateValuePicker($entry, $config): void
    {
        $identifier = $config['identifier'];
        if (!is_array($entry)) {
            throw new Exception(
                "'Number' field '$identifier' configuration 'valuePicker' must be of type array."
            );
        }
        if (array_key_exists('mode', $entry)) {
            if ('append' === $entry['mode'] || 'prepend' === $entry['mode'] || '' === $entry['mode']) {
                $this->valuePicker['mode'] = $entry['mode'];
            } else {
                throw new Exception(
                    "'Number' field '$identifier' configuration 'valuePicker['mode']' must either contain the keyword 'append' or 'prepend'."
                );
            }
        }
        if (isset($entry['items']) && is_array($entry['items'])) {
            $this->valuePicker['items'] = [];
            foreach ($entry['items'] as $item) {
                if (is_array($item) && count($item) === 1) {
                    if (is_string(array_key_first($item))) {
                        if (is_string(current($item)) || is_numeric(current($item)) || is_bool(current($item))) {
                            $this->valuePicker['items'][] = $item;
                        } else {
                            throw new Exception(
                                "'Number' field '$identifier' configuration 'valuePicker['items'][0-n][key => value]' must have a string, numeric, or boolean value."
                            );
                        }
                    } else {
                        throw new Exception(
                            "'Number' field '$identifier' configuration 'valuePicker['items'][0-n][key => value]' must have a string key."
                        );
                    }
                } else {
                    throw new Exception(
                        "'Number' field '$identifier' configuration 'valuePicker['items'][0-n]' must be an array with exactly one entry. Example:\n" .
                        "valuePicker:\n  items:\n    - key1: value1\n    - key2: 2\n    - key3: 3.0\n    - key4: true"
                    );
                }
            }
        }
    }

    /**
     * Convert an array configuration into object properties.
     *
     * @param array $config The configuration array.
     * @param array $fieldProperties The valid field properties.
     *
     * @throws Exception If the validation fails or an invalid configuration key is provided.
     */
    public function arrayToConfig(array $config, array $fieldProperties, ?array $globalConf = NULL, mixed $misc = NULL): void
    {
        $globalConf = $globalConf ?? $config;
        $this->checkRequirements($globalConf, [], 'Text');
        $properties = get_object_vars($this);

        foreach ($config as $configKey => $value) {
            if ($this->isValidConfig($properties, $configKey)) {
                if ($GLOBALS['CbBuilder']['config']['Strict'] === true) {
                    switch ($configKey) {
                        case 'default':
                            if ($GLOBALS['CbBuilder']['config']['propertySpecific']['Text']['default']['allowLongerThanMaxDbLen'] === false) {
                                $this->_validateLength($globalConf, strlen($value), 'default');
                            }
                            $this->default = $value;
                            break;
                        case 'eval':
                            $this->_validateEval($value, $globalConf);
                            $this->eval = $value;
                            break;
                        case 'max':
                            $this->_validateLength($globalConf, $value, 'max');
                            $this->max = intval($value);
                            break;
                        case 'mode':
                            $this->_validateMode($value, $globalConf);
                            $this->mode = $value;
                            break;
                        case 'search':
                            $this->_validateSearch($value, $globalConf);
                            $this->search = $value;
                            break;
                        case 'size':
                            $this->validateInteger($value, $globalConf, 'size', 'Text', 10, 50);
                            $this->size = intval($value);
                            break;
                        case 'softref':
                            $value = str_replace(' ', '', $value);
                            $this->_validateSoftRef($value, $globalConf);
                            $this->softref = $value;
                            break;
                        case 'valuePicker':
                            $this->_validateValuePicker($value, $globalConf);
                            break;
                        default:
                            $this->addGenericConfig($configKey, $value, $globalConf, $this, $fieldProperties);
                            break;
                    }
                } else {
                    $this->$configKey = $value;
                }
            } elseif (!in_array($configKey, $fieldProperties)) {
                throw new Exception(
                    "'Text' field '{$globalConf['identifier']}' configuration '$configKey' is not valid.\n" .
                    "Valid settings are: " . implode(', ', array_keys($properties))
                );
            }
        }
    }
}

/**
 * Represents a text field with its configuration.
 */
final class TextField extends Field
{
    /**
     * Configuration for the text field.
     */
    protected TextFieldConfig $config;

    /**
     * Get the configuration of the text field.
     *
     * @return TextFieldConfig The text field configuration.
     */
    public function getConfig(): TextFieldConfig
    {
        return $this->config;
    }

    /**
     * Convert an array representation into a text field object.
     *
     * @param array $field The array representation of the field.
     */
    private function _arrayToField(array $field): void
    {
        $this->__arrayToField('input', $field);
        $field['table'] = $this->table;
        $field['useExistingField'] = $this->useExistingField;
        $field['identifier'] = $this->identifier;
        $config = new TextFieldConfig();
        $config->arrayToConfig($field, array_keys(get_object_vars($this)));
        $this->config = $config;
    }

    /**
     * Merge another text field into this one.
     *
     * @param TextField $foreign The text field to merge.
     */
    public function mergeField(TextField $foreign): void
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
     * Constructor for the text field.
     *
     * @param array $field The array representation of the field.
     * @param string $table The table name associated with the field.
     */
    public function __construct(array $field, string $table)
    {
        $this->table = $table;
        $this->_arrayToField($field);
    }
}