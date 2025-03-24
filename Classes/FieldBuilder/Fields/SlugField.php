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
 * Configuration class for slug field generator options.
 */
final class SlugFieldGeneratorOptionsConfig extends Config
{
    /**
     * Array of fields used for generating the slug.
     */
    protected array $fields = [];

    /**
     * Separator used between fields in the slug.
     */
    protected string $fieldSeparator = '';

    /**
     * Whether to prefix the parent page slug.
     */
    protected ?bool $prefixParentPageSlug = null;

    /**
     * Array of replacements for slug generation.
     */
    protected array $replacements = [];

    /**
     * Array of post-modifiers for slug generation.
     */
    protected array $postModifiers = [];

    /**
     * Get the array of fields used for generating the slug.
     *
     * @return array The list of fields.
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Get the separator used between fields in the slug.
     *
     * @return string The field separator.
     */
    public function getFieldSeparator(): string
    {
        return $this->fieldSeparator;
    }

    /**
     * Get whether to prefix the parent page slug.
     *
     * @return bool|null Whether to prefix the parent page slug.
     */
    public function getPrefixParentPageSlug(): ?bool
    {
        return $this->prefixParentPageSlug;
    }

    /**
     * Get the array of replacements for slug generation.
     *
     * @return array The list of replacements.
     */
    public function getReplacements(): array
    {
        return $this->replacements;
    }

    /**
     * Get the array of post-modifiers for slug generation.
     *
     * @return array The list of post-modifiers.
     */
    public function getPostModifiers(): array
    {
        return $this->postModifiers;
    }

    /**
     * Set the array of fields used for generating the slug.
     *
     * @param array $fields The list of fields.
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    /**
     * Set the separator used between fields in the slug.
     *
     * @param string $fieldSeparator The field separator.
     */
    public function setFieldSeparator(string $fieldSeparator): void
    {
        $this->fieldSeparator = $fieldSeparator;
    }

    /**
     * Set whether to prefix the parent page slug.
     *
     * @param bool|null $prefixParentPageSlug Whether to prefix the parent page slug.
     */
    public function setPrefixParentPageSlug(?bool $prefixParentPageSlug): void
    {
        $this->prefixParentPageSlug = $prefixParentPageSlug;
    }

    /**
     * Set the array of replacements for slug generation.
     *
     * @param array $replacements The list of replacements.
     */
    public function setReplacements(array $replacements): void
    {
        $this->replacements = $replacements;
    }

    /**
     * Set the array of post-modifiers for slug generation.
     *
     * @param array $postModifiers The list of post-modifiers.
     */
    public function setPostModifiers(array $postModifiers): void
    {
        $this->postModifiers = $postModifiers;
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

        if ($foreign->getFields() !== []) {
            $this->fields = $foreign->getFields();
        }

        if ($foreign->getFieldSeparator() !== '') {
            $this->fieldSeparator = $foreign->getFieldSeparator();
        }

        if ($foreign->getPrefixParentPageSlug() !== null) {
            $this->prefixParentPageSlug = $foreign->getPrefixParentPageSlug();
        }

        if ($foreign->getReplacements() !== []) {
            $this->replacements = $foreign->getReplacements();
        }

        if ($foreign->getPostModifiers() !== []) {
            $this->postModifiers = $foreign->getPostModifiers();
        }
    }

    /**
     * Validate fields arrays recursively.
     *
     * @param array $fields The fields array to validate.
     * @param array $config The configuration for validation context.
     * @param int $i The current index.
     *
     * @throws Exception If a field is not a string or if it does not exist.
     */
    private function _validateFieldsArrays(array $fields, array $config, int $i): void
    {
        $identifier = $config['identifier'];
        $j = 0;
        foreach ($fields as $field) {
            if (is_array($field)) {
                $this->_validateFieldsArrays($field, $config, $j);
            } elseif (!is_string($field)) {
                throw new Exception(
                    "'Slug' field '$identifier' configuration 'generatorOptions['fields'][$i][$j]' must be of type string.\n" .
                    "Fix:\ngeneratorOptions:\n  fields:\n    - 'input1'\n    - 'input2'\n    -\n" .
                    "      - 'input3'\n      - 'input4'"
                );
            } else {
                if (!FieldBuilder::isSurpressedWarning(862169179) && !FieldBuilder::fieldExists($field, 'Text')) {
                    throw new Exception(
                        "WARNING: 'Slug' field '$identifier' configuration 'generatorOptions['fields'][$i][$j]' field '$field' " .
                        "does not exist in scope or is not of type 'Text'. You can suppress this warning if it exists somewhere else.\n" .
                        "You can suppress this warning in the cbConfig.yaml by adding the code 862169179 to suppressWarning."
                    );
                }
            }
            $j++;
        }
    }

    /**
     * Validate fields configuration.
     *
     * @param mixed $entry The entry to validate.
     * @param array $config The configuration for validation context.
     *
     * @throws Exception If the entry is not an array.
     */
    private function _validateFields($entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_array($entry)) {
            throw new Exception(
                "'Slug' field '$identifier' configuration 'generatorOptions['fields']' must be of type array.\n" .
                "Fix:\ngeneratorOptions:\n  fields:\n    - 'input1'\n    - 'input2'\n    -\n" .
                "      - 'input3'\n      - 'input4'"
            );
        }
        $this->_validateFieldsArrays($entry, $config, 0);
    }

    /**
     * Validate the field separator configuration.
     *
     * @param mixed $entry The entry to validate.
     * @param array $config The configuration for validation context.
     *
     * @throws Exception If the entry is not a string or if it contains invalid characters.
     */
    private function _validateFieldSeparator($entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_string($entry)) {
            throw new Exception(
                "'Slug' field '$identifier' configuration 'generatorOptions['fieldSeparator']' must be of type string."
            );
        }

        if (!FieldBuilder::isSurpressedWarning(862169180)) {
            if (preg_match('/[^a-zA-Z0-9\-._~\/]/', $entry)) {
                throw new Exception(
                    "WARNING: 'Slug' field '$identifier' configuration 'generatorOptions['fieldSeparator']' may need to be encoded.\n" .
                    "You can suppress this warning in the cbConfig.yaml by adding the code 862169180 to suppressWarning."
                );
            }
        }

        if (!FieldBuilder::isSurpressedWarning(862169181)) {
            if (strlen($entry) > 1) {
                throw new Exception(
                    "WARNING: 'Slug' field '$identifier' configuration 'generatorOptions['fieldSeparator']' is longer than one character, is this intended?\n" .
                    "You can suppress this warning in the cbConfig.yaml by adding the code 862169181 to suppressWarning."
                );
            }
        }
    }

    /**
     * Validate the replacements configuration.
     *
     * @param mixed $entry The entry to validate.
     * @param array $config The configuration for validation context.
     *
     * @throws Exception If the entry is not an array or if its keys/values are not strings.
     */
    private function _validateReplacements($entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_array($entry)) {
            throw new Exception(
                "'Slug' field '$identifier' configuration 'generatorOptions['replacements']' must be of type array.\n" .
                "Fix:\ngeneratorOptions:\n  replacements:\n    replaceThis: 'withThis'\n    andThis: 'withSomethingElse'"
            );
        }
        $i = 0;
        foreach ($entry as $key => $value) {
            if (!is_string($key)) {
                throw new Exception(
                    "'Slug' field '$identifier' configuration 'generatorOptions['replacements'][$i]' needle (key) must be of type string.\n" .
                    "Fix:\ngeneratorOptions:\n  replacements:\n    replaceThis: 'withThis'\n    andThis: 'withSomethingElse'"
                );
            }
            if (!is_string($value)) {
                throw new Exception(
                    "'Slug' field '$identifier' configuration 'generatorOptions['replacements'][$i]' replacement (value) must be of type string.\n" .
                    "Fix:\ngeneratorOptions:\n  replacements:\n    replaceThis: 'withThis'\n    andThis: 'withSomethingElse'"
                );
            }
            $i++;
        }
    }

    /**
     * Validate the post-modifiers configuration.
     *
     * @param mixed $entry The entry to validate.
     * @param array $config The configuration for validation context.
     *
     * @throws Exception If the entry is not an array or if its values are invalid.
     */
    private function _validatePostModifiers($entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_array($entry)) {
            throw new Exception(
                "'Slug' field '$identifier' configuration 'generatorOptions['postModifiers']' must be of type array.\n" .
                "Fix:\ngeneratorOptions:\n  postModifiers:\n    - '\\\\Vendor\\\\Extension\\\\UserFunction\\\\ClassName -> method'\n" .
                "    - '\\\\Vendor\\\\Extension\\\\UserFunction\\\\ClassName2 -> method2'\n    ..."
            );
        }
        $i = 0;
        foreach ($entry as $value) {
            $this->validateUserFunc($value, $config, "generatorOptions['postModifiers'][$i]", 'Slug');
            $i++;
        }
    }

    /**
     * Convert an array configuration into the object's properties.
     *
     * @param array $config The configuration array.
     * @param array $fieldConfig The field configuration.
     */
    public function arrayToConfig(array $config, array $fieldProperties, ?array $globalConf = NULL, mixed $misc = NULL): void
    {
        $properties = get_object_vars($this);
        foreach ($config as $configKey => $value) {
            if ($this->isValidConfig($properties, $configKey)) {
                switch ($configKey) {
                    case 'fields':
                        $this->_validateFields($value, $globalConf);
                        $this->fields = $value;
                        break;
                    case 'fieldSeparator':
                        $this->_validateFieldSeparator($value, $globalConf);
                        $this->fieldSeparator = $value;
                        break;
                    case 'replacements':
                        $this->_validateReplacements($value, $globalConf);
                        $this->replacements = $value;
                        break;
                    case 'postModifiers':
                        $this->_validatePostModifiers($value, $globalConf);
                        $this->postModifiers = $value;
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
 * Configuration class for slug field settings.
 */
final class SlugFieldConfig extends Config
{
    /**
     * Appearance settings for the slug field.
     */
    protected array $appearance = [];

    /**
     * Evaluation keyword for slug generation.
     */
    protected string $eval = '';

    /**
     * Fallback character used in slug generation.
     */
    protected string $fallbackCharacter = '';

    /**
     * Generator options for slug generation.
     */
    protected ?SlugFieldGeneratorOptionsConfig $generatorOptions = null;

    /**
     * Whether to prepend a slash to the slug.
     */
    protected ?bool $prependSlash = null;

    /**
     * Get the appearance settings for the slug field.
     *
     * @return array The appearance settings.
     */
    public function getAppearance(): array
    {
        return $this->appearance;
    }

    /**
     * Get the evaluation keyword for slug generation.
     *
     * @return string The evaluation keyword.
     */
    public function getEval(): string
    {
        return $this->eval;
    }

    /**
     * Get the fallback character used in slug generation.
     *
     * @return string The fallback character.
     */
    public function getFallbackCharacter(): string
    {
        return $this->fallbackCharacter;
    }

    /**
     * Get the generator options for slug generation.
     *
     * @return SlugFieldGeneratorOptionsConfig|null The generator options.
     */
    public function getGeneratorOptions(): ?SlugFieldGeneratorOptionsConfig
    {
        return $this->generatorOptions;
    }

    /**
     * Get whether to prepend a slash to the slug.
     *
     * @return bool|null Whether to prepend a slash.
     */
    public function getPrependSlash(): ?bool
    {
        return $this->prependSlash;
    }

    /**
     * Set the appearance settings for the slug field.
     *
     * @param array $appearance The appearance settings.
     */
    public function setAppearance(array $appearance): void
    {
        $this->appearance = $appearance;
    }

    /**
     * Set the evaluation keyword for slug generation.
     *
     * @param string $eval The evaluation keyword.
     */
    public function setEval(string $eval): void
    {
        $this->eval = $eval;
    }

    /**
     * Set the fallback character used in slug generation.
     *
     * @param string $fallbackCharacter The fallback character.
     */
    public function setFallbackCharacter(string $fallbackCharacter): void
    {
        $this->fallbackCharacter = $fallbackCharacter;
    }

    /**
     * Set the generator options for slug generation.
     *
     * @param SlugFieldGeneratorOptionsConfig|null $generatorOptions The generator options.
     */
    public function setGeneratorOptions(?SlugFieldGeneratorOptionsConfig $generatorOptions): void
    {
        $this->generatorOptions = $generatorOptions;
    }

    /**
     * Set whether to prepend a slash to the slug.
     *
     * @param bool|null $prependSlash Whether to prepend a slash.
     */
    public function setPrependSlash(?bool $prependSlash): void
    {
        $this->prependSlash = $prependSlash;
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
        if ($foreign->getAppearance() !== []) {
            $this->appearance = $foreign->getAppearance();
        }

        if ($foreign->getEval() !== '') {
            $this->eval = $foreign->getEval();
        }

        if ($foreign->getFallbackCharacter() !== '') {
            $this->fallbackCharacter = $foreign->getFallbackCharacter();
        }

        if ($foreign->getGeneratorOptions() !== null) {
            if ($this->generatorOptions !== null) {
                $this->generatorOptions->mergeConfig($foreign->getGeneratorOptions());
            } else {
                $this->generatorOptions = $foreign->getGeneratorOptions();
            }
        }

        if ($foreign->getPrependSlash() !== null) {
            $this->prependSlash = $foreign->getPrependSlash();
        }
    }

    /**
     * Valid evaluation keywords for slug generation.
     */
    const EVAL_KEYWORDS = [
        'unique' => parent::STRING_TYPE,
        'uniqueInSite' => parent::STRING_TYPE,
        'uniqueInPid' => parent::STRING_TYPE
    ];

    /**
     * Valid appearance keywords for slug field.
     */
    const APPEARANCE_KEYWORDS = [
        'prefix' => parent::STRING_TYPE
    ];

    /**
     * Validate the appearance settings for the slug field.
     *
     * @param mixed $entry The entry to validate.
     * @param array $config The configuration for validation context.
     *
     * @throws Exception If the entry is not an array or if it contains invalid keywords.
     */
    private function _validateAppearance($entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_array($entry)) {
            throw new Exception(
                "'Slug' field '$identifier' configuration 'appearance' must be of type array.\n" .
                "Fix:\nappearance:\n  prefix: '\\\\Vendor\\\\Extension\\\\UserFunction\\\\ClassName -> method'"
            );
        }
        foreach ($entry as $key => $value) {
            switch ($key) {
                case 'prefix':
                    $this->validateUserFunc($value, $config, "appearance['prefix']", 'Slug');
                    break;
                default:
                    throw new Exception(
                        "'Slug' field '$identifier' configuration 'appearance['$key']' $key is not a valid keyword. " .
                        "Valid keywords are: " . implode(', ', array_keys(self::APPEARANCE_KEYWORDS)) . "\n" .
                        "Fix:\nappearance:\n  prefix: '\\\\Vendor\\\\Extension\\\\UserFunction\\\\ClassName -> method'"
                    );
                    break;
            }
        }
    }

    /**
     * Validate the fallback character used in slug generation.
     *
     * @param mixed $entry The entry to validate.
     * @param array $config The configuration for validation context.
     *
     * @throws Exception If the entry is not a string or if it contains invalid characters.
     */
    private function _validateFallbackCharacter($entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_string($entry)) {
            throw new Exception(
                "'Slug' field '$identifier' configuration 'fallbackCharacter' must be of type string."
            );
        }

        if (!FieldBuilder::isSurpressedWarning(862169177)) {
            if (preg_match('/[^a-zA-Z0-9\-._~\/]/', $entry)) {
                throw new Exception(
                    "WARNING: 'Slug' field '$identifier' configuration 'fallbackCharacter' may need to be encoded.\n" .
                    "You can suppress this warning in the cbConfig.yaml by adding the code 862169177 to suppressWarning."
                );
            }
        }

        if (!FieldBuilder::isSurpressedWarning(862169178)) {
            if (strlen($entry) > 1) {
                throw new Exception(
                    "WARNING: 'Slug' field '$identifier' configuration 'fallbackCharacter' is longer than one character, is this intended?\n" .
                    "You can suppress this warning in the cbConfig.yaml by adding the code 862169178 to suppressWarning."
                );
            }
        }
    }

    /**
     * Validate the evaluation keyword for slug generation.
     *
     * @param mixed $entry The entry to validate.
     * @param array $config The configuration for validation context.
     *
     * @throws Exception If the entry is not a string or if it is not a valid keyword.
     */
    private function _validateEval($entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_string($entry)) {
            throw new Exception(
                "'Slug' field '$identifier' configuration 'eval' must be of type string."
            );
        }
        if (!in_array($entry, array_keys(self::EVAL_KEYWORDS))) {
            throw new Exception(
                "'Slug' field '$identifier' configuration 'eval' $entry is not a valid keyword." .
                "Valid keywords are: " . implode(', ', array_keys(self::EVAL_KEYWORDS))
            );
        }
    }

    /**
     * Validate the generator options for slug generation.
     *
     * @param mixed $entry The entry to validate.
     * @param array $config The configuration for validation context.
     *
     * @throws Exception If the entry is not an array.
     */
    private function _validateAndSetGeneratorOptions(mixed $entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_array($entry)) {
            throw new Exception(
                "'Slug' field '$identifier' configuration 'generatorOptions' must be of type array. Fix:\n" .
                "generatorOptions:\n  fields:\n    - 'input1'\n    - 'input2'\n  fieldSeparator: '/'\n  " .
                "prefixParentPageSlug: true\n  replacements:\n    '/': ''"
            );
        }
        
        $this->generatorOptions = new SlugFieldGeneratorOptionsConfig();
        $this->generatorOptions->arrayToConfig($entry, [], $config);
    }

    /**
     * Convert an array configuration into the object's properties.
     *
     * @param array $config The configuration array.
     * @param array $fieldProperties The field properties.
     */
    public function arrayToConfig(array $config, array $fieldProperties, ?array $globalConf = NULL, mixed $misc = NULL): void
    {
        $globalConf = $globalConf ?? $config;
        $this->checkRequirements($globalConf, [], 'Slug');
        $properties = get_object_vars($this);
        foreach ($config as $configKey => $value) {
            if ($this->isValidConfig($properties, $configKey)) {
                if ($GLOBALS['CbBuilder']['config']['Strict'] === true) {
                    switch ($configKey) {
                        case 'appearance':
                            $this->_validateAppearance($value, $globalConf);
                            $this->appearance = $value;
                            break;
                        case 'eval':
                            $this->_validateEval($value, $globalConf);
                            $this->eval = $value;
                            break;
                        case 'fallbackCharacter':
                            $this->_validateFallbackCharacter($value, $globalConf);
                            $this->fallbackCharacter = $value;
                            break;
                        case 'generatorOptions':
                            $this->_validateAndSetGeneratorOptions($value, $globalConf);
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
                    "'Slug' field '$identifier' configuration '$configKey' is not valid.\n" .
                    "Valid settings are: " . implode(', ', array_keys($properties))
                );
            }
        }
    }
}

/**
 * Represents a slug field in the field builder.
 */
final class SlugField extends Field
{
    /**
     * Configuration for the slug field.
     */
    protected SlugFieldConfig $config;

    /**
     * Get the configuration for the slug field.
     *
     * @return SlugFieldConfig The configuration.
     */
    public function getConfig(): SlugFieldConfig
    {
        return $this->config;
    }

    /**
     * Convert an array representation into a field object.
     *
     * @param array $field The array representation of the field.
     */
    private function _arrayToField(array $field): void
    {
        $this->__arrayToField('slug', $field);
        $field['table'] = $this->table;
        $field['useExistingField'] = $this->useExistingField;
        $field['identifier'] = $this->identifier;
        $config = new SlugFieldConfig();
        $config->arrayToConfig($field, array_keys(get_object_vars($this)));
        $this->config = $config;
    }

    /**
     * Merge the current field with another slug field.
     *
     * @param SlugField $foreign The foreign field to merge.
     */
    public function mergeField(SlugField $foreign): void
    {
        $this->config->mergeConfig($foreign->getConfig());
        $this->mergeFields($foreign);
    }

    /**
     * Parse the field based on the given mode and level.
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
     * Convert the field into an array representation.
     *
     * @return array The array representation of the field.
     */
    public function fieldToArray(): array
    {
        return $this->__fieldToArray($this->config);
    }

    /**
     * Constructor for the slug field.
     *
     * @param array $field The array representation of the field.
     * @param string $table The table name.
     */
    public function __construct(array $field, string $table)
    {
        $this->table = $table;
        $this->_arrayToField($field);
    }
}