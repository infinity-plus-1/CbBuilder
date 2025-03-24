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

/**
 * Class representing a linebreak field.
 */
final class LinebreakField extends Field
{
    /**
     * Convert an array to a linebreak field.
     *
     * @param array $field The array representing the field.
     */
    private function _arrayToField(array $field): void
    {
        $this->__arrayToField('linebreak', $field);
        $field['table'] = $this->table;
        $field['identifier'] = $this->identifier;
    }

    /**
     * Constructor for the linebreak field.
     *
     * @param array $field The array representing the field.
     * @param string $table The table name.
     */
    public function __construct(array $field, string $table)
    {
        $this->table = $table;
        $this->_arrayToField($field);
    }
}