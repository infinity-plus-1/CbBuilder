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

use Exception;
use ReflectionClass;
use Symfony\Component\Filesystem\Filesystem;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * A class containing useful helper functions.
*/
final readonly class Utility
{
    /**
     * Converts a key-value string into an array.
    *
    * @param string $expression The string to convert, formatted as "key: value, key2: value2".
    *
    * @return array An array where keys are the identifiers from the string and values are the corresponding values.
    */
    public static function keyValueStringToArray(string $expression): array
    {
        $arr = $matches = [];
        preg_match_all('/(\w+):\s*([\'"]?)(.*?)\2(?=\s*,|\s*$)/', $expression, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) $arr[$match[1]] = $match[3];
        return $arr;
    }

    /**
     * Returns the last element of an array without moving the internal pointer.
    *
    * @param array $arr The array to return the last element from.
    *
    * @return mixed The last element of the given array.
    */
    public static function array_last(array $arr): mixed
    {
        return $arr[sizeof($arr) - 1];
    }

    /**
     * Checks whether a character at a given position in a string is escaped.
    *
    * @param string $string The string to check.
    *
    * @param int $pos The position of the character to check.
    *
    * @return bool True if the character is escaped, false otherwise.
    */
    public static function isEscaped(string $string, int $pos): bool
    {
        $count = 0;
        while ($pos-- >= 0 && $string[$pos] === '\\') $count++;
        return $count % 2 !== 0; 
    }

    /**
     * Splits a string into an array using a delimiter while preserving quoted substrings.
    *
    * @param string $delimiter The character or string used to separate the input string.
    * Matching occurrences will be omitted in the output array.
    *
    * @param string $string The input string to split into chunks.
    *
    * @param ?int $limit Similar to the $limit parameter in PHP's explode() function.
    *
    * @return string[] A numerically indexed array containing the split parts of the input string.
    */
    public static function stringSafeExplode(string $delimiter, string $string, ?int $limit = PHP_INT_MAX): array {
        if ($delimiter === '') return [];
        if ($limit > 0) $limit--;

        $_limit = $limit > 0 ? $limit : ($limit < 0 ? PHP_INT_MAX : 0);
        $result = [];
        $len = strlen($string);
        $delimiterLen = strlen($delimiter) - 1;
        $lastCuttingPosition = 0;
        $insideQuotes = '';
        $firstDelimiterChar = $delimiter[0];
        $isMatch = true;
        $i = 0;

        for (; $i < $len && $_limit; $i++) { 
            $char = $string[$i];
            switch ($char) {
                case '"':
                    if (!self::isEscaped($string, $i) && $insideQuotes !== "'")
                        $insideQuotes = $insideQuotes === '"' ? '' : '"';
                    break;
                case "'":
                    if (!self::isEscaped($string, $i) && $insideQuotes !== '"')
                        $insideQuotes = $insideQuotes === "'" ? '' : "'";
                    break;
                default:
                    if (!$insideQuotes) {
                        if ($char === $firstDelimiterChar) {
                            $isMatch = true;
                            for ($j = $i + 1, $k = 1; $k < $delimiterLen; $j++, $k++) { 
                                if ($j >= $len || $string[$j] !== $delimiter[$k]) {
                                    $isMatch = false;
                                    break;
                                }
                            }
                            if ($isMatch) {
                                $result[] = substr($string, $lastCuttingPosition, ($i - $lastCuttingPosition));
                                $i += $delimiterLen + 1;
                                $lastCuttingPosition = $i;
                                $_limit--;
                            }
                        }
                    }
                    break;
            }
        }
        $result[] = substr($string, $lastCuttingPosition, $len);
        if ($limit < 0) $result = array_slice($result, 0, (sizeof($result) + $limit));
        return $result;
    }

    /**
     * Splits a string into an array using a delimiter while preserving quoted substrings and trims each part.
    *
    * @param string $delimiter The character or string used to separate the input string.
    * Matching occurrences will be omitted in the output array.
    *
    * @param string $string The input string to split into chunks.
    *
    * @param ?int $limit Similar to the $limit parameter in PHP's explode() function.
    *
    * @param string $characters Characters to trim from each part. Defaults to whitespace.
    *
    * @return string[] A numerically indexed array containing the trimmed and split parts of the input string.
    */
    public static function stringSafeTrimExplode(
        string $delimiter,
        string $string,
        ?int $limit = PHP_INT_MAX,
        string $characters = ''
    ): array {
        $result = self::stringSafeExplode($delimiter, $string, $limit);
        $len = count($result);
        for ($i = 0; $i < $len; $i++) { 
            $result[$i] = self::trimAlwaysWhitespace($result[$i], $characters);
        }
        return $result;
    }

    /**
     * Skips whitespace characters in a string starting from a given index.
     *
     * @param string $string The string to process.
     *
     * @param int &$index The current index in the string. This will be incremented past whitespace characters.
     *
     * @param ?int $len Optional length limit. If negative, it defaults to the end of the string.
     *
     * @return void
     */
    public static function skipWhitespace(string $string, int &$index, ?int $len = -1): void
    {
        $breakOut = false;
        $len--;
        if ($len < 0) $len = strlen($string) - 1;
        while ($index < $len && !$breakOut) {
            $char = $string[$index];
            switch ($char) {
                case "\n":
                case "\r":
                case "\t":
                case "\v":
                case "\f":
                case " ":
                    $index++;
                    break;
                default:
                    $breakOut = true;
                    break;
            }
        }
    }

    /**
     * Skips whitespace characters in a string starting from a given index and moving backward.
     *
     * @param string $string The string to process.
     *
     * @param int &$index The current index in the string. This will be decremented past whitespace characters.
     *
     * @return void
     */
    public static function skipWhitespaceBackward(string $string, int &$index): void
    {
        $breakOut = false;
        while ($index >= 0 && !$breakOut) {
            $char = $string[$index];
            switch ($char) {
                case "\n":
                case "\r":
                case "\t":
                case "\v":
                case "\f":
                case " ":
                    $index--;
                    break;
                default:
                    $breakOut = true;
                    break;
            }
        }
    }

    /**
     * Injects a merge field into a variable string.
     *
     * @param string $variable The placeholder string where the merge field will be injected.
     *
     * @param string $prefixToken The prefix token of the merge field.
     *
     * @param string $mergeFieldIdentifier The identifier of the merge field.
     *
     * @param mixed $value The value to inject into the merge field.
     *
     * @param ?string $suffixToken Optional suffix token of the merge field.
     *
     * @return mixed The modified string with the injected value, or the value itself if the variable matches the merge field pattern.
     */
    public static function injectMergeField(
        string $variable,
        string $prefixToken,
        string $mergeFieldIdentifier,
        mixed $value,
        ?string $suffixToken = ''
    ): mixed {
        $pos = 0;
        $prefixLen = strlen($prefixToken);
        $suffixLen = strlen($suffixToken);
        // $variable is just a placeholder, so we can return the corresponding type directly.
        if (($prefixToken . $mergeFieldIdentifier . $suffixToken) === $variable) {
            if (is_string($value)) {
                $len = strlen($value);
                $lowVal = strtolower($value);
                if ($lowVal === 'true') return true;
                if ($lowVal === 'false') return false;
                if (is_numeric($value)) {
                    return str_contains($value, '.') ? floatval($value) : intval($value);
                }
                if ($value[0] === '[' && $value[$len-1] === ']') return json_decode($value);
                if ($value[0] === '{' && $value[$len-1] === '}') return json_decode($value);
                return $value;
            } else return $value;
        }

        if (!is_string($value)) {
            if (is_bool($value)) $value = $value === true ? 'true' : 'false';
            if (is_numeric($value)) $value = strval($value);
            if (is_array($value)) $value = json_encode($value);
            if (is_object($value)) $value = method_exists($value, '__toString') ? $value->__toString() : 'object';
        }

        $identifierLen = strlen($mergeFieldIdentifier);
        while (($pos = strpos($variable, $prefixToken, $pos)) !== false) {
            if ((strpos($variable, $mergeFieldIdentifier, ($pos+$prefixLen)) === ($pos+$prefixLen)) && !self::isEscaped($variable, $pos)) {
                if ($suffixToken === '' || strpos($variable, $suffixToken, ($pos+$prefixLen+$identifierLen)) === ($pos+$prefixLen+$identifierLen)) {
                    $variable = substr($variable, 0, $pos) . $value . substr($variable, ($pos+$prefixLen+$identifierLen+$suffixLen));
                }
            }
            $pos++;
        }
        return $variable;
    }


    /**
     * Converts a mixed value into a string.
     *
     * @param mixed $value The value to convert.
     *
     * @return string The string representation of the value.
     */
    public static function toString(mixed $value): string
    {
        if (is_string($value)) return $value;
        if (is_numeric($value)) return strval($value);
        if (is_bool($value)) return $value === true ? 'true' : 'false';
        if (is_null($value)) return 'null';
        if (is_array($value)) return serialize($value);
        if (is_object($value)) {
            if (method_exists($value, '__toString')) return $value->__toString();
            else {
                $reflectionClass = new ReflectionClass($value);
                $classArray = [];
                $classArray['Name'] = $reflectionClass->getName();
                $classArray['Attributes'] = $reflectionClass->getAttributes();
                $classArray['Constants'] = $reflectionClass->getConstants();
                $classArray['DefaultProperties'] = $reflectionClass->getDefaultProperties();
                $classArray['Properties'] = $reflectionClass->getProperties();
                $classArray['Traits'] = $reflectionClass->getTraits();
                return serialize($classArray);
            }
        }
        throw new Exception("Unknown type cannot be converted to string.");
    }

    /**
     * Converts a mixed value into a number (int or float).
     *
     * @param mixed $value The value to convert.
     *
     * @return int|float|bool The numeric representation of the value, or false if conversion fails.
     */
    public static function toNumber(mixed $value): int|float|bool
    {
        if (is_int($value)) return $value;
        if (is_float($value)) return $value;
        if (is_string($value) && is_numeric($value)) {
            return str_contains($value, '.') ? floatval($value) : intval($value);
        }
        return false;
    }

    /**
     * Performs a regular expression match and returns the matched string.
     *
     * @param string $pattern The regular expression pattern.
     *
     * @param string $subject The string to search in.
     *
     * @return string|null The matched string if found, otherwise null.
     */
    public static function preg_match_str(string $pattern, string $subject): string|null
    {
        return preg_match($pattern, $subject, $matches) ? $matches[0] : null;
    }

    /**
     * Finds the position of a substring in a string while preserving quoted substrings.
     *
     * @param string $haystack The string to search in.
     *
     * @param string $needle The substring to find.
     *
     * @param int $offset The starting position for the search.
     *
     * @param bool $ignoreWhitespaces Whether to ignore whitespace characters during the search.
     *
     * @return int The position of the substring if found, otherwise -1.
     */
    public static function stringSafeStrpos(string $haystack, string $needle, int $offset = 0, bool $ignoreWhitespaces = false): int
    {
        $hLen = strlen($haystack);
        $nLen = strlen($needle);
        $mLen = $hLen - $nLen;
        $firstChar = $needle[0];
        $isString = '';
        
        $isMatch = function (int $index) use ($haystack, $needle, $nLen, $mLen, $ignoreWhitespaces) {
            $len = $nLen;
            if ($nLen === 1) {
                return $haystack[$index] === $needle[0];
            }
            for ($i=1, $index++; $i < $len; $i++, $index++) {
                if ($ignoreWhitespaces === true) self::skipWhitespace($haystack, $index, $mLen);
                if ($haystack[$index] !== $needle[$i]) return false;
            }
            return true;
        };

        if ($mLen < 0) {
            return -1;
        } elseif ($mLen === 0) {
            return ($haystack === $needle) ? 0 : -1;
        }
        
        for ($i=$offset; $i <= $mLen; $i++) { 
            if ($ignoreWhitespaces === true) self::skipWhitespace($haystack, $i, $mLen);
            $char = $haystack[$i];
            switch ($char) {
                case '"':
                    if ($isString === '"') {
                        if (!self::isEscaped($haystack, $i-1)) {
                            $isString = '';
                        } else {
                            if ($char === $firstChar) {
                                if ($isMatch($i) === true) return $i;
                            }
                        }
                    } else {
                        if ($isString === '') {
                            $isString = '"';
                        }
                    }
                    break;
                case "'":
                    if ($isString === "'") {
                        if (!self::isEscaped($haystack, $i-1)) {
                            $isString = '';
                        } else {
                            if ($char === $firstChar) {
                                if ($isMatch($i) === true) return $i;
                            }
                        }
                    } else {
                        if ($isString === '') {
                            $isString = "'";
                        }
                    }
                    break;
                default:
                    if ($isString === '') {
                        if ($char === $firstChar) {
                            if ($isMatch($i) === true) return $i;
                        }
                    }
                    break;
            }
        }
        return -1;
    }

    /**
     * Finds the position of a substring in a string while preserving quoted substrings, searching backward.
     *
     * @param string $haystack The string to search in.
     *
     * @param string $needle The substring to find.
     *
     * @param int $offset The starting position for the search.
     *
     * @param bool $ignoreWhitespaces Whether to ignore whitespace characters during the search.
     *
     * @return int The position of the substring if found, otherwise -1.
     */
    public static function stringSafeStrposBackward(string $haystack, string $needle, int $offset = 0, bool $ignoreWhitespaces = false): int
    {
        $hLen = strlen($haystack);
        $nLen = strlen($needle);
        $mLen = $hLen - $nLen;
        $firstChar = $needle[$nLen - 1];
        $isString = '';
        
        $isMatch = function (int $index) use ($haystack, $needle, $nLen, $mLen, $ignoreWhitespaces) {
            if ($nLen === 1) {
                return $haystack[$index] === $needle[0];
            }
            for ($i = ($nLen - 2), $index--; $i >= 0; $i--, $index--) {
                if ($ignoreWhitespaces === true) self::skipWhitespaceBackward($haystack, $index);
                if ($haystack[$index] !== $needle[$i]) return false;
            }
            return true;
        };

        if ($mLen < 0) {
            return -1;
        } elseif ($mLen === 0) {
            return ($haystack === $needle) ? 0 : -1;
        }
        for ($i=$offset > 0 ? $offset : ($hLen - 1); $i >= 0; $i--) {

            if ($ignoreWhitespaces === true) self::skipWhitespaceBackward($haystack, $i);
            $char = $haystack[$i];
            switch ($char) {
                case '"':
                    if ($isString === '"') {
                        if (!self::isEscaped($haystack, $i-1)) {
                            $isString = '';
                        } else {
                            if ($char === $firstChar) {
                                if ($isMatch($i) === true) return ($i - $nLen + 1);
                            }
                        }
                    } else {
                        if ($isString === '') {
                            $isString = '"';
                        }
                    }
                    break;
                case "'":
                    if ($isString === "'") {
                        if (!self::isEscaped($haystack, $i-1)) {
                            $isString = '';
                        } else {
                            if ($char === $firstChar) {
                                if ($isMatch($i) === true) return ($i - $nLen + 1);
                            }
                        }
                    } else {
                        if ($isString === '') {
                            $isString = "'";
                        }
                    }
                    break;
                default:
                    if ($isString === '') {
                        if ($char === $firstChar) {
                            if ($isMatch($i) === true) return ($i - $nLen + 1);
                        }
                    }
                    break;
            }
        }
        return -1;
    }

    /**
     * Trims whitespace from a string, ensuring that whitespace is always removed regardless of the current locale.
     *
     * @param string $string The string to trim.
     *
     * @param string $characters Optional characters to trim from the string. Defaults to whitespace.
     *
     * @return string The trimmed string.
     */
    public static function trimAlwaysWhitespace(string $string, string $characters = ''): string
    {
        return trim($string, " \n\r\t\v\0" . $characters);
    }

    /**
     * Dumps variables to a file.
     *
     * @param string $file The path to the file where the dump will be written.
     *
     * @param bool $append Whether to append to the file or overwrite it.
     *
     * @param mixed ...$values The variables to dump.
     *
     * @return void
     */
    public static function dumpToFile(string $file, bool $append, mixed ...$values): void
    {
        ob_start();
        dump($values);
        $dumpOutput = ($append ? file_get_contents($file) : '' ) . ob_get_clean();
        file_put_contents($file, $dumpOutput);
    }

    /**
     * Removes zero-indexed nesting from an array.
     *
     * @param array $array The array to process.
     *
     * @return array The array with zero-indexed nesting removed.
     */
    public static function skipZeroIndexed(array $array): array
    {
        while (array_key_exists(0, $array)) {
            $array = $array[0];
        }
        return $array;
    }

    /**
     * Checks if a file ends with a newline character.
     *
     * @param string $filePath The path to the file to check.
     *
     * @return bool|null True if the file ends with a newline, false otherwise. Returns null if the file cannot be opened.
     */
    public static function fileEndsWithNewline(string $filePath): bool|null
    {
        $file = fopen($filePath, 'r');
        if (!$file) {
            return null;
        }
        
        fseek($file, -1, SEEK_END);
        $lastByte = fread($file, 1);
        
        fclose($file);
        
        return $lastByte === "\n";
    }

    /**
     * Checks if a string ends with a newline character.
     *
     * @param string $string The string to check.
     *
     * @return bool True if the string ends with a newline, false otherwise.
     */
    public static function endsWithNewline(string $string): bool
    {
        return $string === '' ? false : $string[strlen($string) - 1] === "\n";
    }

    /**
     * Checks if a file or directory exists and creates those if they don't.
     * 
     * @param string $path The path of the directories or file
     * @param bool $isPath If the path contains directories only
     * 
     * @return bool Returns true if the dir or file did not exists and has been created.
     */
    public static function createIfNot(string $path, bool $isDir = true): bool
    {
        $filesystem = new Filesystem();
        if ($isDir) {
            if (!$filesystem->exists($path)) {
                GeneralUtility::mkdir_deep($path);
                return true;
            } else {
                return false;
            }
        } else {
            if (!$filesystem->exists($path)) {
                $splittedPath = array_reverse(explode('/', $path));
                $fileName = array_shift($splittedPath);
                $path = implode('/', array_reverse($splittedPath));
                GeneralUtility::mkdir_deep($path);
                $filesystem->touch($path . "/$fileName");
                return true;
            } else {
                return false;
            }
        }
    }
}

?>