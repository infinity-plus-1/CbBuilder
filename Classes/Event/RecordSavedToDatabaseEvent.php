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

namespace DS\CbBuilder\Event;

use TYPO3\CMS\Core\DataHandling\DataHandler;

/**
 * Event class for handling records after they have been saved to the database.
 */
final readonly class RecordSavedToDatabaseEvent
{
    /**
     * Constructor for the event.
     *
     * @param string $status Status of the record.
     * @param string $table Table name.
     * @param int|string $id Record ID.
     * @param array $fieldArray Array of fields.
     * @param DataHandler $dataHandler Data handler instance.
     */
    public function __construct(
        private string $status,
        private string $table,
        private int|string $id,
        private array $fieldArray,
        private DataHandler $dataHandler
    ) {}

    /**
     * Returns the status of the record.
     *
     * @return string Record status.
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Returns the table name.
     *
     * @return string Table name.
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Returns the record ID.
     *
     * @return int|string Record ID.
     */
    public function getId(): int|string
    {
        return $this->id;
    }

    /**
     * Returns the field array.
     *
     * @return array Field array.
     */
    public function getFieldArray(): array
    {
        return $this->fieldArray;
    }

    /**
     * Returns the data handler instance.
     *
     * @return DataHandler Data handler instance.
     */
    public function getDataHandler(): DataHandler
    {
        return $this->dataHandler;
    }
}