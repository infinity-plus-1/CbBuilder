<?php

declare(strict_types=1);

/**
 * Part of the Cb-Builder
 * 
 * Created by:          Dennis Schwab (dennis.schwab90@icloud.com)
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

namespace DS\CbBuilder\FieldBuilder;

use DS\CbBuilder\Config\CbBuilderConfig;
use DS\CbBuilder\FieldBuilder\Fields\PaletteContainer;
use DS\CbBuilder\FieldBuilder\Tables\TtContentTable;
use Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Exception class for field builder-related errors.
 */
class FieldBuilderException extends Exception {}

/**
 * Main class for building fields.
 */
final class FieldBuilder
{
    /**
     * Meta keywords for field builder.
     */
    const META_KEYWORDS = [
        'name' => 'The name of the content block.',
        'identifier' => 'The unique identifier of the content block',
        'description' => 'The description of the content block.',
        'group' => "The group of the content block (Use 'default' if you are unsure about this).",
        'placeAt' => 'The identifier of the CType where the content block shall be placed at.',
        'position' => "Needs to be 'before' or 'after', up to where you want to place your new content block."
    ];

    /**
     * The current palette being processed.
     */
    protected ?PaletteContainer $currentPalette = null;

    /**
     * Constructor for the FieldBuilder class.
     *
     * @param string $path The path to the configuration file.
     * @param string $identifier The identifier of the field builder.
     */
    public function __construct(
        protected string $path,
        protected string $identifier
    ){}

    /**
     * Convert a TCA column type to a field type.
     *
     * @param string $type The TCA column type to convert.
     *
     * @return string The corresponding field type.
     */
    public static function convertTypeColumnToField(string $type): string
    {
        switch ($type) {
            case 'input':
                return 'Text';
                break;
            case 'text':
                return 'Textarea';
                break;
            case 'category':
                return 'Category';
                break;
            case 'check':
                return 'Checkbox';
                break;
            case 'color':
                return 'Color';
                break;
            case 'datetime':
                return 'Datetime';
                break;
            case 'email':
                return 'Email';
                break;
            case 'file':
                return 'File';
                break;
            case 'flex':
                return 'Flex';
                break;
            case 'folder':
                return 'Folder';
                break;
            case 'group':
                return 'Group';
                break;
            case 'imageManipulation':
                return 'Image';
                break;
            case 'inline':
                return 'Collection';
                break;
            case 'json':
                return 'Json';
                break;
            case 'language':
                return 'TODO';
                break;
            case 'link':
                return 'Link';
                break;
            case 'none':
                return 'None';
                break;
            case 'number':
                return 'Number';
                break;
            case 'passthrough':
                return 'Pass';
                break;
            case 'password':
                return 'Password';
                break;
            case 'radio':
                return 'Radio';
                break;
            case 'select':
                return 'Select';
                break;
            case 'slug':
                return 'Slug';
                break;
            case 'user':
                return 'Custom';
                break;
            case 'uuid':
                return 'Uuid';
                break;
            default:
                return '';
                break;
        }    
    }

    /**
     * Convert a field type to a TCA column type.
     *
     * @param string $type The field type to convert.
     *
     * @return string The corresponding TCA column type.
     */
    public static function convertTypeFieldToColumn(string $type): string
    {
        switch ($type) {
            case 'Text':
                return 'input';
                break;
            case 'Textarea':
                return 'text';
                break;
            case 'Category':
                return 'category';
                break;
            case 'Checkbox':
                return 'check';
                break;
            case 'Color':
                return 'color';
                break;
            case 'Datetime':
                return 'datetime';
                break;
            case 'Email':
                return 'email';
                break;
            case 'File':
                return 'file';
                break;
            case 'Flex':
                return 'flex';
                break;
            case 'Folder':
                return 'folder';
                break;
            case 'Group':
                return 'group';
                break;
            case 'Image':
                return 'imageManipulation';
                break;
            case 'Collection':
                return 'inline';
                break;
            case 'Json':
                return 'json';
                break;
            case 'Language':
                return 'TODO';
                break;
            case 'Link':
                return 'link';
                break;
            case 'None':
                return 'none';
                break;
            case 'Number':
                return 'number';
                break;
            case 'Pass':
                return 'passthrough';
                break;
            case 'Password':
                return 'password';
                break;
            case 'Radio':
                return 'radio';
                break;
            case 'Select':
                return 'select';
                break;
            case 'Slug':
                return 'slug';
                break;
            case 'Custom':
                return 'user';
                break;
            case 'Uuid':
                return 'uuid';
                break;
            default:
                return $type;
                break;
        }    
    }

    /**
     * Check if a warning is suppressed.
     *
     * @param int|string $code The warning code to check.
     *
     * @return bool True if the warning is suppressed, false otherwise.
     */
    public static function isSurpressedWarning(int|string $code): bool
    {
        if (
            isset($GLOBALS['CbBuilder']['config']['surpressedWarnings'])
            && $GLOBALS['CbBuilder']['config']['surpressedWarnings'] !== null
            && !empty($GLOBALS['CbBuilder']['config']['surpressedWarnings'])
        ) {
            $surpressedWarnings = $GLOBALS['CbBuilder']['config']['surpressedWarnings'];
            $surpressedWarnings = is_int($surpressedWarnings) ? strval($surpressedWarnings) : $surpressedWarnings;
            $surpressedWarnings = GeneralUtility::trimExplode(',', $surpressedWarnings);
            $code = strval($code);
            return in_array($code, $surpressedWarnings);
        } else {
            return false;
        }
    }

    /**
     * Recursively search for a field in a nested array of fields.
     *
     * @param array $fields The array of fields to search in.
     * @param string $field The name of the field to search for.
     * @param array $types The types of fields to include.
     * @param array $excludes The types of fields to exclude.
     *
     * @return bool True if the field is found, false otherwise.
     */
    private static function _searchFields(array $fields, string $field, array $types, array $excludes)
    {
        foreach ($fields as $fieldKey => $_field) {
            if (is_array($_field)) {
                foreach ($_field as $key => $value) {
                    switch ($key) {
                        case 'identifier':
                            if ($value === $field) {
                                if ((empty($types) || in_array($_field['type'], $types)) && !in_array($_field['type'], $excludes)) {
                                    return true;
                                }
                            }
                            break;
                        case 'type':
                            if ('Collection' === $value) {
                                if (isset($_field['fields']) && FieldBuilder::_searchFields($_field['fields'], $field, $types, $excludes)) {
                                    return true;
                                }
                            }
                        default:
                            break;
                    }
                }
            } else {
                switch ($fieldKey) {
                    case 'identifier':
                        if ($_field === $field) {
                            if ((empty($types) || in_array($fields['type'], $types)) && !in_array($fields['type'], $excludes)) {
                                return true;
                            }
                        }
                        break;
                    case 'type':
                        if ('Collection' === $_field) {
                            if (isset($fields['fields']) && FieldBuilder::_searchFields($fields['fields'], $field, $types, $excludes)) {
                                return true;
                            }
                        }
                    default:
                        break;
                }
            }
        }
        return false;
    }

    /**
     * Check if a field exists in the global fields array or a provided array.
     *
     * @param string $field The name of the field to check.
     * @param string|null $types The types of fields to include (comma-separated).
     * @param string|null $excludes The types of fields to exclude (comma-separated).
     * @param array|null $fields The array of fields to check (optional).
     *
     * @return bool True if the field exists, false otherwise.
     */
    public static function fieldExists(string $field, ?string $types = '', ?string $excludes = '', ?array $fields = []): bool
    {
        $types = $types !== '' ? GeneralUtility::trimExplode(',', $types) : [];
        $excludes = $excludes !== '' ? GeneralUtility::trimExplode(',', $excludes) : [];
        $fields = empty($fields) ? $GLOBALS['CbBuilder']['fields'] : $fields;
        if (FieldBuilder::_searchFields($fields, $field, $types, $excludes)) {
            return true;
        }
        return false;
    }

    /**
     * Read meta information from a YAML file.
     *
     * @return array The meta information read from the file.
     *
     * @throws FieldBuilderException If the YAML file has syntax errors or missing meta headers.
     */
    private function _readMeta(): array
    {
        $identifier = $this->identifier;
        $entries = Yaml::parseFile($this->path . "/ContentBlocks/$identifier/fields.yaml");
        if (is_array($entries)) {
            $GLOBALS['CbBuilder']['meta'] = [];
            foreach ($entries as $key => $entry) {
                if (in_array($key, array_keys(self::META_KEYWORDS))) {
                    $GLOBALS['CbBuilder']['meta'][$key] = $entry;
                }
            }
            foreach (self::META_KEYWORDS as $key => $value) {
                if (!in_array($key, array_keys($GLOBALS['CbBuilder']['meta']))) {
                    throw new FieldBuilderException (
                        "Missing meta header '$key' in 'fields.yaml'.\n" .
                        "This key is necessary for:\n" .
                        $value
                    );
                }
            }
        } else {
            throw new FieldBuilderException("Error in YAML syntax in file 'fields.yaml'.");
        }
        return $GLOBALS['CbBuilder']['meta'];
    }


    /**
     * Read fields from a YAML file.
     *
     * @return array The fields read from the file.
     */
    private function _readFields(): array
    {
        $identifier = $this->identifier;
        $fields = Yaml::parseFile($this->path . "/ContentBlocks/$identifier/fields.yaml");
        if (is_array($fields)) {  
            if (array_key_exists('fields', $fields)) {
                $GLOBALS['CbBuilder']['fields'] = $fields['fields'];
                return $fields['fields'];
            }
        }
        return []; // Return an empty array if no fields are found
    }

    /**
     * Build fields for the tt_content table.
     */
    public function buildFields(): void
    {
        $this->_readMeta();
        $fieldsArray = $this->_readFields();
        $ttContent = new TtContentTable('tt_content', 'tt_content', true);
        $ttContent->addFields($fieldsArray);
        $parsedTableArray = $ttContent->parseTable();
        if (CbBuilderConfig::isCrossParsing()) {
            $filesystem = new Filesystem();
            $identifier = CbBuilderConfig::getIdentifier();
            if ($identifier !== '') {
                $fieldsYamlPath = CbBuilderConfig::getContentBlocksPath() . "/$identifier/fields.yaml";
                if ($filesystem->exists($fieldsYamlPath)) {
                    $currentFieldsYaml = Yaml::parseFile($fieldsYamlPath);
                    $currentFieldsYaml['fields'] = $parsedTableArray;
                    $filesystem->dumpFile($fieldsYamlPath, Yaml::dump($currentFieldsYaml, PHP_INT_MAX, 2));
                }
            }
        }
    }
}