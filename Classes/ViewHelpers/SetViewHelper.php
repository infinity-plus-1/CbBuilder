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

namespace DS\CbBuilder\ViewHelpers;

use DS\CbBuilder\Utility\MathematicalExpressions;
use DS\CbBuilder\Utility\Utility;
use Exception;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Custom exception for the SetViewHelper.
 */
class SetViewHelperException extends Exception {}

/**
 * Adds variable(s) to the global variable provider for use in Fluid templates.
 * 
 * Arguments:
 * @param mixed $value Add one or more variables using key-value notation, separated by commas.
 * 
 * These pairs can be passed as an array using {}:  
 * <cb:set value="{key1: 'value1', key2: 'value2'}" />
 * 
 * Alternatively, they can be passed as a string by omitting the {}:  
 * <cb:set value="key1: 'value1', key2: 43" />
 * 
 * Keys can be pure integers or strings, with or without quotes.
 * 
 * Values can have any type if passed as an array. If provided as a string, they can be booleans, integers, floats, strings, arrays, or objects (in JSON format).
 * 
 * Values enclosed in single or double quotes are treated as strings, except when preceded by =, $, {, or [. More details on this later.
 * 
 * You can omit quotes unless using the words "true" or "false," numeric values like 9 or 9.0, or when using a comma or colon in the text.  
 * To avoid ambiguity, it is recommended to wrap strings in quotes.
 * 
 * Special Characters:
 * 
 * $: Indicates a merge field (placeholder), which will be replaced by a value if the same identifier exists in the mergeFields argument  
 * or in the global variable provider. (The mergeFields value takes priority if the identifier exists in both.)
 * 
 * {: If the string starts and ends with curly brackets, json_decode will be called on this variable.  
 * Ensure the JSON syntax is valid in this case.
 * 
 * [: Similar to {, but the JSON should represent a simple array.
 * 
 * =: Indicates a mathematical expression that will be computed and returned as a number (float or int).
 * 
 * Special characters do not need to be enclosed in quotes, as long as the conditions mentioned above are met.
 * 
 * To escape a special character, prepend it with \.
 * 
 * Examples:
 * 
 * $: <cb:set mergeFields="{placeholder: 'text'}">myVar: The following $placeholder will be replaced by the word 'text'.< /fh:set>
 * 
 * $: <cb:set value="globalVar: global variable" />  
 *    <cb:set value="myVar: This is a $globalVar." />
 * 
 * [: <cb:set value="arr: '[\\"City\\", \\"Country\\", \\"River\\"]'" />
 * 
 * {: <cb:set value="obj: '{ \\"name\\":\\"Jack\\", \\"age\\":43, \\"hobbies\\":[\\"fishing\\", \\"darts\\", \\"watching grass grow\\"] }'" />
 * 
 * =: <cb:set value="myExp: '= 43+74-(-32*8)+((45/15)*1000)'" />
 * 
 * =: <cb:set>number2: 3000< /cb:set>  
 *    <cb:set value="myExp: '= (-55/11+$number$-($number2$*43+12345))'" mergeFields="{number: 2000}" />
 * 
 * If you copy and run the expression above, you'll notice that it returns an incorrect result. Debugging reveals that the expression is  
 * evaluated as (-55/11+2000-(20002*43+12345)) instead of (-55/11+2000-(3000*43+12345)).  
 * This happens because the mergeFields array is processed first, replacing both $number and $number2 incorrectly, since  
 * $number2 is partially matched as $number. The parser will always do an in-place replacement!
 * To prevent such issues, it is recommended to append another $ at the end of placeholders.
 * 
 * The correct expression should be:
 * 
 * <cb:set>number2: 3000< /cb:set>  
 * <cb:set value="myExp: '= (-55/11+$number$-($number2$*43+12345))'" mergeFields="{number: 2000}" />
 * 
 * @param mixed $value The variables to be set in the global scope.
 * @param array $mergeFields Define merge fields to replace placeholders in strings or mathematical expressions.  
 * Keys can be numerical or strings. Values can be of any type, but objects must implement a __toString function.  
 * Syntax: {key1: 1, 0: 'value1', key2: 'value2', 1: 43}
 * 
 * @param bool $force If false, the view helper will throw an exception if the variable name already exists in the global variable provider.  
 * Set to true to overwrite existing keys.
 */

final class SetViewHelper extends AbstractViewHelper
{
    /**
     * Whether to escape the output.
     */
    protected $escapeOutput = false;

    /**
     * Initializes the arguments for this view helper.
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('value', 'mixed', 'The variables to be set in the global scope.');
        $this->registerArgument('mergeFields', 'mixed', 'If the variable is a string or mathematical expression containing other variables, these can be forwarded here.');
        $this->registerArgument('force', 'boolean', 'If true, existing keys will be overwritten. Otherwise, an exception will be thrown.', false, false);
    }

    /**
     * Compiles a mathematical expression and returns the result as a float or int.
     *
     * @param string $expression The mathematical expression to compile.
     * @param array $mergeFields Merge fields to replace placeholders in the expression.
     *
     * @return int|float The result of the compiled expression.
     */
    private function _compileExpression(string $expression, array $mergeFields): int|float
    {
        $expression = $this->_injectMergefield($expression, $mergeFields);
        $me = new MathematicalExpressions();
        $value = $me->compileExpression($expression);
        return str_contains($value, '.') ? floatval($value) : intval($value);
    }

    /**
     * Injects merge fields into a string variable.
     *
     * @param string $variable The string to inject merge fields into.
     * @param array $mergeFields Merge fields to replace placeholders.
     *
     * @return string The variable with merge fields injected.
     */
    private function _injectMergefield(string $variable, array $mergeFields): string
    {
        $matches = [];
        preg_match_all("/(?<!\/)\\\$[a-zA-Z_][a-zA-Z0-9_]*\\\${0,1}/", $variable, $matches, PREG_PATTERN_ORDER);
        if (!empty($matches) && !empty($matches[0])) {
            $canUseMergefieldsArray = $mergeFields && is_array($mergeFields) && !empty($mergeFields);
            $matches[0] = array_unique($matches[0]);
            foreach ($matches[0] as $match) {
                $len = strlen($match);
                $suffixToken = $match[$len-1] === '$' ? '$' : '';
                $match = trim($match, '$');
                if ($canUseMergefieldsArray && isset($mergeFields[$match])) {
                    $variable = Utility::injectMergeField($variable, '$', $match, $mergeFields[$match], $suffixToken); 
                }
                else if ($this->renderingContext->getVariableProvider()->exists($match)) {
                    $variable = Utility::injectMergeField($variable, '$', $match, $this->renderingContext->getVariableProvider()->get($match), $suffixToken);
                }
            }
        }
        return $variable;
    }

    /**
     * Validates a variable name against a list of reserved names.
     *
     * @param mixed $name The name to validate.
     *
     * @return bool Whether the name is valid.
     */
    #[Pure]
    private function _nameValidator($name): bool
    {
        return in_array($name, [
            'settings', 'data', 'current'
        ]);
    }

    /**
     * Handles a string value by processing special syntaxes like JSON or mathematical expressions.
     *
     * @param string $value The string value to handle.
     * @param array $mergeFields Merge fields to replace placeholders.
     *
     * @return mixed The processed value.
     */
    private function _handleStringValue(string $value, array $mergeFields): mixed
    {
        $value = substr($value, 1, -1);
        if ($value === '') return '';
        $len = strlen($value);
        $lastChar = $value[$len-1];
        switch ($value[0]) {
            case '{':
                if ($lastChar === '}') {
                    return json_decode($value);
                } else {
                    throw new SetViewHelperException (
                        "Error in syntax: Expected closing '}' at the end of variable $value." .
                        "Wrap it with single or double quotes if the variable was supposed to be of type string."
                    );
                }
                break;
            case '[':
                if ($lastChar === ']') {
                    return json_decode($value);
                } else {
                    throw new SetViewHelperException (
                        "Error in syntax: Expected closing ']' at the end of variable $value." .
                        "Wrap it with single or double quotes if the variable was supposed to be of type string."
                    );
                }
                break;
            case '=':
                $value = substr($value, 1, $len);
                return $this->_compileExpression($value, $mergeFields);
                break;
            default:
                if ($value[0] === '\\') $value = substr($value, 1, $len);
                return $this->_injectMergefield($value, $mergeFields);
                break;
        }
    }

    /**
     * Processes a string variable by handling quotes and special syntaxes.
     *
     * @param string $value The string variable to process.
     * @param array $mergeFields Merge fields to replace placeholders.
     *
     * @return mixed The processed variable.
     */
    private function _processStringVariable(string $value, array $mergeFields): mixed
    {
        $lastChar = $value[strlen($value)-1];
        switch ($value[0]) {
            case '"':
                if ($lastChar === '"') {
                    $value = $this->_handleStringValue($value, $mergeFields);
                } else {
                    throw new SetViewHelperException (
                        "Error in syntax: Expected closing '\"' at the end of variable $value. " .
                        "Wrap it with single or double quotes if the variable was supposed to be of type string."
                    );
                }
                break;
            case "'":
                if ($lastChar === "'") {
                    $value = $this->_handleStringValue($value, $mergeFields);
                } else {

                    throw new SetViewHelperException (
                        "Error in syntax: Expected closing \"'\" at the end of variable $value. " .
                        "Wrap it with single or double quotes if the variable was supposed to be of type string."
                    );
                }
                break;
            case '{':
                if ($lastChar === '}') {
                    return json_decode($value);
                } else {
                    throw new SetViewHelperException (
                        "Error in syntax: Expected closing '}' at the end of variable $value." .
                        "Wrap it with single or double quotes if the variable was supposed to be of type string."
                    );
                }
                break;
            case '[':
                if ($lastChar === ']') {
                    return json_decode($value);
                } else {
                    throw new SetViewHelperException (
                        "Error in syntax: Expected closing ']' at the end of variable $value." .
                        "Wrap it with single or double quotes if the variable was supposed to be of type string."
                    );
                }
                break;
            case '=':
                $value = substr($value, 1, strlen($value));
                return $this->_compileExpression($value, $mergeFields);
                break;
            default:
                $lowVal = strtolower($value);
                if ($lowVal === 'true') $value = true;
                if ($lowVal === 'false') $value = false;
                if (is_numeric($value)) {
                    $value = str_contains($value, '.') ? floatval($value) : intval($value);
                }
                if (is_string($value)) $value = $this->_injectMergefield($value, $mergeFields);
                break;
        }
        return $value;
    }

    /**
     * Lexes variables by processing string values and replacing placeholders.
     *
     * @param mixed $variables Variables to lex, either as an array or a string in key-value format.
     * @param array $mergeFields Merge fields to replace placeholders in string values.
     *
     * @return array The lexed variables.
     */
    private function _lexVariables($variables, array $mergeFields): array
    {
        $results = [];
        if (is_array($variables)) {
            foreach ($variables as $key => $value) {
                $key = is_string($key) ? trim($key) : $key;
                if (is_string($value)) {
                    $value = trim($value);
                    $results[$key] = $this->_processStringVariable($value, $mergeFields);
                } else {
                    $results[$key] = $value;
                }
            }
        } else if (is_string($variables)) {
            $splittedVars = Utility::stringSafeExplode(',', $variables);
            $variables = [];
            foreach ($splittedVars as $variable) {
                if (str_contains($variable, ':')) {
                    $variable = Utility::stringSafeExplode(':', $variable);
                    if (sizeof($variable) !== 2) {
                        throw new SetViewHelperException(
                            "Syntax error: Variables must contain the format key: value. Found too many colons in the expression."
                        );
                    }
                    $key = is_string($variable[0]) ? trim($variable[0]) : $variable[0];
                    $value = trim($variable[1]);
                    $results[$key] = $this->_processStringVariable($value, $mergeFields);
                } else {
                    throw new SetViewHelperException("Syntax error: Variables must contain the format key: value.");
                }
            }
        } else {
            throw new SetViewHelperException("Variables must be of type array or string in the format key: value.");
        }
        return $results;
    }

    /**
     * Renders the view helper by setting variables in the global variable provider.
     */
    public function render(): void
    {
        $variables = $this->arguments['value'] ?? ($this->renderChildren() ?? NULL);
        $mergeFields = $this->arguments['mergeFields'] ?? [];
        if (!$variables) throw new SetViewHelperException('No variable(s) provided.');
        $variables = $this->_lexVariables($variables, $mergeFields);
        $globalVars = $this->renderingContext->getVariableProvider();
        foreach ($variables as $key => $variable)
        {
            if ($this->_nameValidator($key))
                throw new SetViewHelperException("Variable name '$key' is reserved. Please choose a different identifier.");

            if ($this->arguments['force'] && $globalVars->exists($key)) $globalVars->remove($key);

            $globalVars->add($key, $variable);
        }
    }

    /**
     * Returns the name of the content argument.
     *
     * @return string The name of the content argument.
     */
    public function getContentArgumentName(): string
    {
        return 'value';
    }
}

?>