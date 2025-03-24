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

use Doctrine\DBAL\Query\QueryBuilder;
use Exception;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use __IDE\Pure;
use TYPO3\CMS\Core\Database\Connection;

/**
 * Exception class for database query errors.
*/
class SimpleDatabaseQueryException extends Exception {}

/**
 * Class representing a collection of database query nodes.
*/
final class SimpleDatabaseQueryNodes extends SimpleDatabaseQueryNode
{
    /**
     * Array of simple database query nodes.
    *
    * @var array<SimpleDatabaseQueryNode>
    */
    protected array $nodes;

    /**
     * Initializes an empty collection of nodes.
    */
    public function __construct()
    {
        $this->nodes = [];
    }

    /**
     * Adds a node to the collection at the specified index.
    *
    * @param SimpleDatabaseQueryNode $node The node to add.
    * @param int $index The position where the node should be added.
    *
    * @throws SimpleDatabaseQueryException If there is a syntax error in the expression.
    */
    public function addNode(SimpleDatabaseQueryNode $node, int $index)
    {
        $type = $node->getType();
        switch ($type)
        {
            case SimpleDatabaseQueryNode::CLAUSE:
                $lastNode = $this->sizeof() > 0 ? Utility::array_last($this->nodes) : null;
                if ($lastNode && $lastNode->getType() === SimpleDatabaseQueryNode::CLAUSE)
                    throw new SimpleDatabaseQueryException("Syntax error in expression: Found clause operator at an incorrect position: $index.");
                if ($node->getLValue() === '')
                    throw new SimpleDatabaseQueryException("No column name is set at position: $index.");
                elseif ($node->getComparison() === null)
                    throw new SimpleDatabaseQueryException("No comparison operator is set at position: $index.");
                elseif ($node->getRValue() === null || $node->getRValue() === '')
                    throw new SimpleDatabaseQueryException("No value is set at position: $index.");
                break;
            case SimpleDatabaseQueryNode::LOGICAL:
                if ($this->sizeof() == 0)
                    throw new SimpleDatabaseQueryException("Expression cannot start with logical operators.");
                $lastNode = Utility::array_last($this->nodes);
                if ($lastNode && $lastNode->getType() === SimpleDatabaseQueryNode::LOGICAL)
                    throw new SimpleDatabaseQueryException("Syntax error in expression: Found logical operator at an incorrect position: $index.");
                break;
            case SimpleDatabaseQueryNode::GROUP:
                break;
            case SimpleDatabaseQueryNode::COMMA_GROUP:
                break;
            default:
                throw new SimpleDatabaseQueryException("Unknown error. Please verify that the type value ranges between 1 and 4: $type.");
                break;
        }

        // Note: The index parameter is not used in this implementation.
        //       It might be intended to insert at a specific position, but currently, it appends to the end.
        $this->nodes[] = $node;
    }

    /**
     * Returns the collection of nodes.
    *
    * @return array<SimpleDatabaseQueryNode> The list of nodes.
    */
    #[Pure]
    public function getNodes()
    {
        return $this->nodes;
    }

    /**
     * Returns the number of nodes in the collection.
    *
    * @return int The size of the node collection.
    */
    #[Pure]
    public function sizeof()
    {
        return count($this->nodes);
    }
} 

/**
 * Class representing a node in a simple database query.
 */
class SimpleDatabaseQueryNode
{
    /**
     * Constant for a clause node type.
     */
    public const CLAUSE = 1;

    /**
     * Constant for a logical operator node type.
     */
    public const LOGICAL = 2;

    /**
     * Constant for a group node type.
     */
    public const GROUP = 3;

    /**
     * Constant for a comma-separated group node type.
     */
    public const COMMA_GROUP = 4;

    /**
     * Initializes a new query node.
     *
     * @param string $lValue The left-hand value of the node.
     * @param int|null $type The type of the node. Defaults to CLAUSE.
     * @param string|null $comparison The comparison operator. Defaults to null.
     * @param mixed|null $rValue The right-hand value of the node. Defaults to null.
     */
    public function __construct(
        protected string $lValue,
        protected ?int $type = self::CLAUSE,
        protected ?string $comparison = null,
        protected mixed $rValue = null
    ) {}

    /**
     * Returns the type of the node.
     *
     * @return int|null The type of the node.
     */
    #[Pure]
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the left-hand value of the node.
     *
     * @return string The left-hand value.
     */
    #[Pure]
    public function getLValue()
    {
        return $this->lValue;
    }

    /**
     * Returns the comparison operator of the node.
     *
     * @return string|null The comparison operator.
     */
    #[Pure]
    public function getComparison()
    {
        return $this->comparison;
    }

    /**
     * Returns the right-hand value of the node.
     *
     * @return mixed The right-hand value.
     */
    #[Pure]
    public function getRValue()
    {
        return $this->rValue;
    }

    /**
     * Sets the left-hand value of the node.
     *
     * @param string $lValue The new left-hand value.
     */
    public function setLValue(string $lValue)
    {
        $this->lValue = $lValue;
    }

    /**
     * Sets the type of the node.
     *
     * @param int $type The new type of the node.
     */
    public function setType(int $type)
    {
        $this->type = $type;
    }

    /**
     * Sets the comparison operator of the node.
     *
     * @param string|null $comparison The new comparison operator.
     */
    public function setComparison(?string $comparison)
    {
        $this->comparison = $comparison;
    }

    /**
     * Sets the right-hand value of the node.
     *
     * @param mixed $rValue The new right-hand value.
     */
    public function setRValue(mixed $rValue)
    {
        $this->rValue = $rValue;
    }
}

/**
 * Class for executing simple database queries.
 * Usage:
 * 
 * $sqd = new SimpleDatabaseQuery();
 * 
 * if($sqd->tableExists('products')) {
 * 
 *  $res = $sqd->fetch('products', '*', 'stock >= 1 && category == $category && (title %% 'screw' || title %% 'nail')', 'price ASC',
 *  ['category' => 1], false, false);
 * 
 *  ...
 * 
 * }
 */
final class SimpleDatabaseQuery extends Connection
{
    /**
     * Query builder instance.
     */
    protected QueryBuilder $queryBuilder;

    /**
     * Collection of query clauses.
     */
    protected ?SimpleDatabaseQueryNodes $clauses;

    /**
     * Current query node being processed.
     */
    protected ?SimpleDatabaseQueryNode $currentNode;

    /**
     * Array of variables used in the query.
     */
    protected array $variables;

    /**
     * Number of groups in the query.
     */
    protected int $groupCount;

    /**
     * Flag indicating whether the current value is a column.
     */
    protected bool $isColumn;

    /**
     * String indicating the type of the current value.
     */
    protected string $isString;

    /**
     * Flag indicating whether the current variable is a string.
     */
    protected bool $currentVarIsString;

    /**
     * Flag indicating whether the current operation is a LIKE comparison.
     */
    protected bool $isLike;

    /**
     * Flag indicating whether the current operation is an IN comparison.
     */
    protected bool $isIn;

    /**
     * Mapping of field types to their respective length ranges.
     */
    const FIELD_TYPES = [
        'CHAR' => [1, 255],
        'VARCHAR' => [0, 65535],
        'BINARY' => [0, 255],
        'VARBINARY' => [0, 65535],
        'TINYTEXT' => [0, 255],
        'TEXT' => [0, 65535],
        'MEDIUMTEXT' => [0, 16777215],
        'LONGTEXT' => [0, 4294967295],
    
        'BIT' => [1, 64],
    
        'TINYINT' => 1,
        'SMALLINT' => 1,
        'MEDIUMINT' => 1,
        'INT' => 1,
        'BIGINT' => 1,
    
        'DATE' => null,
        'DATETIME' => null,
        'TIMESTAMP' => null,
        'TIME' => null,
        'YEAR' => null,
    
        'ENUM' => [0, 65535],
        'SET' => [0, 64],
    ];

    /**
     * Mapping of field indexes to their respective SQL definitions.
     */
    const FIELD_INDEXES = [
        'PRIMARY' => 'PRIMARY KEY', 
        'UNIQUE' => 'UNIQUE', 
        'INDEX' => 'INDEX'
    ];

    /**
     * Initializes a new instance of the query class.
     *
     * @param ConnectionPool|null $connection Optional connection pool instance.
     */
    public function __construct(
        protected ?ConnectionPool $connection = null
    )
    {
        $this->connection = $this->connection ?? GeneralUtility::makeInstance(ConnectionPool::class);
    }

    /**
     * Establishes a connection to the database for the specified table.
     *
     * @param string $table The name of the table to connect to.
     *
     * @return QueryBuilder The query builder instance for the table.
     */
    private function _establishConnection(string $table): QueryBuilder
    {
        return $this->connection->getQueryBuilderForTable($table);
    }

    /**
     * Creates a new query node with the specified left-hand value and type.
     *
     * @param string $lValue The left-hand value of the node.
     * @param int|null $type The type of the node. Defaults to CLAUSE.
     */
    private function _newQueryNode(string $lValue, ?int $type = SimpleDatabaseQueryNode::CLAUSE): void
    {
        if (!$this->currentNode)
        {
            $this->currentNode = new SimpleDatabaseQueryNode($lValue, $type);
        }
    }

    /**
     * Adds the current query node to the collection of clauses.
     *
     * @param int $index The position where the node should be added.
     */
    private function _addQueryNode(int $index): void
    {
        $this->clauses->addNode($this->currentNode, $index);
        $this->currentNode = null;
    }

    /**
     * Converts a value to its appropriate data type based on its content.
     *
     * @param mixed $value The value to convert.
     *
     * @return mixed The converted value.
     */
    private function _convertValue(mixed &$value): mixed
    {
        if (is_numeric($value))
        {
            if (str_contains($value, '.'))
                $value = floatval($value);
            else
                $value = intval($value);
        } else if (is_string($value)) {
            if (strtolower($value) === 'true')
                $value = 1;
            else if (strtolower($value) === 'false')
                $value = 0;
        }
        return $value;
    }

    /**
     * Adds a comparison operator to the current query node.
     *
     * @param string $operator The comparison operator to add.
     * @param string $expression The full expression being parsed.
     * @param int $index The current position in the expression.
     * @param int $len The length of the expression.
     * @param string $column The current column name.
     * @param mixed $value The current value being processed.
     *
     * @throws SimpleDatabaseQueryException If there is a syntax error in the expression.
     */
    private function _addComparisonOperator(string $operator, string $expression, int &$index, int $len, string &$column, mixed &$value): void
    {
        if (!$this->isString)
        {
            if ($index == $len)
                throw new SimpleDatabaseQueryException("Syntax error in expression. Wrong terminating character $operator.");
            $this->isColumn = false;
            $this->_newQueryNode($column);
            $column = '';
            $index++;
            switch ($operator)
            {
                case '<':
                    if ($expression[$index] === '=') {
                        $this->currentNode->setComparison('<=');
                    } else {
                        $this->currentNode->setComparison('<');
                        $index--;
                    }
                    break;
                case '>':
                    if ($expression[$index] === '=') {
                        $this->currentNode->setComparison('>=');
                    } else {
                        $this->currentNode->setComparison('>');
                        $index--;
                    }
                    break;
                case '=':
                    if ($expression[$index] !== '=')
                        throw new SimpleDatabaseQueryException("Syntax error in expression. Wrong comparison character at position $index.");
                    $this->currentNode->setComparison('==');
                    break;
                case '!':
                    if ($expression[$index] !== '=')
                        throw new SimpleDatabaseQueryException("Syntax error in expression. Wrong comparison character at position $index.");
                    $this->currentNode->setComparison('!=');
                    break;
                case '%':
                    if ($expression[$index] !== '%')
                        throw new SimpleDatabaseQueryException("Syntax error in expression. Wrong comparison character at position $index.");
                    $this->currentNode->setComparison('%%');
                    $this->isLike = true;
                    break;
                case '-':
                    if ($expression[$index] !== '>')
                        throw new SimpleDatabaseQueryException("Syntax error in expression. Wrong comparison character at position $index.");
                    $this->currentNode->setComparison('->');
                    $this->isIn = true;
                    break;
                default:
                    throw new SimpleDatabaseQueryException("Unknown error. Wrong operand $operator is forwarded to function _addCompareOperand");
                    break;
            }
            
        }
        else
        {
            if ($this->isColumn) $column .= $operator;
            else $value .= $operator;
        }
    }

    /**
     * Adds a value to the current query clause.
     *
     * @param string $expression The full expression being parsed.
     * @param int $index The current position in the expression.
     * @param bool $isLast Whether this is the last value in the expression.
     * @param mixed $value The value to add.
     *
     * @throws SimpleDatabaseQueryException If there is a syntax error in the expression.
     */
    private function _addValueToClause(string $expression, int $index, bool $isLast, mixed &$value): void
    {
        if ($this->isColumn) {
            $index++;
            throw new SimpleDatabaseQueryException (
                "Unknown error in syntax. Value is marked as a column name at expression character position $index. " .
                "Have you set a variable?"
            );
        }

        if (is_string($value)) {
            $value = htmlspecialchars($value);
        }

        if ($this->isString) {
            if (!$isLast) $index--;
            if ($expression[$index] !== '"' && $expression[$index] !== "'")
                throw new SimpleDatabaseQueryException (
                    "Error in syntax: No string termination character before end of expression or logical operator at position $index."
                );
            if ($expression[$index] !== $this->isString)
            {
                $closingCharacter = $expression[$index];
                throw new SimpleDatabaseQueryException (
                    "Error in syntax: String termination characters do not match. Opening character is $this->isString " .
                    "and closing character is $closingCharacter at position $index."
                );
            }   
        } else {
            if ($this->currentVarIsString) {
                $value = "'" . $value . "'";
                $this->currentVarIsString = false;
            } else  {
                $value = $this->_convertValue($value);
            }
        }

        if (is_string($value)) {
            if ($value !== '' && $this->isLike) $value = '%' . $value . '%';
            if ($value !== '') {
                $this->currentNode->setRValue($value);
                $this->_addQueryNode($index);
            }
        } else if ($value instanceof SimpleDatabaseQueryNode) {
            if ($value->getType() === SimpleDatabaseQueryNode::COMMA_GROUP) {
                $value->setLValue(trim($value->getLValue(), '()'));
                $splittedValues = Utility::stringSafeTrimExplode(',', $value->getLValue());
                $len = count($splittedValues);
                for ($i=0; $i < $len; $i++) { 
                    $splittedValues[$i] = "'" . trim($splittedValues[$i], "'") . "'";
                }
                $value->setLValue(implode(', ', $splittedValues));
                $this->currentNode->setRValue($value);
                $this->_addQueryNode($index);
            }
        } else {
            $this->currentNode->setRValue($value);
            $this->_addQueryNode($index);
        }
        $value = '';
        $this->isColumn = true;
    }

    /**
     * Adds a logical operator to the current query node.
     *
     * @param string $operator The logical operator to add.
     * @param string $expression The full expression being parsed.
     * @param int $index The current position in the expression.
     * @param int $len The length of the expression.
     * @param string $column The current column name.
     * @param mixed $value The current value being processed.
     *
     * @throws SimpleDatabaseQueryException If there is a syntax error in the expression.
     */
    private function _addLogicalOperator(string $operator, string $expression, int &$index, int $len, string &$column, mixed &$value): void
    {
        if (!$this->isString) {
            if ($index == $len)
                throw new SimpleDatabaseQueryException("Syntax error in expression. Wrong terminating character $operator.");
            $index++;
            if ($value !== '') $this->_addValueToClause($expression, $index, false, $value);
            switch ($operator) {
                case '&':
                    if ($expression[$index] !== '&')
                        throw new SimpleDatabaseQueryException("Syntax error in expression. Wrong logical character at position $index.");
                    $this->_newQueryNode('&&', SimpleDatabaseQueryNode::LOGICAL);
                    $this->_addQueryNode($index);
                    break;
                case '|':
                    if ($expression[$index] !== '|')
                        throw new SimpleDatabaseQueryException("Syntax error in expression. Wrong logical character at position $index.");
                    $this->_newQueryNode('||', SimpleDatabaseQueryNode::LOGICAL);
                    $this->_addQueryNode($index);
                    break;
                default:
                    throw new SimpleDatabaseQueryException("Unknown error. Wrong operator $operator is forwarded to function _addLogicalOperator");
                    break;
            }
        } else {
            if ($this->isColumn) $column .= $operator;
            else $value .= $operator;
        }
    }

    /**
     * Finalizes the lexical and parsing process for the expression.
     *
     * @param string $expression The full expression being parsed.
     * @param int $index The current position in the expression.
     * @param mixed $value The current value being processed.
     *
     * @throws SimpleDatabaseQueryException If there is a syntax error in the expression.
     */
    private function _finalizeLexAndParse(string $expression, int $index, mixed $value): void
    {
        $finalChar = $expression[$index];
        if ($this->isString) {
            if ($finalChar !== '"' && $finalChar !== "'")
                throw new SimpleDatabaseQueryException("Syntax error in expression: No closing string character found.");
            if ($expression[$index-1] === '\\' && Utility::isEscaped($expression, $index-1))
                throw new SimpleDatabaseQueryException("Syntax error in expression: Last closing string character is escaped.");

            if ($this->currentVarIsString) $this->isString = '';
            $this->_addValueToClause($expression, $index, true, $value);
        } else {
            if ($this->groupCount == 1 && $finalChar === ')') {
                $this->_addGroup($expression, $index, $value, $column, false);
            } else {
                $value .= $finalChar;
                $this->_addValueToClause($expression, $index, true, $value);
            }
        }
        if ($this->groupCount != 0)
        {
            if ($this->groupCount > 0) {
                throw new SimpleDatabaseQueryException("Number of group characters do not match. Missing $this->groupCount closing ) characters.");
            } else {
                throw new SimpleDatabaseQueryException("Number of group characters do not match. Missing $this->groupCount opening ( characters.");
            }
        }
    }

    /**
     * Adds a group to the current query node.
     *
     * @param string $expression The full expression being parsed.
     * @param int $index The current position in the expression.
     * @param mixed $value The current value being processed.
     * @param string $column The current column name.
     * @param bool|null $open Whether this is an opening or closing group. Defaults to true.
     *
     * @throws SimpleDatabaseQueryException If there is a syntax error in the expression.
     */
    private function _addGroup(string $expression, int $index, mixed &$value, string &$column, ?bool $open = true): void
    {
        $char = $open ? '(' : ')';
        if (!$this->isString) {
            if (!$this->isColumn && $value !== '') $this->_addValueToClause($expression, $index, false, $value);
            $this->_newQueryNode($char, SimpleDatabaseQueryNode::GROUP);
            $this->_addQueryNode($index);
            $this->groupCount += $open ? 1 : -1;
        } else {
            if ($this->isColumn) $column .= $char;
            else $value .= $char;
        }
    }

    /**
     * Adds a comma-separated group to the current query node.
     *
     * @param string $expression The full expression being parsed.
     * @param int $index The current position in the expression.
     * @param int $lenMinusOne The length of the expression minus one.
     * @param mixed $value The current value being processed.
     * @param string $column The current column name.
     * @param bool|null $open Whether this is an opening or closing group. Defaults to true.
     *
     * @throws SimpleDatabaseQueryException If there is a syntax error in the expression.
     */
    private function _addCommaGroup(string $expression, int &$index, int $lenMinusOne, mixed &$value, string &$column, ?bool $open = true): void
    {
        $group = '';
        if (!$this->isString) {
            $isString = '';
            $breakOut = false;
            for (; $index < $lenMinusOne && !$breakOut; $index++) { 
                $char = $expression[$index];
                switch ($char) {
                    case '\'':
                        if (($expression[$index-1] !== '\\') || ($expression[$index-1] === '\\' && !Utility::isEscaped($expression, ($index-1)))) {
                            if ($isString === '') {
                                $isString = '\'';
                            } elseif ($isString === '\'') {
                                $isString = '';
                            }
                        }
                        break;
                    case '"':
                        if (($expression[$index-1] !== '\\') || ($expression[$index-1] === '\\' && !Utility::isEscaped($expression, ($index-1)))) {
                            if ($isString === '') {
                                $isString = '"';
                            } elseif ($isString === '"') {
                                $isString = '';
                            }
                        }
                        break;
                    case ')':
                        if ($isString === '') {
                            $breakOut = true;
                            $index--;
                        }
                        $group .= ')';
                        break;
                    default:
                        $group .= $char;
                        break;
                }
            }
            $value = new SimpleDatabaseQueryNode($group, SimpleDatabaseQueryNode::COMMA_GROUP);
            $this->_addValueToClause($expression, $index, false, $value);
        } else {
            if ($this->isColumn) $column .= $open ? '(' : ')';
            else $value .= $open ? '(' : ')';
        }
    }

    /**
     * Lexically analyzes and parses the WHERE clause expression.
     *
     * @param string $clause The WHERE clause expression to parse.
     */
    private function _lexAndParseWhereClauses(string $clause): void
    {
        $expression = str_replace(' ', '', str_replace('IN', '->', $clause));
        if ($expression === '') {
            $this->clauses = null;
            return;
        }
        $len = strlen($expression);
        $lenMinusOne = $len-1;
        $column = '';
        $value = '';
        $this->clauses = new SimpleDatabaseQueryNodes();
        $this->currentNode = null;
        $this->groupCount = 0;
        $this->isColumn = true;
        $this->isString = '';
        $this->currentVarIsString = false;
        $this->isLike = false;
        $this->isIn = false;
        for ($i=0; $i < $lenMinusOne; $i++) {
            $char = $expression[$i];
            switch ($char) {
                case '\'':
                    if ($i > 0 && (($expression[$i-1] !== '\\') || ($expression[$i-1] === '\\' && !Utility::isEscaped($expression, ($i-1))))) {
                        if ($this->isString === '' && !$this->isColumn) {
                            $this->isString = "'";
                            $this->currentVarIsString = true;
                        } else {
                            $this->isString = '';
                        }
                    }
                    break;
                case '"':
                    if ($i > 0 && (($expression[$i-1] !== '\\') || ($expression[$i-1] === '\\' && !Utility::isEscaped($expression, ($i-1))))) {
                        if ($this->isString === '' && !$this->isColumn) {
                            $this->isString = "'";
                            $this->currentVarIsString = true;
                        } else {
                            $this->isString = '';
                        }
                    }
                    break;
                case '(':
                    if (!$this->isIn) {
                        $this->_addGroup($expression, $i, $value, $column);
                    } else {
                        $this->_addCommaGroup($expression, $i, $lenMinusOne, $value, $column);
                    }
                    break;
                case ')':
                    if (!$this->isIn) {
                        $this->_addGroup($expression, $i, $value, $column, false);
                    } else {
                        $this->isIn = false;
                    }
                    break;
                case '<':
                    $this->_addComparisonOperator('<', $expression, $i, $len, $column, $value);
                    break;
                case '>':
                    $this->_addComparisonOperator('>', $expression, $i, $len, $column, $value);
                    break;
                case '=':
                    $this->_addComparisonOperator('=', $expression, $i, $len, $column, $value);
                    break;
                case '!':
                    $this->_addComparisonOperator('!', $expression, $i, $len, $column, $value);
                    break;
                case '%':
                    $this->_addComparisonOperator('%', $expression, $i, $len, $column, $value);
                    break;
                case '-':
                    $this->_addComparisonOperator('-', $expression, $i, $len, $column, $value);
                    break;
                case '&':
                    $this->_addLogicalOperator('&', $expression, $i, $len, $column, $value);
                    break;
                case '|':
                    $this->_addLogicalOperator('|', $expression, $i, $len, $column, $value);
                    break;
                default:
                    if ($this->isColumn) $column .= $char;
                    else $value .= $char;
                    break;
            }
        }
        $this->_finalizeLexAndParse($expression, $lenMinusOne, $value);
    }

    /**
     * Injects variables into a query node.
     *
     * @param SimpleDatabaseQueryNode $node The query node to inject variables into.
     */
    private function _injectVariable(SimpleDatabaseQueryNode &$node)
    {
        if ($node->getType() === SimpleDatabaseQueryNode::CLAUSE) {
            $rValue = $node->getRValue();
            if (is_string($rValue) && $rValue !== '') {
                if ($rValue[0] === '$') {
                    $rValue = substr($rValue, 1);
                    if (array_key_exists($rValue, $this->variables)) {
                        if (is_string(($this->variables[$rValue]))) {
                            $rValue = "'" . htmlspecialchars($this->variables[$rValue]) . "'";
                        } else {
                            $rValue = htmlspecialchars(Utility::toString($this->variables[$rValue]));
                        }
                        $node->setRValue($this->_convertValue($rValue));
                    }
                }
            } elseif ($rValue === '' || $rValue === null) {
                $column = $node->getLValue();
                if ($column)
                    throw new SimpleDatabaseQueryException("No value set for column name $column.");
                else
                    throw new SimpleDatabaseQueryException("A clause has neither a column name nor a value.");
            }
        }
    }

    /**
     * Builds the WHERE clause from the parsed query nodes.
     *
     * @return string The constructed WHERE clause.
     */
    private function _buildWhere(): string
    {
        if (!$this->clauses) return '';
        $nodes = $this->clauses->getNodes();
        $dql = '';
        foreach ($nodes as $node) {
            $type = $node->getType();
            switch ($type) {
                case SimpleDatabaseQueryNode::CLAUSE:
                    $dql .= $node->getLValue() . ' ';
                    switch ($node->getComparison()) {
                        case '==':
                            $dql .= '= ';
                            break;
                        case '%%':
                            $dql .= 'LIKE ';
                            break;
                        case '->':
                            $dql .= 'IN ';
                            break;
                        default:
                            $dql .= $node->getComparison() . ' ';
                            break;
                    }
                    $this->_injectVariable($node);
                    
                    if (is_string($node->getRValue())) {
                        $dql .= $node->getRValue() . " ";
                    } elseif ($node->getRValue() instanceof SimpleDatabaseQueryNode) {
                        $dql .= '(' . $node->getRValue()->getLValue() . ') ';
                    } else {
                        $dql .= strval($node->getRValue()) . ' ';
                    }
                    
                    break;
                case SimpleDatabaseQueryNode::LOGICAL:
                    $logicalOperator = $node->getLValue();
                    switch ($logicalOperator) {
                        case '&&':
                            $dql .= 'AND ';
                            break;
                        case '||':
                            $dql .= 'OR ';
                            break;
                        default:
                            throw new SimpleDatabaseQueryException("Unsupported logical operator $logicalOperator in expression");
                            break;
                    }
                    break;
                case SimpleDatabaseQueryNode::GROUP:
                    $dql .= $node->getLValue();
                    break;
                default:
                    throw new SimpleDatabaseQueryException("Unknown error. Please verify that the following value ranges between 1 and 3: $type.");
                    break;
            }
        }
        return $dql;
    }

    /**
     * Creates an ORDER BY clause based on the provided order string.
     */
    private function _createOrderBy(string $order): void
    {
        $matches = [];
        preg_match_all('/([^ \n\r,]+ +(ASC|asc|DESC|desc))/', $order, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $match = explode(' ', $match[0]);
            if (is_array($match)) {
                $columnName = trim($match[0]);
                $order = trim($match[1]);
                $this->queryBuilder->addOrderBy($columnName, $order);
            }
        }
    }

    /**
     * Verifies if a table with the given identifier exists.
     * 
     * @param string $table The name of the table in the database.
     * 
     * @return bool Returns true if the table with the given identifier exists and false otherwise.
     */
    public function tableExists(string $table): bool
    {
        return $this->connection->getConnectionForTable($table)->createSchemaManager()->tableExists($table);
    }

    /**
     * Fetches data from a table in the database.
     *
     * @param string $table The identifier of the table to fetch the data from.
     * @param string|array $select You can reduce the output to specific columns or read all columns by simply forwarding * (Default: *).
     * @param string|null $whereClauseExpression The where clauses rely on the if-syntax of C-type languages with the exception of the added 
     * comparison operator %% . The operator %% stands for the LIKE keyword and wraps the value with percentage characters.
     * Typical where expressions could look like:
     * 
     * 1): category == 5 && (price < 20.0 || discount >= 20)
     * 
     * 2): city == 'New York' && street %% 'Avenue'
     * 
     * (Default: 'uid == 0')
     * 
     * @param string|null $order Comma-separated ordering definitions in the format COLUMN_NAME ASC|DESC.
     * 
     * E.g.: surname DESC, firstname ASC 
     * 
     * (Default: '')
     * 
     * @param array|null $variables You can pass an array of variables to the where expression in the format ['varName1' => value1, 'varName2' => value2].
     * 
     * These will be injected to merge fields in the expression. A merge field can be declared like a PHP variable with a preceding $ sign.
     * 
     * An example usage could be: $variables = ['name' => 'Jack'], $whereClauseExpression = 'age > 18 && firstname == $name'
     * 
     * (Default: [])
     * 
     * @param bool|null $useNativeDqlSyntax The if-syntax is capable of taking basic instructions which usually cover most cases. If you need
     * more complex expressions with more operators though, you can pass Doctrine's DQL language instead if this value is set to true.
     * (Default: false)
     * 
     * @param bool|null $throwIfTableDoesNotExist If the existence of a table is crucial for the further process, you can set this parameter to
     * true to throw an exception if the table does not exist. (Default: true)
     *  
     * @return array An array of arrays of the fetched entries from the database.
     */
    public function fetch (
        string $table,
        string|array $select = '*',
        ?string $whereClauseExpression = 'uid == 0',
        ?string $order = '',
        ?array $variables = [],
        ?bool $useNativeDqlSyntax = false,
        ?bool $throwIfTableDoesNotExist = true
    ): array
    {
        if ($this->tableExists($table) || $throwIfTableDoesNotExist === false) {
            $this->variables = $variables;
            $select = is_array($select) ? $select : [$select];
            $this->queryBuilder = $this->_establishConnection($table);
            $this->_lexAndParseWhereClauses($whereClauseExpression);
            $this->queryBuilder
                ->select(...$select)
                ->from($table)
                ->where($useNativeDqlSyntax ? $whereClauseExpression : $this->_buildWhere());
            $this->_createOrderBy($order);
            return $this->queryBuilder
                ->executeQuery()
                ->fetchAllAssociative();
        } else {
            if ($throwIfTableDoesNotExist)
                throw new SimpleDatabaseQueryException("Table with identifier $table does not exist.");
            else return [];
        }
    }

    /**
     * Inserts data into a table in the database.
     *
     * @param string $tableName The name of the table to insert into.
     * @param array $data The data to insert.
     * @param array $types Optional types for the data.
     *
     * @return int The number of affected rows.
     */
    public function insert(string $tableName, array $data, array $types = []): int
    {
        return $this->connection->getConnectionForTable($tableName)->insert($tableName, $data, $types);
    }

    /**
     * Updates data in a table in the database.
     *
     * @param string $tableName The name of the table to update.
     * @param array $data The data to update.
     * @param array $identifier The identifier for the update.
     * @param array $types Optional types for the data.
     *
     * @return int The number of affected rows.
     */
    public function update(string $tableName, array $data, array $identifier = [], array $types = []): int
    {
        return $this->connection->getConnectionForTable($tableName)->update($tableName, $data, $identifier, $types);
    }

    /**
     * Retrieves the properties of a field in a specific table.
     *
     * @param string $identifier The name of the field.
     * @param string|null $table The name of the table. Defaults to 'tt_content'.
     *
     * @return array An array containing the field properties.
     */
    public static function getFieldProperties(string $identifier, ?string $table = 'tt_content'): array
    {
        $table = $table ?? 'tt_content';
        $sdq = new SimpleDatabaseQuery();
        if ($sdq->tableExists($table)) {
            $select = [
                'TABLE_SCHEMA', 'TABLE_NAME', 'COLUMN_NAME', 'ORDINAL_POSITION', 'COLUMN_DEFAULT', 'IS_NULLABLE', 'DATA_TYPE',
                'CHARACTER_MAXIMUM_LENGTH', 'CHARACTER_OCTET_LENGTH', 'NUMERIC_PRECISION', 'NUMERIC_SCALE', 'DATETIME_PRECISION',
                'CHARACTER_SET_NAME', 'COLLATION_NAME', 'COLUMN_TYPE', 'COLUMN_KEY', 'EXTRA', 'PRIVILEGES', 'COLUMN_COMMENT',
                'GENERATION_EXPRESSION'
            ];
            $where = "TABLE_NAME == '$table' && COLUMN_NAME == '$identifier'";
            return $sdq->fetch("INFORMATION_SCHEMA.COLUMNS", $select, $where, '', [], false, false);
        }
        return [];
    }

    /**
     * Retrieves the properties of a field across multiple tables.
     *
     * @param string $identifier The name of the field.
     * @param array|null $tables An array of table names. Defaults to ['*'].
     *
     * @return array An array containing the field properties across the specified tables.
     */
    public static function getFieldsProperties(string $identifier, ?array $tables = ['*']): array
    {
        $sdq = new SimpleDatabaseQuery();
        $select = [
            'TABLE_SCHEMA', 'TABLE_NAME', 'COLUMN_NAME', 'ORDINAL_POSITION', 'COLUMN_DEFAULT', 'IS_NULLABLE', 'DATA_TYPE',
            'CHARACTER_MAXIMUM_LENGTH', 'CHARACTER_OCTET_LENGTH', 'NUMERIC_PRECISION', 'NUMERIC_SCALE', 'DATETIME_PRECISION',
            'CHARACTER_SET_NAME', 'COLLATION_NAME', 'COLUMN_TYPE', 'COLUMN_KEY', 'EXTRA', 'PRIVILEGES', 'COLUMN_COMMENT',
            'GENERATION_EXPRESSION'
        ];
        $tableNames = '';
        if (!in_array('*', $tables)) {
            $tableNames .= 'TABLE_NAME IN (';
            $legit = false;
            foreach ($tables as $table) {
                if (is_string($table) && $sdq->tableExists($table)) {
                    $tableNames .= "'" . trim($table, "'") . "', ";
                    $legit = true;
                }
            }
            if (!$legit) return [];
            $tableNames = trim($tableNames, ', ') . ') && ';
        }
        $where = $tableNames . "COLUMN_NAME == '$identifier'";
        return $sdq->fetch("INFORMATION_SCHEMA.COLUMNS", $select, $where, '', [], false, false);
    }

    /**
     * Retrieves all fields of a table.
     *
     * @param string $table The name of the table.
     *
     * @return array An array containing the names of all fields in the table.
     */
    public static function getAllFieldsOfTable(string $table): array
    {
        $sdq = new SimpleDatabaseQuery();
        $select = [ 'COLUMN_NAME' ];
        $where = "TABLE_NAME == '$table'";
        return $sdq->fetch("INFORMATION_SCHEMA.COLUMNS", $select, $where, '', [], false, false);
    }

    /**
     * Retrieves the next unique key for a table.
     *
     * @param string $table The name of the table.
     *
     * @return int The next unique key.
     */
    public static function getNextUniqueKey(string $table): int
    {
        $sdq = new SimpleDatabaseQuery();
        $select = [ 'AUTO_INCREMENT' ];
        $where = "TABLE_NAME == '$table'";
        $res = $sdq->fetch("INFORMATION_SCHEMA.TABLES", $select, $where, '', [], false, false);
        if (isset($res[0]) && isset($res[0]['AUTO_INCREMENT'])) {
            return $res[0]['AUTO_INCREMENT'];
        }
        return 0;
    }

    /**
     * Checks if a field exists in any of the specified tables.
     *
     * @param string $field The name of the field.
     * @param array|null $tables An array of table names. Defaults to ['*'].
     *
     * @return bool True if the field exists, false otherwise.
     */
    public static function fieldExists(string $field, ?array $tables = ['*']): bool
    {
        return !empty(SimpleDatabaseQuery::getFieldsProperties($field, $tables));
    }

    /**
     * Adds a new field to a table.
     *
     * @param string $tableName The name of the table.
     * @param string $fieldName The name of the field to add.
     * @param string $type The data type of the field.
     * @param int|null $length The length of the field if applicable.
     * @param string|int|null $default The default value of the field.
     * @param bool $null Whether the field can be null.
     * @param string|null $attributes Additional attributes for the field.
     * @param string|null $index The index type for the field.
     *
     * @throws \InvalidArgumentException If the table does not exist or if the field type is invalid.
     */
    public static function addField (
        string $tableName,
        string $fieldName,
        string $type,
        ?int $length = NULL,
        string|int|NULL $default = NULL,
        bool $null = false,
        ?string $attributes = NULL,
        ?string $index = NULL,
    ): void {
        $sdq = new SimpleDatabaseQuery();
        if (!$sdq->tableExists($tableName)) {
            throw new \InvalidArgumentException("Table $tableName does not exist.");
        }
        
        $type = strtoupper($type);

        if (!array_key_exists($type, self::FIELD_TYPES)) {
            throw new \InvalidArgumentException("Invalid type: $type");
        }
        
        if (self::FIELD_TYPES[$type] !== NULL && is_array(self::FIELD_TYPES[$type]) && $length !== NULL) {
            if ($length < self::FIELD_TYPES[$type][0] || $length > self::FIELD_TYPES[$type][1]) {
                throw new \InvalidArgumentException("Length for type $type must be between " . self::FIELD_TYPES[$type][0] . " and " . self::FIELD_TYPES[$type][1]);
            }
        }

        if (is_string($default)) {
            if (!is_array(self::FIELD_TYPES[$type])) {
                throw new \InvalidArgumentException("Default is set as a string, but field type is not a string.");
            } else {
                if (self::FIELD_TYPES[$type] === 'ENUM' || self::FIELD_TYPES[$type] === 'SET') {
                    throw new \InvalidArgumentException("Default is set as a string, but field type is not a string.");
                }
            }
        } elseif (is_int($default)) {
            if (!is_int(self::FIELD_TYPES[$type])) {
                throw new \InvalidArgumentException("Default is set as numeric, but field type is not numeric.");
            }
        }
        
        $sql = "ALTER TABLE $tableName ADD COLUMN $fieldName ";
        $sql .= $type;
        $sql .= $length ? "($length)" : '';
        
        if ($attributes !== NULL) {
            $sql .= " $attributes";
        }
        
        if (!$null) {
            $sql .= " NOT NULL";
        } else {
            $sql .= " NULL";
        }
        
        if ($default !== NULL) {
            if (is_string($default)) {
                $sql .= " DEFAULT '$default'";
            } else {
                $sql .= " DEFAULT $default";
            }
        } elseif ($default === 'CURRENT_TIMESTAMP') {
            $sql .= " DEFAULT CURRENT_TIMESTAMP";
        } elseif ($null === true) {
            $sql .= " DEFAULT NULL";
        }
        
        if ($index !== null) {
            $index = strtoupper($index);
            if (!isset(self::FIELD_INDEXES[$index])) {
                throw new \InvalidArgumentException("Invalid index: $index");
            }
            $sql .= ", ADD " . self::FIELD_INDEXES[$index] . "(`$fieldName`)";
        }

        $sql .= ';';
        
        $sdq->connection->getConnectionForTable($tableName)->connect()->query($sql);
    }
}

?>