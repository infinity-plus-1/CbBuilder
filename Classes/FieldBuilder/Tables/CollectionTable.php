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

use DS\CbBuilder\FieldBuilder\Fields\CollectionContainer;
use DS\CbBuilder\FieldBuilder\Fields\Field;
use DS\CbBuilder\Utility\ArrayParser;
use Exception;

/**
 * Represents a table for managing collections.
 */
final class CollectionTable extends Table
{
    /**
     * Container for managing collections.
     */
    protected ?CollectionContainer $collectionContainer = null;


    /**
     * Gets the label of the field.
     * 
     * @return string The label of the field.
     */
    public function getLabel(): string
    {
        return $this->collectionContainer->getLabel();
    }

    /**
     * Gets the classes applied to the field.
     * 
     * @return array The classes applied to the field.
     */
    public function getClasses(): array
    {
        return $this->collectionContainer->getClasses();
    }

    /**
     * Get the collection container.
     *
     * @return CollectionContainer|null The collection container instance.
     */
    public function getCollectionContainer(): ?CollectionContainer
    {
        return $this->collectionContainer;
    }

    /**
     * Set the collection container.
     *
     * @param CollectionContainer $collectionContainer The collection container to set.
     */
    public function setCollectionContainer(CollectionContainer $collectionContainer): void
    {
        $this->collectionContainer = $collectionContainer;
    }

    /**
     * Merge the collection container from another table.
     *
     * @param CollectionTable $foreign The table from which to merge the collection container.
     */
    public function mergeTable(CollectionTable $foreign): void
    {
        if ($foreign->getCollectionContainer() !== null) {
            if ($this->collectionContainer !== null) {
                $this->collectionContainer->mergeField($foreign->getCollectionContainer());
            } else {
                $this->collectionContainer = $foreign->getCollectionContainer();
            }
        }
    }

    /**
     * Load default control settings from a file.
     *
     * @throws Exception If the file cannot be loaded or is malformed.
     */
    public function loadDefaultCtrl(): void
    {
        $path = __DIR__ . '/../../../Templates/defaultCtrl.def';
        $ctrl = ArrayParser::extractArraysFromFile($path);
        if (!is_array($ctrl)) {
            throw new Exception("Could not load settings from '$path'.\nFix: Please restore the original file.");
        }
        if (isset($ctrl[0])) {
            $ctrl = $ctrl[0];
        }
        if (isset($ctrl['ctrl'])) {
            $ctrl = $ctrl['ctrl'];
        }
        if (!isset($ctrl['label'])) {
            throw new Exception("Could not load all settings from '$path'.\nFix: Please restore the original file.");
        }
        $this->ctrl = $ctrl;
    }

    /**
     * Set default control settings based on the table's fields.
     */
    public function setDefaultCtrl(): void
    {
        $altLabels = [];
        foreach ($this->fields as $field) {
            if ($field instanceof Field) {
                if ($field->getType() === 'Text' || $field->getType() === 'Textarea') {
                    if ($this->ctrl['label'] === '') {
                        $this->ctrl['label'] = $field->getIdentifier();
                    } else {
                        $altLabels[] = $field->getIdentifier();
                    }
                }
            }
        }
        $this->ctrl['label_alt'] = implode(', ', $altLabels);
        $this->ctrl['title'] = $this->table;
    }

    /**
     * Parse the control settings into a string.
     *
     * @return string The control settings as a string.
     */
    public function parseCtrl(): string
    {
        return ArrayParser::arrayToString($this->ctrl, 'ctrl', 2, true) . ',';
    }

    /**
     * Create a collection container based on the provided collection element and parent element.
     *
     * @param array $collectionElement The collection element configuration.
     * @param string $parentElement The parent element identifier.
     */
    public function createCollectionContainer(array $collectionElement, string $parentElement): void
    {
        $this->collectionContainer = Field::createField($collectionElement, $parentElement, $this);
    }
}