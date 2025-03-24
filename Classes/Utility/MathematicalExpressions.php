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

/**
 * Custom exception for mathematical expressions.
*/
final class MathematicalExpressionsException extends Exception {}

/**
 * Class to lex, parse, and compile mathematical string expressions.
* Usage: $me = new MathematicalExpressions(); $res = $me->compileExpression('50+((-43*5)/3*2+8-3)');
*/
final class MathematicalExpressions
{
    /**
     * Array to store numbers from the expression.
    */
    private $numbers = [];

    /**
     * Array to store operators from the expression.
    */
    private $operators = [];

    /**
     * Constants for return types.
    */
    public const RETURN_FLOAT = 1;
    public const RETURN_INT = 2;
    public const RETURN_STRING = 3;

    /**
     * Set the current number in the expression.
    *
    * @param float|null $currentNumber The current number being processed.
    * @param string|null $numberPrefix Prefix for the number (e.g., negative sign).
    * @param bool $decimalPointFound Whether a decimal point has been encountered.
    * @param int $decimalRadix The radix for decimal numbers (default is 10).
    */
    private function _setCurrentNumber(&$currentNumber, &$numberPrefix, &$decimalPointFound, &$decimalRadix)
    {
        $decimalPointFound = false;
        $decimalRadix = 10;
        if ($currentNumber !== null) {
            $this->numbers[] = $numberPrefix === '-' ? (0 - $currentNumber) : $currentNumber;
            $numberPrefix = null;
            $currentNumber = null;
        }
    }

    /**
     * Parse and compile a partial expression.
    *
    * This method calculates multiplications and divisions first, then additions and subtractions.
    *
    * @return string The result of the compiled expression as a string.
    */
    private function _parseAndCompilePartial()
    {
        // Calculate multiplications and divisions first
        $exitLoop = false;
        $len = count($this->operators);
        if ($len >= count($this->numbers)) {
            throw new MathematicalExpressionsException('Expression syntax error. Count of operators is equal to or greater than numbers.');
        }

        for ($i = 0; $i < $len; $i++) {
            if (!isset($this->operators[$i]) || $exitLoop) {
                break;
            }
            switch ($this->operators[$i]) {
                case '*':
                    $this->numbers[$i] *= $this->numbers[$i + 1];
                    array_splice($this->numbers, ($i + 1), 1);
                    array_splice($this->operators, $i, 1);
                    $i--;
                    break;
                case '/':
                    $this->numbers[$i] /= $this->numbers[$i + 1];
                    array_splice($this->numbers, ($i + 1), 1);
                    array_splice($this->operators, $i, 1);
                    $i--;
                    break;
                case '%':
                    $this->numbers[$i] %= $this->numbers[$i + 1];
                    array_splice($this->numbers, ($i + 1), 1);
                    array_splice($this->operators, $i, 1);
                    $i--;
                    break;
                case '+':
                case '-':
                    break;
                default:
                    // Uh-oh, something's wrong here - terminate!
                    $exitLoop = true;
                    break;
            }
        }

        // Finalize additions and subtractions
        $len = count($this->operators);
        if ($len >= count($this->numbers)) {
            throw new MathematicalExpressionsException('Expression syntax error. Count of operators is equal to or greater than numbers.');
        }
        $exitLoop = false;
        while (count($this->operators)) {
            if ($exitLoop) {
                break;
            }
            switch ($this->operators[0]) {
                case '+':
                    $this->numbers[0] += $this->numbers[1];
                    array_splice($this->numbers, 1, 1);
                    array_splice($this->operators, 0, 1);
                    break;
                case '-':
                    $this->numbers[0] -= $this->numbers[1];
                    array_splice($this->numbers, 1, 1);
                    array_splice($this->operators, 0, 1);
                    break;
                default:
                    $exitLoop = true;
                    break;
            }
        }

        return strval($this->numbers[0]);
    }

    /**
     * Clean the expression by removing unnecessary parentheses around numbers.
    *
    * @param string $expression The mathematical expression to clean.
    */
    private function _cleanExpression(&$expression)
    {
        $matches = [];
        preg_match_all("/(\([+\-]?[0-9]*\))/", $expression, $matches);
        foreach ($matches[0] as $match) {
            $expression = str_replace($match, trim($match, "()"), $expression);
        }
    } 

    /**
     * Lex a partial expression into numbers and operators.
     *
     * This method iterates through the expression character by character, identifying numbers and operators.
     *
     * @param string $expression The partial expression to lex.
     */
    private function _lexPartial(string $expression)
    {
        $len = strlen($expression);
        $this->operators = [];
        $this->numbers = [];
        $numberPrefix = null;
        $currentNumber = null;
        $decimalPointFound = false;
        $decimalRadix = 10;
        $radix = 10;

        for ($i = 0; $i < $len; $i++) {
            switch ($expression[$i]) {
                case '+':
                    $this->_setCurrentNumber($currentNumber, $numberPrefix, $decimalPointFound, $decimalRadix);
                    if ($i == 0 || !is_numeric($expression[$i - 1])) {
                        $numberPrefix = '+';
                    } else {
                        $this->operators[] = '+';
                    }
                    break;
                case '-':
                    $this->_setCurrentNumber($currentNumber, $numberPrefix, $decimalPointFound, $decimalRadix);
                    if ($i == 0 || !is_numeric($expression[$i - 1])) {
                        $numberPrefix = '-';
                    } else {
                        $this->operators[] = '-';
                    }
                    break;
                case '*':
                    $this->_setCurrentNumber($currentNumber, $numberPrefix, $decimalPointFound, $decimalRadix);
                    $this->operators[] = '*';
                    break;
                case '/':
                    $this->_setCurrentNumber($currentNumber, $numberPrefix, $decimalPointFound, $decimalRadix);
                    $this->operators[] = '/';
                    break;
                case '%':
                    $this->_setCurrentNumber($currentNumber, $numberPrefix, $decimalPointFound, $decimalRadix);
                    $this->operators[] = '%';
                    break;
                default:
                    if (is_numeric($expression[$i])) {
                        if ($currentNumber === null) {
                            $currentNumber = intval($expression[$i]);
                        } else {
                            if ($decimalPointFound === false) {
                                $currentNumber *= $radix;
                                $currentNumber += intval($expression[$i]);
                            } else {
                                $tempValue = intval($expression[$i]);
                                $tempValue /= $decimalRadix;
                                $decimalRadix *= 10;
                                $currentNumber += $tempValue;
                            }
                        }
                    } elseif ($expression[$i] === '.') {
                        if ($currentNumber === null) {
                            $currentNumber = 0;
                            $decimalPointFound = true;
                        } else {
                            if ($decimalPointFound === true) {
                                throw new MathematicalExpressionsException('Invalid expression syntax. Multiple decimal points found in number');
                            } else {
                                $decimalPointFound = true;
                            }
                        }
                    }
                    break;
            }
        }
        $this->_setCurrentNumber($currentNumber, $numberPrefix, $decimalPointFound, $decimalRadix);
    }

    /**
     * Compile and calculate a mathematical string expression.
     *
     * This method supports the four standard math operators, parentheses, and dot operations.
     * It also supports alternative operators like the middle dot · (multiplication) and the ratio : (division).
     * Variables can be replaced with values from the $mergeFields array.
     *
     * @param string $expression The mathematical string expression.
     * @param array $mergeFields A map of variables in format ["variableName" => value, ...]. Defaults to an empty array.
     * @param int|null $returnType Determines the return value type. Can be set using the constants
     * MathematicalExpressions::RETURN_STRING, MathematicalExpressions::RETURN_FLOAT, or MathematicalExpressions::RETURN_INT.
     * Defaults to MathematicalExpressions::RETURN_STRING.
     *
     * @return mixed The compiled expression result. Type can be string, float, or int based on $returnType.
     */
    public function compileExpression(string $expression, array $mergeFields = [], ?int $returnType = MathematicalExpressions::RETURN_STRING): mixed
    {
        if (!empty($mergeFields)) {
            foreach ($mergeFields as $var => $val) {
                if (!is_numeric($val)) {
                    throw new MathematicalExpressionsException("Value $val is not a number. Variable: $var");
                }
                $expression = str_replace($var, $val, $expression);
            }
        }

        // Replace common alternative math operators
        $expression = str_replace('·', '*', str_replace(':', '/', $expression));

        // Validate the expression
        $validate = [];
        if (preg_match_all("/(\[^0-9()*\-\/+% \])+/", $expression, $validate)) {
            throw new MathematicalExpressionsException("Expression contains non-mathematical characters.");
        }

        // Remove all whitespaces
        $expression = preg_replace('/[ \n\r]+/', '', $expression);

        // Clean from trash input
        $this->_cleanExpression($expression);

        $partialExpressions = [];

        // First extract and calculate all nested expressions wrapped by parentheses
        while (preg_match_all("/\(([+\-]?[0-9]+[+\-*\/%]{1}[+\-]?[0-9]*[.]*[0-9]+)+\)/", $expression, $partialExpressions)) {
            $len = count($partialExpressions[0]);
            for ($i = 0; $i < $len; $i++) {
                $this->_lexPartial($partialExpressions[0][$i]);
                $expression = str_replace($partialExpressions[0][$i], $this->_parseAndCompilePartial(), $expression);
            }
        }

        // Finalize the calculation
        $this->_lexPartial($expression);

        return $returnType === MathematicalExpressions::RETURN_INT ? intval($this->_parseAndCompilePartial()) :
            ($returnType === MathematicalExpressions::RETURN_FLOAT ? floatval($this->_parseAndCompilePartial()) :
            $this->_parseAndCompilePartial());
    }

    /**
     * Compile and calculate multiple mathematical string expressions to the desired output type.
     *
     * This method supports the four standard math operators, parentheses, and dot operations.
     * It also supports alternative operators like the middle dot · (multiplication) and the ratio : (division).
     * Variables can be replaced with values from the $mergeFields array.
     *
     * @param string $expression The first mathematical string expression.
     * @param array|null $mergeFields A map of variables in format ["variableName" => value, ...]. Defaults to null.
     * @param int|null $returnType Determines the return value type. Can be set using the constants
     * MathematicalExpressions::RETURN_STRING, MathematicalExpressions::RETURN_FLOAT, or MathematicalExpressions::RETURN_INT.
     * Defaults to MathematicalExpressions::RETURN_STRING.
     * @param string ...$expressions Additional mathematical string expressions.
     *
     * @return array The compiled expressions in the same order as the inputs ($expression, $expressions...).
     * Type can be string, float, or int based on $returnType.
     */
    public function compileExpressions(
        string $expression,
        ?array $mergeFields = [],
        ?int $returnType = MathematicalExpressions::RETURN_STRING,
        ?string ...$expressions
    ): array {
        $_expressions = [];
        $_expressions[] = $this->compileExpression($expression, $mergeFields, $returnType);
        foreach ($expressions as $_expression) {
            $_expressions[] = $this->compileExpression($_expression, $mergeFields, $returnType);
        }
        return $_expressions;
    }
}

?>