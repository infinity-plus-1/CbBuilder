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
 * Event class for handling records after they have been deleted.
 */
final readonly class RecordDeletedEvent
{
    /**
     * Constructor for the event.
     *
     * @param string $table Table name.
     * @param int|string $id Record ID.
     * @param array $recordToDelete Record to be deleted.
     * @param bool $recordWasDeleted Whether the record was successfully deleted.
     * @param DataHandler $dataHandler Data handler instance.
     */
    public function __construct(
        private string $table,
        private int|string $id,
        private array $recordToDelete,
        private bool $recordWasDeleted,
        private DataHandler $dataHandler
    ) {}

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
     * Returns the record to be deleted.
     *
     * @return array Record to be deleted.
     */
    public function getRecordToDelete(): array
    {
        return $this->recordToDelete;
    }

    /**
     * Checks if the record was successfully deleted.
     *
     * @return bool Whether the record was deleted.
     */
    public function isRecordWasDeleted(): bool
    {
        return $this->recordWasDeleted;
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