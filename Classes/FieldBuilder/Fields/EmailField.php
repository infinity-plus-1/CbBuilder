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
 * Class EmailFieldConfig
 * 
 * Represents the configuration for an email field.
 */
final class EmailFieldConfig extends Config
{
    /**
     * Whether autocomplete is enabled.
     */
    protected ?bool $autocomplete = NULL;

    /**
     * The evaluation type for the field.
     */
    protected string $eval = '';

    /**
     * The mode for the field.
     */
    protected string $mode = '';

    /**
     * Whether the field is nullable.
     */
    protected ?bool $nullable = NULL;

    /**
     * The placeholder text for the field.
     */
    protected string $placeholder = '';

    /**
     * Whether the field is read-only.
     */
    protected ?bool $readOnly = NULL;

    /**
     * Whether the field is required.
     */
    protected ?bool $required = NULL;

    /**
     * The size of the field.
     */
    protected int $size = -1;

    /**
     * Get whether autocomplete is enabled.
     * 
     * @return bool|null Whether autocomplete is enabled.
     */
    public function isAutocomplete(): ?bool
    {
        return $this->autocomplete;
    }

    /**
     * Get the evaluation type.
     * 
     * @return string The evaluation type.
     */
    public function getEval(): string
    {
        return $this->eval;
    }

    /**
     * Get the mode for the field.
     * 
     * @return string The mode.
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * Get whether the field is nullable.
     * 
     * @return bool|null Whether the field is nullable.
     */
    public function isNullable(): ?bool
    {
        return $this->nullable;
    }

    /**
     * Get the placeholder text.
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
    public function isReadOnly(): ?bool
    {
        return $this->readOnly;
    }

    /**
     * Get whether the field is required.
     * 
     * @return bool|null Whether the field is required.
     */
    public function isRequired(): ?bool
    {
        return $this->required;
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
     * Set whether autocomplete is enabled.
     * 
     * @param bool|null $autocomplete Whether autocomplete should be enabled.
     */
    public function setAutocomplete(?bool $autocomplete): void
    {
        $this->autocomplete = $autocomplete;
    }

    /**
     * Set the evaluation type.
     * 
     * @param string $eval The evaluation type to set.
     */
    public function setEval(string $eval): void
    {
        $this->eval = $eval;
    }

    /**
     * Set the mode for the field.
     * 
     * @param string $mode The mode to set.
     */
    public function setMode(string $mode): void
    {
        $this->mode = $mode;
    }

    /**
     * Set whether the field is nullable.
     * 
     * @param bool|null $nullable Whether the field should be nullable.
     */
    public function setNullable(?bool $nullable): void
    {
        $this->nullable = $nullable;
    }

    /**
     * Set the placeholder text.
     * 
     * @param string $placeholder The placeholder text to set.
     */
    public function setPlaceholder(string $placeholder): void
    {
        $this->placeholder = $placeholder;
    }

    /**
     * Set whether the field is read-only.
     * 
     * @param bool|null $readOnly Whether the field should be read-only.
     */
    public function setReadOnly(?bool $readOnly): void
    {
        $this->readOnly = $readOnly;
    }

    /**
     * Set whether the field is required.
     * 
     * @param bool|null $required Whether the field should be required.
     */
    public function setRequired(?bool $required): void
    {
        $this->required = $required;
    }

    /**
     * Set the size of the field.
     * 
     * @param int $size The size to set.
     */
    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    /**
     * Merge the configuration from another EmailFieldConfig instance.
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

        if ($foreign->isAutocomplete() !== null) {
            $this->autocomplete = $foreign->isAutocomplete();
        }

        if ($foreign->getEval() !== '') {
            $this->eval = $foreign->getEval();
        }

        if ($foreign->getMode() !== '') {
            $this->mode = $foreign->getMode();
        }

        if ($foreign->isNullable() !== null) {
            $this->nullable = $foreign->isNullable();
        }

        if ($foreign->getPlaceholder() !== '') {
            $this->placeholder = $foreign->getPlaceholder();
        }

        if ($foreign->isReadOnly() !== null) {
            $this->readOnly = $foreign->isReadOnly();
        }

        if ($foreign->isRequired() !== null) {
            $this->required = $foreign->isRequired();
        }

        if ($foreign->getSize() !== -1) {
            $this->size = $foreign->getSize();
        }
    }

    /**
     * Valid evaluation keywords for email fields.
     */
    const EVAL_KEYWORDS = [
        'unique', 'uniqueInPid'
    ];

    /**
     * Validate the 'eval' configuration.
     * 
     * @param mixed $entry The 'eval' value.
     * @param array $config The configuration array.
     * 
     * @throws Exception If validation fails.
     */
    private function _validateEval(mixed $entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_string($entry)) {
            throw new Exception(
                "'Email' field '$identifier' configuration 'eval' must be of type string."
            );
        }
        if (!in_array($entry, self::EVAL_KEYWORDS)) {
            throw new Exception(
                "'Email' field '$identifier' configuration 'eval' must contain a specific keyword. " .
                "Valid keywords are " . implode(', ', self::EVAL_KEYWORDS)
            );
        }
    }

    /**
     * Validate the 'mode' configuration.
     * 
     * @param mixed $entry The 'mode' value.
     * @param array $config The configuration array.
     * 
     * @throws Exception If validation fails.
     */
    private function _validateMode(mixed $entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_string($entry)) {
            throw new Exception(
                "'Email' field '$identifier' configuration 'mode' must be of type string."
            );
        }
        if ('useOrOverridePlaceholder' !== $entry) {
            throw new Exception(
                "'Email' field '$identifier' configuration 'mode' must contain the keyword 'useOrOverridePlaceholder'."
            );
        }
        if (!isset($config['placeholder'])) {
            throw new Exception(
                "'Email' field '$identifier' configuration 'mode' must takes only effect if a placeholder is defined as well."
            );
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
        $this->checkRequirements($globalConf, [], 'Email');
        $properties = get_object_vars($this);
        foreach ($config as $configKey => $value) {
            if ($this->isValidConfig($properties, $configKey)) {
                if ($GLOBALS['CbBuilder']['config']['Strict'] === true) {
                    switch ($configKey) {
                        case 'eval':
                            $this->_validateEval($value, $globalConf);
                            $this->eval = $value;
                            break;
                        case 'mode':
                            $this->_validateMode($value, $globalConf);
                            $this->mode = $value;
                            break;
                        case 'size':
                            $this->validateInteger($value, $globalConf, 'size', 'Email', 10, 50);
                            $this->size = $value;
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
                    "'Email' field '$identifier' configuration '$configKey' is not valid.\n" .
                    "Valid settings are: " . implode(', ', array_keys($properties))
                );
            }
        }
    }

    /**
     * Convert the object's configuration to an array for element creation.
     * 
     * @return array The configuration array.
     */
    public function configToElement(): array
    {
        $properties = get_object_vars($this);
        return parent::_configToElement('email', $properties);
    }
}

/**
 * Class EmailField
 * 
 * Represents an email field.
 */
final class EmailField extends Field
{
    /**
     * The configuration for this email field.
     */
    protected EmailFieldConfig $config;

    /**
     * Get the configuration of this email field.
     * 
     * @return EmailFieldConfig The configuration.
     */
    public function getConfig(): EmailFieldConfig
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
        $this->__arrayToField('email', $field);
        $field['table'] = $this->table;
        $field['useExistingField'] = $this->useExistingField;
        $field['identifier'] = $this->identifier;
        $config = new EmailFieldConfig();
        $config->arrayToConfig($field, array_keys(get_object_vars($this)));
        $this->config = $config;
    }

    /**
     * Convert the field to an array for element creation.
     * 
     * @return array The field array.
     */
    public function fieldToElement(): array
    {
        $element = [];
        $element['config'] = $this->config->configToElement();
        return $element;
    }

    /**
     * Merge the configuration of another email field into this one.
     * 
     * @param EmailField $foreign The field to merge.
     */
    public function mergeField(EmailField $foreign): void
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
     * Constructor for the EmailField class.
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