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
  * Class CheckboxFieldConfig
  * 
  * This class represents the configuration for a checkbox field.
  */
 final class CheckboxFieldConfig extends Config
 {
     /**
      * The number of columns for the checkboxes.
      */
     protected int|string $cols = '';
 
     /**
      * The default value for the checkboxes.
      */
     protected int $default = -1;
 
     /**
      * The default list of checked checkboxes.
      */
     protected string|array $defaultList = '';
 
     /**
      * The evaluation type for the field.
      */
     protected string $eval = '';
 
     /**
      * Whether to invert the display state of checkboxes.
      */
     protected ?bool $invertStateDisplay = NULL;
 
     /**
      * The items (checkboxes) for the field.
      */
     protected array $items = [];
 
     /**
      * The itemsProcFunc for dynamic item generation.
      */
     protected string $itemsProcFunc = '';
 
     /**
      * Whether the field is read-only.
      */
     protected ?bool $readOnly = NULL;
 
     /**
      * Validation rules for the field.
      */
     protected array $validation = [];
 
     /**
      * Get the number of columns for the checkboxes.
      * 
      * @return int|string The number of columns or 'inline'.
      */
     public function getCols(): int|string
     {
         return $this->cols;
     }
 
     /**
      * Set the number of columns for the checkboxes.
      * 
      * @param int|string $cols The new number of columns or 'inline'.
      * 
      * @return static The instance itself for chaining.
      */
     public function setCols(int|string $cols): static
     {
         $this->cols = $cols;
         return $this;
     }
 
     /**
      * Get the default value for the checkboxes.
      * 
      * @return int The default value.
      */
     public function getDefault(): int
     {
         return $this->default;
     }
 
     /**
      * Set the default value for the checkboxes.
      * 
      * @param int $default The new default value.
      * 
      * @return static The instance itself for chaining.
      */
     public function setDefault(int $default): static
     {
         $this->default = $default;
         return $this;
     }
 
     /**
      * Get the default list of checked checkboxes.
      * 
      * @return string|array The default list.
      */
     public function getDefaultList(): string|array
     {
         return $this->defaultList;
     }
 
     /**
      * Set the default list of checked checkboxes.
      * 
      * @param string|array $defaultList The new default list.
      * 
      * @return static The instance itself for chaining.
      */
     public function setDefaultList(string|array $defaultList): static
     {
         $this->defaultList = $defaultList;
         return $this;
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
      * Set the evaluation type for the field.
      * 
      * @param string $eval The new evaluation type.
      * 
      * @return static The instance itself for chaining.
      */
     public function setEval(string $eval): static
     {
         $this->eval = $eval;
         return $this;
     }
 
     /**
      * Get whether to invert the display state of checkboxes.
      * 
      * @return bool|null Whether to invert the display state.
      */
     public function getInvertStateDisplay(): ?bool
     {
         return $this->invertStateDisplay;
     }
 
     /**
      * Set whether to invert the display state of checkboxes.
      * 
      * @param bool|null $invertStateDisplay Whether to invert the display state.
      * 
      * @return static The instance itself for chaining.
      */
     public function setInvertStateDisplay(?bool $invertStateDisplay): static
     {
         $this->invertStateDisplay = $invertStateDisplay;
         return $this;
     }
 
     /**
      * Get the items (checkboxes) for the field.
      * 
      * @return array The items.
      */
     public function getItems(): array
     {
         return $this->items;
     }
 
     /**
      * Set the items (checkboxes) for the field.
      * 
      * @param array $items The new items.
      * 
      * @return static The instance itself for chaining.
      */
     public function setItems(array $items): static
     {
         $this->items = $items;
         return $this;
     }
 
     /**
      * Get the itemsProcFunc for dynamic item generation.
      * 
      * @return string The itemsProcFunc.
      */
     public function getItemsProcFunc(): string
     {
         return $this->itemsProcFunc;
     }
 
     /**
      * Set the itemsProcFunc for dynamic item generation.
      * 
      * @param string $itemsProcFunc The new itemsProcFunc.
      * 
      * @return static The instance itself for chaining.
      */
     public function setItemsProcFunc(string $itemsProcFunc): static
     {
         $this->itemsProcFunc = $itemsProcFunc;
         return $this;
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
      * Set whether the field is read-only.
      * 
      * @param bool|null $readOnly Whether the field should be read-only.
      * 
      * @return static The instance itself for chaining.
      */
     public function setReadOnly(?bool $readOnly): static
     {
         $this->readOnly = $readOnly;
         return $this;
     }
 
     /**
      * Get the validation rules for the field.
      * 
      * @return array The validation rules.
      */
     public function getValidation(): array
     {
         return $this->validation;
     }
 
     /**
      * Set the validation rules for the field.
      * 
      * @param array $validation The new validation rules.
      * 
      * @return static The instance itself for chaining.
      */
     public function setValidation(array $validation): static
     {
         $this->validation = $validation;
         return $this;
     }
 
     /**
      * Merge the configuration from another CheckboxFieldConfig instance.
      * 
      * @param CheckboxFieldConfig $foreign The configuration to merge.
      */
      public function mergeConfig(Config $foreign): void
      {
          if (!$foreign instanceof self) {
              throw new InvalidArgumentException (
                  "Config 'foreign' must be of type " . get_class($this)
              );
          }
         $this->mergeMainConfig($foreign);
         $this->cols = ($foreign->getCols() !== '' || $foreign->getCols() >= 0) ? $foreign->getCols() : $this->cols;
         $this->default = ($foreign->getDefault() >= 0) ? $foreign->getDefault() : $this->default;
 
         $fDefLis = $foreign->getDefaultList();
         $this->defaultList = (is_array($fDefLis && !empty($fDefLis))) || (is_string($fDefLis) && $fDefLis !== '')
             ? $fDefLis
             : $this->defaultList;
 
         $this->eval = ($foreign->getEval() !== '') ? $foreign->getEval() : $this->eval;
         $this->invertStateDisplay = ($foreign->getInvertStateDisplay() !== NULL)
             ? $foreign->getInvertStateDisplay()
             : $this->invertStateDisplay;
         $this->items = (!empty($foreign->getItems())) ? $foreign->getItems() : $this->items;
         $this->itemsProcFunc = ($foreign->getItemsProcFunc() !== '') ? $foreign->getItemsProcFunc() : $this->itemsProcFunc;
         $this->readOnly = ($foreign->getReadOnly() !== NULL) ? $foreign->getReadOnly() : $this->readOnly;
         $this->validation = (!empty($foreign->getValidation())) ? $foreign->getValidation() : $this->validation;
     }
 
     /**
      * Valid render types for checkboxes.
      */
     const RENDER_TYPES = [
         'check', 'checkboxToggle', 'checkboxLabeledToggle'
     ];
 
     /**
      * Valid evaluation keywords for checkboxes.
      */
     const EVAL_KEYWORDS = [
         'maximumRecordsChecked', 'maximumRecordsCheckedInPid'
     ];
 
     /**
      * Valid item keywords for checkboxes.
      */
     const ITEM_KEYWORDS = [
         'label' => Config::STRING_TYPE,
         'invertStateDisplay' => Config::BOOL_TYPE,
         'iconIdentifierChecked' => Config::STRING_TYPE,
         'iconIdentifierUnchecked' => Config::STRING_TYPE,
         'labelChecked' => Config::STRING_TYPE,
         'labelUnchecked' => Config::STRING_TYPE
     ];
 
     /**
      * Calculate the bitmask from the default list.
      * 
      * @return int The calculated bitmask.
      */
     private function _calculateBITMASK(): int
     {
         $set = 0;
         $list = $this->defaultList;
         foreach ($list as $enabledPos) {
             if ($enabledPos > 0 && $enabledPos < 33) {
                 $set |= (1 << ($enabledPos - 1));
             }
         }
         return $set;
     }
 
     /**
      * Validate a bitmask against the number of items.
      * 
      * @param int $bitmask The bitmask to validate.
      * 
      * @return bool Whether the bitmask is valid.
      */
     private function _validateBITMASK(int $bitmask): bool
     {
         if (empty($this->items) && $bitmask > 1) return false;
         $count = count($this->items);
         if ($count === 32 && $bitmask === 2147483647) return false;
         /** ~(0 - (1 << count($this->items))) >= bitmask */
         return ((~(abs(~(PHP_INT_MAX - (1 << $count))))) & (0x7FFFFFFFFFFFFFFF)) >= $bitmask;
     }
 
     /**
      * Validate the 'cols' configuration.
      * 
      * @param mixed $entry The 'cols' value.
      * @param array $config The configuration.
      * 
      * @throws Exception If validation fails.
      */
     private function _validateAndSetCols(mixed $entry, array $config): void
     {
         $identifier = $config['identifier'];
         if ('inline' === $entry) {
             $this->cols = 'inline';
         } else if ($entry = $this->handleIntegers($entry)) {
             if ($entry <= 0 || $entry >= 32) {
                 $this->cols = $entry;
             }
         } else {
             throw new Exception("'Checkbox' field '$identifier' configuration 'cols' must contain either 'inline' or an integer " .
             "between 1-31.");
         }
     }
 
     /**
      * Validate the 'default' configuration.
      * 
      * @param mixed $entry The 'default' value.
      * @param array $config The configuration.
      * 
      * @throws Exception If validation fails.
      */
     private function _validateDefault(mixed $entry, array $config): void
     {
         $identifier = $config['identifier'];
         if ($entry = $this->handleIntegers($entry)) {
             if ($entry > 0xFFFFFFFF) {
                 throw new Exception("'Checkbox' field '$identifier' configuration 'default' must be an integer between 0 and 4294967295.");
             }
             if ($entry === 0 || $entry === 1) {
                 $this->default = $entry;
             } else if (isset($config['items'])) {
                 if (empty($this->items)) $this->_validateItems($config['items'], $config);
                 if ($this->_validateBITMASK($entry)) {
                     $this->default = $entry;
                 } else {
                     throw new Exception(
                         "'Checkbox' field '$identifier' configuration 'default' does not match with the " .
                         "number of checkboxes defined in 'items'."
                     );
                 }
             } else {
                 throw new Exception(
                     "'Checkbox' field '$identifier' configuration 'default' does not match with the " .
                     "number of checkboxes defined in 'items'."
                 );
             }
         } else {
             throw new Exception("'Checkbox' field '$identifier' configuration 'default' must be an integer.");
         }
     }

    /**
     * Validate the 'defaultList' configuration.
     * 
     * @param mixed $entry The 'defaultList' value.
     * @param array $config The configuration.
     * 
     * @throws Exception If validation fails.
     */
    private function _validateDefaultList(mixed $entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (is_string($entry) && $entry !== '') {
            $entry = array_map('trim', explode(',', $entry));
        }
        if (isset($config['items']) && empty($this->items)) $this->_validateItems($config['items'], $config);
        $this->defaultList = [];
        $i = 0;
        foreach ($entry as $num) {
            if ($num = $this->handleIntegers($num)) {
                $this->defaultList[] = $num;
            } else {
                throw new Exception(
                    "'Checkbox' field '$identifier' configuration 'defaultList[$i]' must be an integer."
                );
            }
            $i++;
        }
        $this->_validateDefault($this->_calculateBITMASK(), $config);
    }

    /**
     * Validate the 'eval' configuration.
     * 
     * @param mixed $entry The 'eval' value.
     * @param array $config The configuration.
     * 
     * @throws Exception If validation fails.
     */
    private function _validateEval(mixed $entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (!in_array($entry, self::EVAL_KEYWORDS)) {
            throw new Exception(
                "'Checkbox' field '$identifier' configuration 'eval' must contain one of the following keywords: " .
                implode(', ', self::EVAL_KEYWORDS)
            );
        }
        if (!isset($config['validation'])) {
            throw new Exception(
                "'Checkbox' field '$identifier' configuration 'eval' must be combined with 'validation'." .
                " Fix: 'eval' => '$entry',\n'validation' => [\n\t'$entry' => 1\n]"
            );
        }
        $this->eval = $entry;
        if (empty($this->validation)) $this->_validateValidation($config['validation'], $config);
        if ($entry !== array_key_first($this->validation[0])) {
            throw new Exception(
                "'Checkbox' field '$identifier' configuration 'eval' must match with 'validation'." .
                " E.g.: 'eval' => '$entry',\n'validation' => [\n\t'$entry' => 1\n]"
            );
        }
    }

    /**
     * Validate the 'items' configuration.
     * 
     * @param mixed $entry The 'items' value.
     * @param array $config The configuration.
     * 
     * @throws Exception If validation fails.
     */
    private function _validateItems(mixed $entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_array($entry)) {
            throw new Exception(
                "'Checkbox' field '$identifier' configuration 'items' must be of type array."
            );
        }
        $this->items = [];
        $i = 0;
        foreach ($entry as $item) {
            if (!is_array($item)) {
                throw new Exception(
                    "'Checkbox' field '$identifier' configuration 'items[$i]' each item must be of type array."
                );
            }
            foreach ($item as $key => $property) {
                $this->validateKeywordAndType($key, $identifier, $property, "items[$i]", 'Checkbox', self::ITEM_KEYWORDS);
                $i++;
            }
            $this->items[] = $item;
        }
    }

    /**
     * Validate the 'itemsProcFunc' configuration.
     * 
     * @param mixed $entry The 'itemsProcFunc' value.
     * @param array $config The configuration.
     * 
     * @throws Exception If validation fails.
     */
    private function _validateItemsProcFunc(mixed $entry, array $config): void
    {
        $identifier = $config['identifier'];
        if ((str_contains($entry, '->') && str_contains($entry, '::class'))) {
            $this->itemsProcFunc = $entry;
        } else {
            throw new Exception(
                "'Checkbox' field '$identifier' configuration 'itemsProcFunc' must contain a class path and name and method name in format: " .
                "'\VENDOR\Extension\UserFunction\FormEngine\YourClass::class->yourMethod'."
            );
        }
    }

    /**
     * Validate the 'validation' configuration.
     * 
     * @param mixed $entry The 'validation' value.
     * @param array $config The configuration.
     * 
     * @throws Exception If validation fails.
     */
    private function _validateValidation(mixed $entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (is_array($entry) || $GLOBALS['CbBuilder']['config']['Strict'] === false) {
            if (count($entry) === 1 || $GLOBALS['CbBuilder']['config']['Strict'] === false) {
                if (!isset($entry[0])) {
                    throw new Exception(
                        "'Checkbox' field '$identifier' configuration 'validation[]' must contain exactly one element at index 0." .
                        " Fix:\nvalidation:\n  - maximumRecordsChecked: 1"
                    );
                }
                $key = array_key_first($entry[0]);
                if ('maximumRecordsChecked' === $key || 'maximumRecordsCheckedInPid' === $key || $GLOBALS['CbBuilder']['config']['Strict'] === false) {
                    if (!isset($config['eval'])) {
                        if ($GLOBALS['CbBuilder']['config']['Strict'] === true) {
                            throw new Exception(
                                "'Checkbox' field '$identifier' configuration 'validation' must be combined with 'eval'." .
                                " E.g.: 'eval' => 'maximumRecordsCheckedInPid',\n'validation' => [\n\t'maximumRecordsCheckedInPid' => 1\n]"
                            );
                        }
                    }
                    if (!is_int($entry[0][$key])) {
                        throw new Exception(
                            "'Checkbox' field '$identifier' configuration 'validation[0][key]' must contain an integer value."
                        );
                    }
                    $this->validation = $entry;
                    if ($this->eval === '') $this->_validateEval($config['eval'], $config);
                    if ($key !== $this->eval) {
                        if ($GLOBALS['CbBuilder']['config']['Strict'] === true) {
                            throw new Exception(
                                "'Checkbox' field '$identifier' configuration 'validation' must match with 'eval'." .
                                " E.g.: 'eval' => 'maximumRecordsCheckedInPid',\n'validation' => [\n\t'maximumRecordsCheckedInPid' => 1\n]"
                            );
                        }
                    }
                } else {
                    throw new Exception(
                        "'Checkbox' field '$identifier' configuration 'validation[0]' must either contain 'maximumRecordsChecked' or " .
                        "'maximumRecordsCheckedInPid'."
                    );
                }
            } else {
                throw new Exception(
                    "'Checkbox' field '$identifier' configuration 'validation' must contain exactly one element of type array."
                );
            }
        } else {
            throw new Exception(
                "'Checkbox' field '$identifier' configuration 'validation' must be of type array."
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
        $this->checkRequirements($globalConf, ['identifier'], 'Checkbox');
        $properties = get_object_vars($this);
        foreach ($config as $configKey => $value) {
            if ($this->isValidConfig($properties, $configKey)) {
                if ($GLOBALS['CbBuilder']['config']['Strict'] === true) {
                    switch ($configKey) {
                        case 'cols':
                            $this->_validateAndSetCols($value, $config);
                            break;
                        case 'default':
                            if ($this->default < 0) $this->_validateDefault($value, $globalConf);
                            break;
                        case 'defaultList':
                            if (empty($this->defaultList)) $this->_validateDefaultList($value, $globalConf);
                            break;
                        case 'eval':
                            if ($this->eval === '') $this->_validateEval($value, $globalConf);
                            break;
                        case 'items':
                            if (empty($this->items)) $this->_validateItems($value, $globalConf);
                            break;
                        case 'itemsProcFunc':
                            $this->_validateItemsProcFunc($value, $globalConf);
                            break;
                        case 'validation':
                            if (empty($this->validation)) $this->_validateValidation($value, $globalConf);
                            break;
                        default:
                            $this->addGenericConfig($configKey, $value, $globalConf, $this, $fieldProperties);
                    }
                } else {
                    $this->$configKey = $value;
                }
            } else if (!in_array($configKey, $fieldProperties)) {
                $identifier = $config['identifier'];
                throw new Exception(
                    "'Checkbox' field '$identifier' configuration '$configKey' is not valid.\n" .
                    "Valid settings are: " . implode(', ', array_keys($properties))
                );
            }
        }
    }
}

/**
 * Class CheckboxField
 * 
 * Represents a checkbox field.
 */
final class CheckboxField extends Field
{
    /**
     * The configuration for this checkbox field.
     */
    protected CheckboxFieldConfig $config;

    /**
     * Get the configuration of this checkbox field.
     * 
     * @return CheckboxFieldConfig The configuration.
     */
    public function getConfig(): CheckboxFieldConfig
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
        $this->__arrayToField('checkbox', $field);
        $field['table'] = $this->table;
        $field['useExistingField'] = $this->useExistingField;
        $field['identifier'] = $this->identifier;
        $config = new CheckboxFieldConfig();
        $config->arrayToConfig($field, array_keys(get_object_vars($this)));
        $this->config = $config;
    }

    /**
     * Merge the configuration of another checkbox field into this one.
     * 
     * @param CheckboxField $foreign The field to merge.
     */
    public function mergeField(CheckboxField $foreign): void
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
     * Constructor for the CheckboxField class.
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