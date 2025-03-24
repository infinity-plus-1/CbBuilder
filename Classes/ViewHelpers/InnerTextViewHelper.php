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

use Exception;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Custom exception for the InnerTextViewHelper.
 */
class InnerTextViewHelperException extends Exception {}

/**
 * Strips HTML tags around text.
 * 
 * Usage:
 * 
 * Input:
 * Fluid variable: ['myText': '<p><strong>Inner text</strong></p>']
 * <fh:innerText>{myText}</fh:innerText>
 * 
 * Output: Inner text
 * 
 * @param string $value The HTML text to extract the inner text from.
 */
final class InnerTextViewHelper extends AbstractViewHelper
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
        $this->registerArgument('value', 'string', 'The HTML text to extract the inner text from.');
    }

    /**
     * Renders the view helper by stripping HTML tags from the input text.
     *
     * @return string The text with HTML tags stripped.
     */
    public function render(): string
    {
        $html = $this->arguments['value'] ?? ($this->renderChildren() ?? NULL);
        return trim(strip_tags(htmlspecialchars_decode($html)));
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