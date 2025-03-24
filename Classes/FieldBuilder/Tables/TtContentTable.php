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

namespace DS\CbBuilder\FieldBuilder\Tables;

use DS\CbBuilder\Utility\ArrayParser;
use DS\CbBuilder\Utility\Utility;
use Exception;

/**
 * Represents a tt_content table.
 *
 * @property array $showItem
 * @property array $columnsOverrides
 * @property string $stringContent
 */
final class TtContentTable extends Table
{
    /**
     * Metadata for the table.
     */
    protected array $meta = [];

    /**
     * Load metadata from the table content.
     *
     * @throws Exception If metadata cannot be loaded.
     */
    public function loadMeta(): void
    {
        $this->meta['new'] = $GLOBALS['CbBuilder']['meta'];
        $meta = Utility::stringSafeExplode("ExtensionManagementUtility::addTcaSelectItem", $this->stringContent);
        if (count($meta) !== 2) {
            throw new Exception("Could not find a 'addTcaSelectItem' method call. Please check your 'tt_content.php'.");
        }
        
        $meta = ArrayParser::extractArrayFromString($meta[1]);
        if ($meta === false || count($meta) !== 3) {
            throw new Exception("Could not find an 'item' in the 'addTcaSelectItem' method. Please check your 'tt_content.php'.");
        }
        
        $this->meta['legacy'] = ArrayParser::parsePhpArrayString($meta[0])[0];
    }

    /**
     * Set metadata for the table.
     *
     * @return string The metadata as a string.
     */
    public function setMeta(): string
    {
        $meta = $this->meta['new'];
        $placeAt = $meta['placeAt'];
        $position = $meta['position'];
        $item = $this->meta['legacy'];
        $item['label'] = $meta['name'];
        $item['description'] = $meta['description'];
        $item['value'] = $meta['identifier'];
        $item['icon'] = $meta['identifier'] . '_icon';
        $item['group'] = $meta['group'];
        $item = ArrayParser::arrayToString($item, '', 2);
        return  "\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem (\n" .
                    "\t'tt_content',\n" .
                    "\t'CType',\n" .
                    "\t" . $item . ",\n" .
                    "\t'$placeAt',\n" .
                    "\t'$position'\n" .
                ");\n\n";
    }
}