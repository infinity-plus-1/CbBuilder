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

namespace DS\CbBuilder\Utility;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Utility class for parsing arrays from strings.
 */
final class ArrayParser
{
    /**
     * Extracts an array from a given string.
     *
     * @param string $string The input string to parse.
     * @param int|null $offset The starting position in the string. Defaults to 0.
     * @param string $identifier Optional identifier to search for before parsing. Defaults to an empty string.
     *
     * @return array|bool The extracted array or false if parsing fails.
     */
    public static function extractArrayFromString(string $string, ?int $offset = 0, string $identifier = ''): array|bool
    {
        $array = [];
        $strArray = '';

        // Adjust the offset if an identifier is provided
        if ($identifier !== '') {
            $offset = Utility::stringSafeStrpos($string, $identifier, $offset);
        }
        if ($offset < 0) {
            return false; // Identifier not found
        }

        // Find the start of the array
        $startArray = Utility::stringSafeStrpos($string, '[', $offset);
        $endArray = Utility::stringSafeStrpos($string, ';', $startArray);

        if ($startArray < 0) {
            return false; // No array found
        }

        // Determine the start position based on surrounding characters
        $semiColPos = Utility::stringSafeStrposBackward($string, ';', $startArray);
        $braPos = Utility::stringSafeStrposBackward($string, '}', $startArray);
        $start = -1;
        if ($semiColPos >= 0 || $braPos >= 0) {
            if ($semiColPos > $braPos) {
                $start = $semiColPos;
            } else {
                $start = $braPos;
            }
        } else {
            $dolPos = Utility::stringSafeStrposBackward($string, '$', $startArray);
            $retPos = Utility::stringSafeStrposBackward($string, 'return', $startArray);
            if ($dolPos >= 0 || $retPos >= 0) {
                if ($dolPos > $retPos) {
                    $start = $dolPos;
                } else {
                    $start = $retPos;
                }
            } else {
                $comPos = Utility::stringSafeStrposBackward($string, ',', $startArray);
                $parPos = Utility::stringSafeStrposBackward($string, ')', $startArray);
                if ($comPos >= 0 || $parPos >= 0) {
                    // Count square brackets to find the end of the array
                    $squareBracketsCount = 1;
                    $isString = '';
                    $len = strlen($string);
                    for ($cIndex = $startArray + 1; $cIndex < $len; $cIndex++) {
                        Utility::skipWhitespace($string, $cIndex, $len);
                        $char = $string[$cIndex];
                        switch ($char) {
                            case '"':
                                if (!Utility::isEscaped($string, $cIndex - 1)) {
                                    if ($isString === '') {
                                        $isString = '"';
                                    } else {
                                        $isString = '';
                                    }
                                }
                                break;
                            case "'":
                                if (!Utility::isEscaped($string, $cIndex - 1)) {
                                    if ($isString === '') {
                                        $isString = "'";
                                    } else {
                                        $isString = '';
                                    }
                                }
                                break;
                            case '[':
                                if ($isString === '') {
                                    $squareBracketsCount++;
                                }
                                break;
                            case ']':
                                if ($isString === '') {
                                    $squareBracketsCount--;
                                }
                                break;
                        }
                        if ($squareBracketsCount === 0) {
                            break;
                        }
                    }
                    $endArray = $cIndex + 1;
                }
            }
        }

        // Extract the key if available
        $key = $start >= 0 ? Utility::trimAlwaysWhitespace(substr($string, $start, ($startArray - $start)), ';') : null;

        // Extract the array string
        $strArray = Utility::trimAlwaysWhitespace(substr($string, $startArray, ($endArray - $startArray)), ';');

        // Handle special case for 'return'
        if ($key === 'return') {
            $array[0] = $strArray;
            $array[1] = $endArray;
            $array[2] = $key;
            return $array;
        }

        // Split the string into key-value pairs
        $splitted = Utility::stringSafeExplode('=', $strArray, 2);
        if (count($splitted) < 1 || count($splitted) > 2) {
            return false; // Invalid format
        } elseif (count($splitted) === 1) {
            $array[0] = $strArray;
            $array[1] = $endArray;
            $array[2] = is_string($key) ? Utility::trimAlwaysWhitespace($key, '=$') : $key;
            return $array;
        } else {
            // Check for array keys in the format of ['key'] = 'value'
            if (Utility::preg_match_str("/(\s*\[\s*(?:'[^']*'|\"[^\"]*\"|\d+)\s*\]\s*)+/", $splitted[0])) {
                preg_match_all("/\[\s*(?:'([^']*)'|\"([^\"]*)\"|([^\[\]]+))\s*\]/", $splitted[0], $matches, PREG_SET_ORDER);
                $keys = array_map(fn($v): string => $v[1], $matches);
                $indentStr = '';
                $array[0] = "[\n";
                foreach ($keys as $_key) {
                    $indentStr .= "\t";
                    $array[0] .= $indentStr . '"' . $_key . '"' . " => [\n";
                }
                $indentStr .= "\t";
                $array[0] .= $indentStr . Utility::trimAlwaysWhitespace($splitted[1], '[]') . "\n";
                foreach ($keys as $_key) {
                    $indentStr = substr($indentStr, 1);
                    $array[0] .= $indentStr . "]\n";
                }
                $array[0] .= ']';
                $array[1] = $endArray;
                $array[2] = is_string($key) ? Utility::trimAlwaysWhitespace($key, '=$') : $key;
                return $array;
            } else {
                $array[0] = $strArray;
                $array[1] = $endArray;
                $array[2] = is_string($key) ? Utility::trimAlwaysWhitespace($key, '=$') : $key;
                return $array;
            }
        }
        return false;
    }

    /**
     * Parses a PHP array string into an actual array.
     *
     * @param string $input The input string representing a PHP array.
     * @param bool|null $useEval Whether to use eval for parsing. Defaults to false.
     * @param string $key Optional key to use if returning a single element array. Defaults to an empty string.
     *
     * @return array The parsed array.
     */
    public static function parsePhpArrayString(string $input, ?bool $useEval = false, string $key = ''): array
    {
        $input = trim($input);
        if ($input === '') {
            return [[]];
        }
        if ($input[0] !== '[' || $input[strlen($input) - 1] !== ']') {
            return [[]];
        }
        if ($useEval === false) {
            $input = 'array' . $input . ';';
            $tokens = token_get_all("<?php $input");
            array_shift($tokens);
            $array = ArrayParser::parseArrayTokens($tokens);
            return $key !== '' ? [$key => current($array)] : $array;
        } else {
            $parsed = [];
            eval("\$parsed = " . trim(trim($input), ';') . ';');
            return $parsed;
        }
    }

    /**
     * Static property to store the last parsed value.
     */
    public static $lastValue = NULL;

    /**
     * Parses PHP array tokens into an actual array.
     *
     * @param array &$tokens The tokens to parse.
     *
     * @return array The parsed array.
     */
    public static function parseArrayTokens(array &$tokens): array
    {
        $array = [];
        $key = null;
        $expectingKey = true;
        $expectingValue = false;
        $expectingDColons = false;
        $expectingClass = false;
        $value = NULL;
        $tempValue = NULL;
        while ($tokens) {
            $token = array_shift($tokens);
            if (is_array($token)) {
                [$id, $value] = $token;

                if ($id === T_CONSTANT_ENCAPSED_STRING) {
                    $value = stripcslashes(substr($value, 1, -1));
                    if ($expectingKey) {
                        $key = $value;
                    } elseif ($expectingValue) {
                        $array[$key] = $value;
                        $expectingKey = true;
                        $expectingValue = false;
                        $key = NULL;
                    }
                } else if ($id === T_DOUBLE_ARROW) {
                    if ($value === '=>') {
                        $expectingValue = true;
                        $expectingKey = false;
                    }
                } elseif ($id === T_LNUMBER || $id === T_DNUMBER) {
                    $value = $value + 0;
                    if ($expectingKey) {
                        $key = $value;
                    } elseif ($expectingValue) {
                        $array[$key] = $value;
                        $expectingKey = true;
                        $expectingValue = false;
                        $key = NULL;
                    }
                } elseif ($id === T_STRING && ($value === 'true' || $value === 'false' || $value === 'null')) {
                    $value = $value === 'true' ? true : ($value === 'false' ? false : null);
                    if ($expectingValue) {
                        $array[$key] = $value;
                        $expectingKey = true;
                        $expectingValue = false;
                        $key = NULL;
                    }
                } else {
                    //This part needs further testing
                    if ($id === T_STRING) {
                        $tempValue = $value;
                        $expectingDColons = true;
                    } elseif ($id === T_DOUBLE_COLON && $expectingDColons) {
                        $expectingDColons = false;
                        $expectingClass = true;
                    } elseif ($id === T_CLASS && $expectingClass) {
                        $array[$key] = $tempValue . '::class';
                        $expectingDColons = false;
                        $expectingClass = false;
                        $tempValue = NULL;
                        $expectingKey = true;
                        $expectingValue = false;
                        $key = NULL;
                    } else {
                        $expectingDColons = false;
                        $expectingClass = false;
                        $tempValue = NULL;
                    }
                }
            } elseif (is_string($token)) {
                if ($token === '[' || $token === 'array(') {
                    $nestedArray = ArrayParser::parseArrayTokens($tokens);
                    if ($expectingKey) {
                        $key = count($array);
                    }
                    $array[$key] = $nestedArray;
                    $expectingKey = true;
                    $expectingValue = false;
                    $key = NULL;
                } elseif ($token === ']') {
                    if ($expectingKey === true && $key !== NULL) {
                        $array[] = $key;
                        $key = NULL;
                    }
                    return $array;
                } elseif ($token === ',') {
                    if ($expectingKey === true && $key !== NULL) {
                        $array[] = $key;
                        $key = NULL;
                    }
                    $expectingKey = true;
                } elseif ($token === '=>') {
                    $expectingValue = true;
                }
            }
        }
        return $array;
    }

    /**
     * Extracts arrays from a given string.
     *
     * @param string $content The input string to parse.
     * @param bool|null $useEval Whether to use eval for parsing. Defaults to false.
     * @param string|null $identifier Optional identifier to search for before parsing. Defaults to null.
     * @param bool|null $inclKey Whether to include the key in the output. Defaults to false.
     *
     * @return array The extracted arrays.
     */
    public static function extractArraysFromString(string $content, ?bool $useEval = false, ?string $identifier = '', ?bool $inclKey = false): array
    {
        $index = 0;
        $arrays = [];
        do {
            $stringArray = ArrayParser::extractArrayFromString($content, $index, $identifier);
            if ($stringArray !== false) {
                $index = $stringArray[1];
                if ($inclKey === false) {
                    $arrays[] = ArrayParser::parsePhpArrayString($stringArray[0], $useEval)[0];
                } else {
                    if ($stringArray[2] !== NULL) {
                        if (isset($arrays[$stringArray[2]])) {
                            $arrays[$stringArray[2]] = array_merge_recursive($arrays[$stringArray[2]], ArrayParser::parsePhpArrayString($stringArray[0], false)[0]);
                        } else {
                            $arrays[$stringArray[2]] = ArrayParser::parsePhpArrayString($stringArray[0], $useEval)[0];
                        }
                    } else {
                        $arrays[] = ArrayParser::parsePhpArrayString($stringArray[0], $useEval)[0];
                    }
                }
            }
        } while ($stringArray !== false);
        return $arrays;
    }

    /**
     * Extracts arrays from a file.
     *
     * @param string $file The file path to parse.
     * @param bool $useEval Whether to use eval for parsing. Defaults to true.
     * @param string|null $identifier Optional identifier to search for before parsing. Defaults to null.
     * @param bool|null $inclKey Whether to include the key in the output. Defaults to false.
     *
     * @return array The extracted arrays.
     */
    public static function extractArraysFromFile(string $file, bool $useEval = false, ?string $identifier = '', ?bool $inclKey = false): array
    {
        $filesystem = new Filesystem();
        if ($filesystem->exists($file)) {
            $content = file_get_contents($file);
            return ArrayParser::extractArraysFromString($content, $useEval, $identifier, $inclKey);
        }
        return [];
    }

    /**
     * Converts an array to a string representation.
     *
     * @param array $array The array to convert.
     * @param string|null $identifier Optional identifier to prefix the array. Defaults to null.
     * @param int|null $level The indentation level. Defaults to 1.
     * @param bool|null $identifierIsKey Whether the identifier is a key. Defaults to false.
     *
     * @return string The string representation of the array.
     */
    public static function arrayToString(array $array, ?string $identifier = '', ?int $level = 1, ?bool $identifierIsKey = false): string
    {
        $tabs = '';
        for ($i = $level; $i > 0; $i--) {
            $tabs .= "\t";
        }
        $arrayString = (
            $identifier !== '' ? (
                $identifierIsKey ? substr($tabs, 1) . "\"$identifier\" => [" : substr($tabs, 1) . "$$identifier = ["
            ) : "["
        );

        foreach ($array as $key => $value) {
            $arrayString .= "\n" . $tabs . (is_string($key) ? ('"' . $key . '" => ') : '');
            if (is_array($value)) {
                $arrayString .= ArrayParser::arrayToString($value, '', ($level + 1));
            } else if (is_string($value)) {
                $arrayString .= "\"$value\"";
            } else {
                $arrayString .= Utility::toString($value);
            }
            $arrayString .= ',';
        }
        return $arrayString . "\n" . substr($tabs, 1) . ']';
    }
}

