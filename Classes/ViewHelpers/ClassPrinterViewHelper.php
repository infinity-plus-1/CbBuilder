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

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper to print CSS classes based on a field identifier.
 */
final class ClassPrinterViewHelper extends AbstractViewHelper
{
    /**
     * Initializes the arguments for this view helper.
     */
    public function initializeArguments(): void
    {
        $this->registerArgument(
            'value',
            'string',
            'The field identifier (prepend the table if multiple fields with that identifier exists. Like: table:identifier'
        );
    }

    /**
     * Searches for classes by identifier in the given array of classes.
     *
     * @param string $identifier The identifier to search for.
     * @param array $classes The array of classes to search in.
     *
     * @return string|bool The list of classes if found, false otherwise.
     */
    private function _searchClassesByIdentifier(string $identifier, array $classes): string|bool
    {
        $table = str_contains($identifier, ':') ? explode(':', $identifier)[0] : '';
        $classList = '';
        foreach ($classes as $key => $value) {
            $splittedKey = array_reverse(explode(':', $key));
            if ($identifier === $splittedKey[0] && ($table === '' || $table === $splittedKey[1])) {
                $classList = implode(' ', $value);
                return $classList;
            }
        }
        return false;
    }

    /**
     * Renders the view helper.
     *
     * @return string The list of classes as a string.
     */
    public function render(): string
    {
        $globalVars = $this->renderingContext->getVariableProvider();
        $variables = $globalVars->get('cbData');
        if (is_array($variables) && isset($variables['classes']) && is_array($variables['classes']) && $variables['classes'] !== []) {
            $classes = $variables['classes'];
            $identifier = $this->arguments['value'] ?? NULL;

            if ($identifier) {
                $classList = $this->_searchClassesByIdentifier($identifier, $classes);
                if ($classList !== false) {
                    return $classList;
                }
            }
        }
        return '';
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