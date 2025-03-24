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

namespace DS\CbBuilder\Hook;

use DS\CbBuilder\Event\RecordBeforeSendToDatabaseEvent;
use DS\CbBuilder\Event\RecordBeforeSendToDatabaseSanitizedEvent;
use DS\CbBuilder\Event\RecordDeletedEvent;
use DS\CbBuilder\Event\RecordSavedToDatabaseEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Hook class for attribute palette processing.
 */
class AttributePaletteHook
{
    /**
     * Dispatches the RecordBeforeSendToDatabaseSanitizedEvent after processing the field array.
     *
     * @param string $status The status of the operation.
     * @param mixed $table The table name.
     * @param mixed $id The record ID.
     * @param array $fieldArray The processed field array.
     * @param DataHandler $obj The DataHandler instance.
     */
    public function processDatamap_postProcessFieldArray(string $status, mixed $table, mixed $id, array &$fieldArray, DataHandler $obj): void
    {
        GeneralUtility::makeInstance(EventDispatcherInterface::class)->dispatch(
            new RecordBeforeSendToDatabaseSanitizedEvent($status, $table, $id, $fieldArray, $obj)
        );
    }

    /**
     * Dispatches the RecordBeforeSendToDatabaseEvent before processing the field array.
     *
     * @param array $fieldArray The field array to be processed.
     * @param mixed $table The table name.
     * @param mixed $id The record ID.
     * @param DataHandler $obj The DataHandler instance.
     */
    public function processDatamap_preProcessFieldArray(array &$fieldArray, mixed $table, mixed $id, DataHandler $obj): void
    {
        GeneralUtility::makeInstance(EventDispatcherInterface::class)->dispatch(
            new RecordBeforeSendToDatabaseEvent($fieldArray, $table, $id, $obj)
        );
    }

    /**
     * Dispatches the RecordSavedToDatabaseEvent after database operations.
     *
     * @param string $status The status of the operation.
     * @param string $table The table name.
     * @param mixed $id The record ID.
     * @param array $fieldArray The processed field array.
     * @param DataHandler $obj The DataHandler instance.
     */
    public function processDatamap_afterDatabaseOperations(string $status, string $table, mixed $id, array $fieldArray, DataHandler $obj): void
    {
        GeneralUtility::makeInstance(EventDispatcherInterface::class)->dispatch(
            new RecordSavedToDatabaseEvent($status, $table, $id, $fieldArray, $obj)
        );
    }

    /**
     * Dispatches the RecordDeletedEvent during the delete action.
     *
     * @param string $table The table name.
     * @param int $id The record ID.
     * @param array $recordToDelete The record being deleted.
     * @param bool $recordWasDeleted Whether the record was successfully deleted.
     * @param DataHandler $obj The DataHandler instance.
     */
    public function processCmdmap_deleteAction(string $table, int $id, array $recordToDelete, bool $recordWasDeleted, DataHandler $obj): void
    {
        GeneralUtility::makeInstance(EventDispatcherInterface::class)->dispatch(
            new RecordDeletedEvent($table, $id, $recordToDelete, $recordWasDeleted, $obj)
        );
    }
}