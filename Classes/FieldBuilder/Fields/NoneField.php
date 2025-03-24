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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Configuration class for none field.
 */
final class NoneFieldConfig extends Config
{
    /**
     * Default value for the field.
     */
    protected string $default = '';

    /**
     * Format string for the field.
     */
    protected string $format = '';

    /**
     * Format array for the field.
     */
    protected array $format_ = [];

    /**
     * Size of the field.
     */
    protected int $size = -1;

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
     * Get the format string for the field.
     *
     * @return string The format string.
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * Get the format array for the field.
     *
     * @return array The format array.
     */
    public function getFormatArray(): array
    {
        return $this->format_;
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
     * Set the default value for the field.
     *
     * @param string $default The default value to set.
     */
    public function setDefault(string $default): void
    {
        $this->default = $default;
    }

    /**
     * Set the format string for the field.
     *
     * @param string $format The format string to set.
     */
    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    /**
     * Set the format array for the field.
     *
     * @param array $format_ The format array to set.
     */
    public function setFormatArray(array $format_): void
    {
        $this->format_ = $format_;
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

        if ($foreign->getDefault() !== '') {
            $this->default = $foreign->getDefault();
        }

        if ($foreign->getFormat() !== '') {
            $this->format = $foreign->getFormat();
        }

        if (!empty($foreign->getFormatArray())) {
            $this->format_ = $foreign->getFormatArray();
        }

        if ($foreign->getSize() >= 0) {
            $this->size = $foreign->getSize();
        }
    }

    /**
     * Available format keywords for the field.
     */
    const FORMAT_KEYWORDS = [
        'date' => [
            'option' => parent::STRING_TYPE,
            'appendAge' => parent::BOOL_TYPE
        ],
        'datetime' => NULL,
        'time' => NULL,
        'timesec' => NULL,
        'year' => NULL,
        'int' => [
            'base' => [
                'dec', 'hex', 'HEX', 'oct', 'bin'
            ]
        ],
        'float' => [ 'precision' => parent::FUNCTION ],
        'number' => [ 'option' => parent::STRING_TYPE ],
        'md5' => NULL,
        'filesize' => [ 'appendByteSize' => parent::BOOL_TYPE ],
        'user' => [ 'userFunc' => parent::FUNCTION ]
    ];

    /**
     * Validate the precision option for float format.
     *
     * @param mixed $value The value to validate.
     * @param string $identifier The identifier of the field.
     *
     * @throws Exception If the precision is not a valid integer or out of range.
     */
    private function _validateFormatOption_precision($value, string $identifier): void
    {
        if (!$value = $this->handleIntegers($value)) {
            throw new Exception(
                "'None' field '$identifier' configuration 'format['float']['precision']' must be of type integer or a string representing an integer."
            );
        }

        if ($value < 0 || $value > 10) {
            throw new Exception(
                "'None' field '$identifier' configuration 'format['float']['precision']' must be a range between 0 and 10."
            );
        }
    }

    /**
     * Validate the user function option for user format.
     *
     * @param mixed $value The value to validate.
     * @param string $identifier The identifier of the field.
     *
     * @throws Exception If the user function is not a valid string or does not match the required format.
     */
    private function _validateFormatOption_userFunc($value, string $identifier): void
    {
        if (!is_string($value)) {
            throw new Exception(
                "'None' field '$identifier' configuration 'format['user']['userFunc']' must be of type string."
            );
        }

        if (!str_contains($value, '->')) {
            throw new Exception(
                "'None' field '$identifier' configuration 'format['user']['userFunc']' must be in format Vendor\Extension\UserFunction\ClassName -> method."
            );
        }

        if ($GLOBALS['CbBuilder']['config']['propertySpecific']['None']['format.']['user']['testIfClassAndMethodExists'] === true) {
            $className = $methodName = '';
            try {
                $splitted = explode('->', $value);
                $className = trim($splitted[0]);
                $methodName = trim($splitted[1]);
                GeneralUtility::makeInstance($className);
            } catch (\Throwable $th) {
                if (str_contains($th->getMessage(), 'not found')) {
                    throw new Exception(
                        "'None' field '$identifier' configuration 'format['user']['userFunc']' class $className not found."
                    );
                }
            }

            if (!method_exists($className, $methodName)) {
                throw new Exception(
                    "'None' field '$identifier' configuration 'format['user']['userFunc']' method $methodName not found."
                );
            }
        }
    }

    /**
     * Validate the format entry.
     *
     * @param mixed $entry The format entry to validate.
     * @param array $config The configuration array.
     *
     * @throws Exception If the format entry is not a valid string or keyword.
     */
    private function _validateFormat($entry, array $config): void
    {
        $identifier = $config['identifier'];
        if (!is_string($entry)) {
            throw new Exception(
                "'None' field '$identifier' configuration 'format' must be of type string."
            );
        }
        if (!in_array($entry, self::FORMAT_KEYWORDS) && !array_key_exists($entry, self::FORMAT_KEYWORDS)) {
            throw new Exception(
                "'None' field '$identifier' configuration 'format' must be one of the following keywords: " .
                implode(', ', array_keys(self::FORMAT_KEYWORDS))
            );
        }
    }

    /**
     * Validate the format options.
     *
     * @param array $entry The format options to validate.
     * @param array $config The configuration array.
     *
     * @throws Exception If the format options are invalid.
     */
    private function _validateFormatOptions(mixed $entry, array $config): void
    {
        $identifier = $config['identifier'];

        if (!is_array($entry)) {
            throw new Exception(
                "'None' field '$identifier' configuration 'format.' must be of type array."
            );
        }

        if ($this->format === '' && !isset($config['format'])) {
            throw new Exception(
                "'None' field '$identifier' configuration 'format.' needs 'format' to have a keyword assigned."
            );
        }

        $format = $config['format'];
        if ($this->format === '') {
            $this->_validateFormat($format, $config);
        }

        // Special case
        if ($format === 'date' && array_key_exists('strftime', $entry)) {
            throw new Exception(
                "'None' field '$identifier' configuration 'format.['date']['strftime']' 'strftime' is deprecated since " .
                "PHP 8.1.0 and thus not supported at this place, even still available in TYPO3."
            );
        }

        if (!array_key_exists($format, self::FORMAT_KEYWORDS)) {
            $formatsWithOptions = array_filter(self::FORMAT_KEYWORDS, function ($value, $key) {
                return is_array($value);
            }, ARRAY_FILTER_USE_BOTH);
            $formatsWithOptions = array_keys($formatsWithOptions);
            throw new Exception(
                "'None' field '$identifier' configuration 'format.[$format]' has no available options." .
                "Formats with options available are: " . implode(', ', $formatsWithOptions)
            );
        }

        $formatOptions = self::FORMAT_KEYWORDS[$format];

        if (!is_array($formatOptions)) {
            throw new Exception(
                "'None' field '$identifier' configuration 'format=>$format' has no available options."
            );
        }

        $i = 0;
        foreach ($entry as $key => $value) {
            if (!array_key_exists($key, $formatOptions)) {
                $formatOptions = array_keys($formatOptions);
                throw new Exception(
                    "'None' field '$identifier' configuration 'format.[$i]' is not a valid option. Valid options are: " .
                    implode(', ', $formatOptions)
                );
            }
            switch ($formatOptions[$key]) {
                case self::BOOL_TYPE:
                    if (!is_bool($value)) {
                        throw new Exception(
                            "'None' field '$identifier' configuration 'format.['$key']' must be of type boolean."
                        );
                    }
                    break;
                case self::STRING_TYPE:
                    if (!is_string($value)) {
                        throw new Exception(
                            "'None' field '$identifier' configuration 'format.['$key']' must be of type string."
                        );
                    }
                    break;
                case self::INTEGER_TYPE:
                    if (!$this->handleIntegers($value)) {
                        throw new Exception(
                            "'None' field '$identifier' configuration 'format.['$key']' must be of type integer."
                        );
                    }
                    break;
                case self::FLOAT_TYPE:
                    if (!is_numeric($value)) {
                        throw new Exception(
                            "'None' field '$identifier' configuration 'format.['$key']' must be of type float."
                        );
                    }
                    break;
                case self::FUNCTION:
                    $function = '_validateFormatOption_' . $key;
                    call_user_func([$this, $function], $value, $identifier);
                    break;
                default:
                    if (is_array($formatOptions[$key])) {
                        if (!in_array($value, $formatOptions[$key])) {
                            throw new Exception(
                                "'None' field '$identifier' configuration 'format.['$key']' $value is not a valid option. " .
                                "Valid options are: " . implode(', ', $formatOptions[$key])
                            );
                        }
                    }
                    break;
            }
            $i++;
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
        $this->checkRequirements($globalConf, ['identifier'], 'None');
        $properties = get_object_vars($this);
        foreach ($config as $configKey => $value) {
            if ($this->isValidConfig($properties, $configKey) || $configKey === 'format.') {
                if ($GLOBALS['CbBuilder']['config']['Strict'] === true) {
                    switch ($configKey) {
                        case 'format':
                            $this->_validateFormat($value, $globalConf);
                            $this->format = $value;
                            break;
                        case 'format.':
                            $this->_validateFormatOptions($value, $globalConf);
                            $this->format_ = $value;
                            break;
                        case 'size':
                            $this->validateInteger($value, $globalConf, 'size', 'None', 10, 50);
                            $this->size = $value;
                            break;
                        default:
                            $this->addGenericConfig($configKey, $value, $globalConf, $this, $fieldProperties);
                            break;
                    }
                } else {
                    $this->$configKey = $value;
                }
            } elseif (!in_array($configKey, $fieldProperties)) {
                $identifier = $globalConf['identifier'];
                throw new Exception(
                    "'None' field '$identifier' configuration '$configKey' is not valid.\n" .
                    "Valid settings are: " . implode(', ', array_keys($properties))
                );
            }
        }
    }
}

/**
 * Class representing a none field.
 */
final class NoneField extends Field
{
    /**
     * Configuration for the none field.
     */
    protected NoneFieldConfig $config;

    /**
     * Get the configuration for the none field.
     *
     * @return NoneFieldConfig The configuration.
     */
    public function getConfig(): NoneFieldConfig
    {
        return $this->config;
    }

    /**
     * Convert an array to a none field.
     *
     * @param array $field The array to convert.
     */
    private function _arrayToField(array $field): void
    {
        $this->__arrayToField('none', $field);
        $field['table'] = $this->table;
        $field['useExistingField'] = $this->useExistingField;
        $field['identifier'] = $this->identifier;
        $config = new NoneFieldConfig();
        $config->arrayToConfig($field, array_keys(get_object_vars($this)));
        $this->config = $config;
    }

    /**
     * Merge the current field with another field.
     *
     * @param NoneField $foreign The foreign field to merge.
     */
    public function mergeField(NoneField $foreign): void
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
     * Constructor for the none field.
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