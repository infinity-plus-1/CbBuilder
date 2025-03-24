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
 * Configuration class for password rules.
 */
final class PasswordRules extends Config
{
    /**
     * Minimum length of the password.
     */
    protected int $length = -1;

    /**
     * Whether to require lower case characters.
     */
    protected ?bool $lowerCaseCharacters = null;

    /**
     * Randomization method for the password.
     */
    protected string $random = '';

    /**
     * Whether to require special characters.
     */
    protected ?bool $specialCharacters = null;

    /**
     * Whether to require upper case characters.
     */
    protected ?bool $upperCaseCharacters = null;

    /**
     * Get the minimum length of the password.
     *
     * @return int The minimum length.
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * Get whether to require lower case characters.
     *
     * @return bool|null Whether to require lower case characters.
     */
    public function getLowerCaseCharacters(): ?bool
    {
        return $this->lowerCaseCharacters;
    }

    /**
     * Get the randomization method for the password.
     *
     * @return string The randomization method.
     */
    public function getRandom(): string
    {
        return $this->random;
    }

    /**
     * Get whether to require special characters.
     *
     * @return bool|null Whether to require special characters.
     */
    public function getSpecialCharacters(): ?bool
    {
        return $this->specialCharacters;
    }

    /**
     * Get whether to require upper case characters.
     *
     * @return bool|null Whether to require upper case characters.
     */
    public function getUpperCaseCharacters(): ?bool
    {
        return $this->upperCaseCharacters;
    }

    /**
     * Set the minimum length of the password.
     *
     * @param int $length The minimum length to set.
     */
    public function setLength(int $length): void
    {
        $this->length = $length;
    }

    /**
     * Set whether to require lower case characters.
     *
     * @param bool|null $lowerCaseCharacters Whether to require lower case characters.
     */
    public function setLowerCaseCharacters(?bool $lowerCaseCharacters): void
    {
        $this->lowerCaseCharacters = $lowerCaseCharacters;
    }

    /**
     * Set the randomization method for the password.
     *
     * @param string $random The randomization method to set.
     */
    public function setRandom(string $random): void
    {
        $this->random = $random;
    }

    /**
     * Set whether to require special characters.
     *
     * @param bool|null $specialCharacters Whether to require special characters.
     */
    public function setSpecialCharacters(?bool $specialCharacters): void
    {
        $this->specialCharacters = $specialCharacters;
    }

    /**
     * Set whether to require upper case characters.
     *
     * @param bool|null $upperCaseCharacters Whether to require upper case characters.
     */
    public function setUpperCaseCharacters(?bool $upperCaseCharacters): void
    {
        $this->upperCaseCharacters = $upperCaseCharacters;
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

        if ($foreign->getLength() >= 0) {
            $this->length = $foreign->getLength();
        }

        if ($foreign->getLowerCaseCharacters() !== null) {
            $this->lowerCaseCharacters = $foreign->getLowerCaseCharacters();
        }

        if ($foreign->getRandom() !== '') {
            $this->random = $foreign->getRandom();
        }

        if ($foreign->getSpecialCharacters() !== null) {
            $this->specialCharacters = $foreign->getSpecialCharacters();
        }

        if ($foreign->getUpperCaseCharacters() !== null) {
            $this->upperCaseCharacters = $foreign->getUpperCaseCharacters();
        }
    }

    /**
     * Available randomization keywords for the password.
     */
    const RANDOM_KEYWORDS = [
        'base64', 'hex'
    ];

    /**
     * Validate the randomization method for the password.
     *
     * @param string $entry The randomization method to validate.
     * @param array $identifier The identifier array.
     *
     * @throws Exception If the randomization method is invalid.
     */
    private function _validateRandom(string $entry, array $identifier): void
    {
        $identifier = $identifier['identifier'];
        if (!is_string($entry)) {
            throw new Exception(
                "'Password' field '$identifier' configuration 'passwordGenerator['options']['passwordRules']['random']' " .
                "must be of type string. Fix:\n" .
                "fieldControl:\n  passwordGenerator:\n    renderType: passwordGenerator\n    options:\n      title: testTitle\n" .
                "      allowEdit: false\n      passwordRules:\n        length: 8\n        random: base64\n" .
                "        specialCharacters: true\n        lowerCaseCharacters: true\n        upperCaseCharacters: true"
            );
        }

        if (!in_array($entry, self::RANDOM_KEYWORDS)) {
            throw new Exception(
                "'Password' field '$identifier' configuration 'passwordGenerator['options']['passwordRules']['random']' " .
                "must contain a specific keyword. " .
                "Valid keywords are: " . implode(', ', self::RANDOM_KEYWORDS) . "\nFix:\n" .
                "fieldControl:\n  passwordGenerator:\n    renderType: passwordGenerator\n    options:\n      title: testTitle\n" .
                "      allowEdit: false\n      passwordRules:\n        length: 8\n        random: base64\n" .
                "        specialCharacters: true\n        lowerCaseCharacters: true\n        upperCaseCharacters: true"
            );
        }
    }

    /**
     * Convert an array configuration to the object's properties.
     *
     * @param array $config The configuration array.
     * @param array $identifier The identifier array.
     */
    public function arrayToConfig(array $config, array $fieldProperties, ?array $globalConf = NULL, mixed $misc = NULL): void
    {
        $properties = get_object_vars($this);
        foreach ($config as $configKey => $value) {
            if ($this->isValidConfig($properties, $configKey)) {
                switch ($configKey) {
                    case 'length':
                        $this->validateInteger(
                            $value,
                            $globalConf,
                            "passwordGenerator['options']['passwordRules']['length']",
                            'Password',
                            1,
                            PHP_INT_MAX
                        );
                        $this->length = intval($value);
                        break;
                    case 'random':
                        $this->_validateRandom($value, $globalConf);
                        $this->random = $value;
                        break;
                    default:
                        $this->$configKey = $value;
                        break;
                }
            } elseif (!in_array($configKey, $properties)) {
                $identifier = $globalConf['identifier'];
                throw new Exception(
                    "'Password' field '$identifier' configuration 'passwordGenerator['options']['passwordRules']['$configKey']' " .
                    "is not valid.\nValid settings are: " . implode(', ', array_keys($properties))
                );
            }
        }
    }
}

/**
 * Configuration class for password generator options.
 */
final class PasswordGeneratorOptionsConfig extends Config
{
    /**
     * Title for the password generator.
     */
    protected string $title = '';

    /**
     * Whether to allow editing of the password.
     */
    protected ?bool $allowEdit = null;

    /**
     * Password rules configuration.
     */
    protected ?PasswordRules $passwordRules = null;

    /**
     * Get the title for the password generator.
     *
     * @return string The title.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get whether to allow editing of the password.
     *
     * @return bool|null Whether editing is allowed.
     */
    public function getAllowEdit(): ?bool
    {
        return $this->allowEdit;
    }

    /**
     * Get the password rules configuration.
     *
     * @return PasswordRules|null The password rules.
     */
    public function getPasswordRules(): ?PasswordRules
    {
        return $this->passwordRules;
    }

    /**
     * Set the title for the password generator.
     *
     * @param string $title The title to set.
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Set whether to allow editing of the password.
     *
     * @param bool|null $allowEdit Whether editing is allowed.
     */
    public function setAllowEdit(?bool $allowEdit): void
    {
        $this->allowEdit = $allowEdit;
    }

    /**
     * Set the password rules configuration.
     *
     * @param PasswordRules|null $passwordRules The password rules to set.
     */
    public function setPasswordRules(?PasswordRules $passwordRules): void
    {
        $this->passwordRules = $passwordRules;
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

        if ($foreign->getTitle() !== '') {
            $this->title = $foreign->getTitle();
        }

        if ($foreign->getAllowEdit() !== null) {
            $this->allowEdit = $foreign->getAllowEdit();
        }

        if ($foreign->getPasswordRules() !== null) {
            if ($this->passwordRules !== null) {
                $this->passwordRules->mergeConfig($foreign->getPasswordRules());
            } else {
                $this->passwordRules = $foreign->getPasswordRules();
            }
        }
    }

    /**
     * Validate the password rules configuration.
     *
     * @param mixed $entry The password rules to validate.
     * @param array $identifier The identifier array.
     *
     * @throws Exception If the password rules are invalid.
     */
    private function _validatePasswordRules(mixed $entry, array $identifier): void
    {
        $identifier = $identifier['identifier'];
        if (!is_array($entry)) {
            throw new Exception(
                "'Password' field '$identifier' configuration 'passwordGenerator['options']['passwordRules']' " .
                "must be of type array. Fix:\n" .
                "fieldControl:\n  passwordGenerator:\n    renderType: passwordGenerator\n    options:\n      title: testTitle\n" .
                "      allowEdit: false\n      passwordRules:\n        length: 8\n        random: base64\n" .
                "        specialCharacters: true\n        lowerCaseCharacters: true\n        upperCaseCharacters: true"
            );
        }
    }

    /**
     * Convert an array configuration to the object's properties.
     *
     * @param array $config The configuration array.
     * @param array $identifier The identifier array.
     */
    public function arrayToConfig(array $config, array $fieldProperties, ?array $globalConf = NULL, mixed $misc = NULL): void
    {
        $properties = get_object_vars($this);
        foreach ($config as $configKey => $value) {
            if ($this->isValidConfig($properties, $configKey)) {
                switch ($configKey) {
                    case 'passwordRules':
                        $this->_validatePasswordRules($value, $globalConf);
                        $this->passwordRules = new PasswordRules();
                        $this->passwordRules->arrayToConfig($value, $fieldProperties, $globalConf);
                        break;
                    default:
                        $this->$configKey = $value;
                        break;
                }
            } elseif (!in_array($configKey, $properties)) {
                $identifier = $globalConf['identifier'];
                throw new Exception(
                    "'Password' field '$identifier' configuration 'passwordGenerator['options']['$configKey']' is not valid.\n" .
                    "Valid settings are: " . implode(', ', array_keys($properties))
                );
            }
        }
    }
}

/**
 * Configuration class for password generator.
 */
final class PasswordGeneratorConfig extends Config
{
    /**
     * Render type for the password generator.
     */
    protected string $renderType = 'passwordGenerator';

    /**
     * Options for the password generator.
     */
    protected ?PasswordGeneratorOptionsConfig $options = null;

    /**
     * Get the options for the password generator.
     *
     * @return PasswordGeneratorOptionsConfig|null The options.
     */
    public function getOptions(): ?PasswordGeneratorOptionsConfig
    {
        return $this->options;
    }

    /**
     * Set the options for the password generator.
     *
     * @param PasswordGeneratorOptionsConfig|null $options The options to set.
     */
    public function setOptions(?PasswordGeneratorOptionsConfig $options): void
    {
        $this->options = $options;
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

        if ($foreign->getRenderType() !== '') {
            $this->renderType = $foreign->getRenderType();
        }

        if ($foreign->getOptions() !== null) {
            if ($this->options !== null) {
                $this->options->mergeConfig($foreign->getOptions());
            } else {
                $this->options = $foreign->getOptions();
            }
        }
    }

    /**
     * Validate the password generator options.
     *
     * @param mixed $entry The options to validate.
     * @param array $identifier The identifier array.
     *
     * @throws Exception If the options are invalid.
     */
    private function _validatePasswordGeneratorOptions(mixed $entry, array $identifier): void
    {
        $identifier = $identifier['identifier'];
        if (!is_array($entry)) {
            throw new Exception(
                "'Password' field '$identifier' configuration 'passwordGenerator['options']' must be of type array. Fix:\n" .
                "fieldControl:\n  passwordGenerator:\n    renderType: passwordGenerator\n    options:\n      title: testTitle\n" .
                "      allowEdit: false\n      passwordRules:\n        length: 8\n        random: base64\n" .
                "        specialCharacters: true\n        lowerCaseCharacters: true\n        upperCaseCharacters: true"
            );
        }
    }

    /**
     * Convert an array configuration to the object's properties.
     *
     * @param array $config The configuration array.
     * @param array $identifier The identifier array.
     */
    public function arrayToConfig(array $config, array $fieldProperties, ?array $globalConf = NULL, mixed $misc = NULL): void
    {
        $properties = get_object_vars($this);
        foreach ($config as $configKey => $value) {
            if ($this->isValidConfig($properties, $configKey)) {
                switch ($configKey) {
                    case 'options':
                        $this->_validatePasswordGeneratorOptions($value, $globalConf);
                        $this->options = new PasswordGeneratorOptionsConfig();
                        $this->options->arrayToConfig($value, $fieldProperties, $globalConf);
                        break;
                    default:
                        $this->$configKey = $value;
                        break;
                }
            } elseif (!in_array($configKey, $properties)) {
                $identifier = $globalConf['identifier'];
                throw new Exception(
                    "'Password' field '$identifier' configuration 'passwordGenerator['$configKey']' is not valid.\n" .
                    "Valid settings are: " . implode(', ', array_keys($properties))
                );
            }
        }
    }
}

/**
 * Configuration class for password field.
 */
final class PasswordFieldConfig extends Config
{
    /**
     * Whether autocomplete is enabled.
     */
    protected ?bool $autocomplete = null;

    /**
     * Default value for the field.
     */
    protected string $default = '';

    /**
     * Whether the password should be hashed.
     */
    protected ?bool $hashed = null;

    /**
     * Mode for the field.
     */
    protected string $mode = '';

    /**
     * Whether the field is nullable.
     */
    protected ?bool $nullable = null;

    /**
     * Password generator configuration.
     */
    protected ?PasswordGeneratorConfig $passwordGenerator = null;

    /**
     * Password policy for the field.
     */
    protected string $passwordPolicy = '';

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
     * Size of the field.
     */
    protected int $size = -1;

    /**
     * Get whether autocomplete is enabled.
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
     * Get whether the password should be hashed.
     *
     * @return bool|null Whether the password should be hashed.
     */
    public function getHashed(): ?bool
    {
        return $this->hashed;
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
    public function getNullable(): ?bool
    {
        return $this->nullable;
    }

    /**
     * Get the password generator configuration.
     *
     * @return PasswordGeneratorConfig|null The password generator configuration.
     */
    public function getPasswordGenerator(): ?PasswordGeneratorConfig
    {
        return $this->passwordGenerator;
    }

    /**
     * Get the password policy for the field.
     *
     * @return string The password policy.
     */
    public function getPasswordPolicy(): string
    {
        return $this->passwordPolicy;
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
     * Set whether the password should be hashed.
     *
     * @param bool|null $hashed Whether the password should be hashed.
     */
    public function setHashed(?bool $hashed): void
    {
        $this->hashed = $hashed;
    }

    /**
     * Set the mode for the field.
     *
     * @param string $mode The mode.
     */
    public function setMode(string $mode): void
    {
        $this->mode = $mode;
    }

    /**
     * Set whether the field is nullable.
     *
     * @param bool|null $nullable Whether the field is nullable.
     */
    public function setNullable(?bool $nullable): void
    {
        $this->nullable = $nullable;
    }

    /**
     * Set the password generator configuration.
     *
     * @param PasswordGeneratorConfig|null $passwordGenerator The password generator configuration.
     */
    public function setPasswordGenerator(?PasswordGeneratorConfig $passwordGenerator): void
    {
        $this->passwordGenerator = $passwordGenerator;
    }

    /**
     * Set the password policy for the field.
     *
     * @param string $passwordPolicy The password policy.
     */
    public function setPasswordPolicy(string $passwordPolicy): void
    {
        $this->passwordPolicy = $passwordPolicy;
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
     * Set the size of the field.
     *
     * @param int $size The size.
     */
    public function setSize(int $size): void
    {
        $this->size = $size;
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

        if ($foreign->getAutocomplete() !== null) {
            $this->autocomplete = $foreign->getAutocomplete();
        }

        if ($foreign->getDefault() !== '') {
            $this->default = $foreign->getDefault();
        }

        if ($foreign->getHashed() !== null) {
            $this->hashed = $foreign->getHashed();
        }

        if ($foreign->getMode() !== '') {
            $this->mode = $foreign->getMode();
        }

        if ($foreign->getNullable() !== null) {
            $this->nullable = $foreign->getNullable();
        }

        if ($foreign->getPasswordGenerator() !== null) {
            if ($this->passwordGenerator !== null) {
                $this->passwordGenerator->mergeConfig($foreign->getPasswordGenerator());
            } else {
                $this->passwordGenerator = $foreign->getPasswordGenerator();
            }
        }

        if ($foreign->getPasswordPolicy() !== '') {
            $this->passwordPolicy = $foreign->getPasswordPolicy();
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

        if ($foreign->getSize() >= 0) {
            $this->size = $foreign->getSize();
        }
    }

    /**
     * Validate the password generator configuration.
     *
     * @param mixed $entry The password generator configuration to validate.
     * @param array $config The configuration array.
     *
     * @throws Exception If the password generator configuration is invalid.
     */
    private function _validatePasswordGenerator(mixed $entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_array($entry)) {
            throw new Exception(
                "'Password' field '$identifier' configuration 'passwordGenerator' must be of type array."
            );
        }
    }

    /**
     * Convert an array configuration to the object's properties.
     *
     * @param array $config The configuration array.
     * @param array $fieldProperties The field properties.
     */
    public function arrayToConfig(array $config, array $fieldProperties, ?array $globalConf = NULL, mixed $misc = NULL): void
    {
        $globalConf = $globalConf ?? $config;
        $this->checkRequirements($globalConf, ['identifier'], 'Password');
        $properties = get_object_vars($this);
        foreach ($config as $configKey => $value) {
            if ($this->isValidConfig($properties, $configKey)) {
                if ($GLOBALS['CbBuilder']['config']['Strict'] === true) {
                    switch ($configKey) {
                        case 'mode':
                            $this->validateMode($value, $globalConf, 'Password');
                            $this->mode = $value;
                            break;
                        case 'passwordGenerator':
                            $this->_validatePasswordGenerator($value, $globalConf);
                            $this->passwordGenerator = new PasswordGeneratorConfig();
                            $this->passwordGenerator->arrayToConfig($value, $fieldProperties, $globalConf);
                            break;
                        case 'size':
                            $this->validateInteger($value, $globalConf, 'size', 'Password', 10, 50);
                            $this->size = intval($value);
                            break;
                        default:
                            $this->addGenericConfig($configKey, $value, $globalConf, $this, $fieldProperties);
                            break;
                    }
                } else {
                    $this->$configKey = $value;
                }
            } elseif (!in_array($configKey, $fieldProperties)) {
                $identifier = $config['identifier'];
                throw new Exception(
                    "'Password' field '$identifier' configuration '$configKey' is not valid.\n" .
                    "Valid settings are: " . implode(', ', array_keys($properties))
                );
            }
        }
    }
}

/**
 * Class representing a password field.
 */
final class PasswordField extends Field
{
    /**
     * Configuration for the password field.
     */
    protected PasswordFieldConfig $config;

    /**
     * Get the configuration for the password field.
     *
     * @return PasswordFieldConfig The configuration.
     */
    public function getConfig(): PasswordFieldConfig
    {
        return $this->config;
    }

    /**
     * Convert an array to a password field.
     *
     * @param array $field The array to convert.
     */
    private function _arrayToField(array $field): void
    {
        $this->__arrayToField('password', $field);
        $field['table'] = $this->table;
        $field['useExistingField'] = $this->useExistingField;
        $field['identifier'] = $this->identifier;
        $config = new PasswordFieldConfig();
        $config->arrayToConfig($field, array_keys(get_object_vars($this)));
        $this->config = $config;
    }

    /**
     * Merge the current field with another field.
     *
     * @param PasswordField $foreign The foreign field to merge.
     */
    public function mergeField(PasswordField $foreign): void
    {
        $this->config->mergeConfig($foreign->getConfig());
        $this->mergeFields($foreign);
    }

    /**
     * Parse the field based on the given mode and level.
     *
     * @param int $mode The mode to parse with.
     * @param int $level The level to parse with.
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
     * Constructor for the password field.
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