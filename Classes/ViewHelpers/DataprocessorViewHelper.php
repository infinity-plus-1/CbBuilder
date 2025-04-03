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

use DS\CbBuilder\ChildTableFetcher\ChildTableFetcher;
use DS\CbBuilder\Utility\SimpleDatabaseQueryException;
use DS\CbBuilder\Utility\Utility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Resource\StorageRepository;

/**
 * Custom exception for the DataprocessorViewHelper.
*/
class DataprocessorViewHelperException extends Exception {}

/**
 * Provides data to Fluid templates from either the content object or the database.
 * 
 * Usage:
 * 
 * Include the Fluid view helpers:
 * 
 * <html
 *  xmlns:be="http://typo3.org/ns/TYPO3/CMS/Backend/ViewHelpers"
 *  xmlns:cb="http://typo3.org/ns/DS/CbBuilder/ViewHelpers"
 *  data-namespace-typo3-fluid="true" >
 * 
 * For database queries:
 * 
 * <cb:dataprocessor source="db" nested="true" where="uid > $uid && (pid == 20 || pid == $pageId)" variables="pageId: 5, uid: 60" order="uid DESC, pid DESC" as="processedData" />
 * 
 * or
 * 
 * The content object:
 * <cb:dataprocessor source="page" nested="false" as="processedData" />
 * 
*/

final class DataprocessorViewHelper extends AbstractViewHelper
{
    /**
     * List of foreign tables.
    */
    protected array $foreignTables = [];

    /**
     * Storage repository instance.
    */
    protected StorageRepository $storageRepository;

    /**
     * Initializes the view helper arguments.
    */
    public function initializeArguments(): void
    {
        $this->registerArgument(
            'source',
            'string',
            'Can be either "db" (fetch data from the database) or "page" (get data from the content object).',
            false,
            'page'
        );
        $this->registerArgument(
            'nested',
            'bool',
            'If true, the data processor will fetch data from the database where an item has a foreign_table set in the TCA configuration.',
            false,
            false
        );
        $this->registerArgument(
            'tableName',
            'string',
            'The table identifier to fetch data from.',
            false,
            'tt_content'
        );
        $this->registerArgument(
            'select',
            'string',
            'The select filter to fetch only specific columns. Comma-separated.',
            false,
            '*'
        );
        $this->registerArgument(
            'where',
            'string',
            'A where filter expression in the format: fieldName COMPAREOPERAND value. For example: pid == 5 && CType != "textpic". Valid expression operators are: &&, ||, ==, !=, <=, >=, <, >, %%',
            false,
            'uid == 0'
        );
        $this->registerArgument(
            'variables',
            'mixed',
            'Define comma-separated key-value pairs to replace placeholders in the where clause. For example: myKey: myValue, myKey2: myValue2, ...',
            false,
            ''
        );
        $this->registerArgument(
            'order',
            'string',
            'Define comma-separated order rules. For example: columnName1 ASC, columnName2 DESC',
            false,
            ''
        );
        $this->registerArgument(
            'useNative',
            'bool',
            'If you wish to perform more complex queries, you can pass DQL by setting this to true.',
            false,
            false
        );
        $this->registerArgument(
            'as',
            'string',
            'Defines the key to access the results in Fluid.',
            true
        );
    }

    /**
     * Converts a variable string into an array.
     *
     * @return array The array representation of the variable string.
    */
    private function _varStringToArray(): array
    {
        return Utility::keyValueStringToArray($this->arguments['variables']);
    } 

    /**
     * Retrieves the tt_content data from the current request or rendering context.
     *
     * @return array The tt_content data.
     * @throws DataprocessorViewHelperException If relevant page information cannot be gathered.
     */
    private function _getTtContent(): array
    {
        $request = $this->_getRequest();
        $cObj = $request->getAttribute('currentContentObject');
        if ($cObj) {
            return $cObj->data;
        } else {
            $globalVars = $this->renderingContext->getVariableProvider();
            if ($globalVars->exists('uid')) {
                return $globalVars->getAll();
            } else {
                throw new DataprocessorViewHelperException("Relevant page information cannot be gathered.");
            }
        }
    }

    /**
     * Retrieves the current server request from the rendering context.
     *
     * @return ServerRequestInterface|null The server request instance or null if not available.
     */
    private function _getRequest(): ?ServerRequestInterface
    {
        if ($this->renderingContext->hasAttribute(ServerRequestInterface::class)) {
            return $this->renderingContext->getAttribute(ServerRequestInterface::class);
        }
        return null;
    }
    
    /**
     * Renders the view helper and provides data to the Fluid template.
     */
    public function render(bool $nonViewHelper = false): void
    {
        $tableName = $this->arguments['tableName'] ?? 'tt_content';
        $variables = $this->arguments['variables'] ?? [];
        $source = $this->arguments['source'] ?? 'db';
        $useNative = $this->arguments['useNative'] ?? false;
        $select = $this->arguments['select'] ?? '*';
        $order = $this->arguments['order'] ?? '';

        $data = [];

        if (!is_array($variables)) {
            if (!is_string($variables)) {
                throw new SimpleDatabaseQueryException(
                    "The argument 'variables' must be declared as an array or a string in Fluid. " .
                    "For example: {myKey1: 'myValue1', myKey2: 2, ...} OR myKey1: 'myValue1', myKey2: 2, ..."
                );
            } else {
                $variables = $this->_varStringToArray();
            }
        }
        $globalVars = $this->renderingContext->getVariableProvider();
        $variables = array_merge($globalVars->getAll(), $variables);
        $ctf = new ChildTableFetcher($tableName, $select, $this->arguments['where'], $order, $variables, $useNative);
        if (strtolower($source) === 'page') {
            $data = [];
            $data[0] = $this->_getTtContent();
        } elseif (strtolower($source) === 'db') {
            $data = $ctf->basicFetch();
        }

        if ($this->arguments['nested'] === true && !empty($data)) {
            $data = $ctf->fetchChilds($data);
        }
        
        $globalVars->add($this->arguments['as'], $data);
    }
}