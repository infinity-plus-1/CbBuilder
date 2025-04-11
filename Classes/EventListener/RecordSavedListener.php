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

namespace DS\CbBuilder\EventListener;

use DS\CbBuilder\Event\RecordSavedToDatabaseEvent;
use DS\CbBuilder\Utility\SimpleDatabaseQuery;
use Exception;
use TYPO3\CMS\Core\Attribute\AsEventListener;

/**
 * Event listener for handling records saved to the database.
 */
final readonly class RecordSavedListener
{
    /**
     * Listens for record save events.
     *
     * @param RecordSavedToDatabaseEvent $event Record save event.
     */
    #[AsEventListener]
    public function __invoke(RecordSavedToDatabaseEvent $event): void
    {
        $status = $event->getStatus();
        $id = $event->getId();
        $table = $event->getTable();
        $dataHandler = $event->getDataHandler();
        $actualUid = 0;

        // Determine the actual UID if the record is new.
        if ($status === 'new') {
            if (isset($dataHandler->substNEWwithIDs[$id])) {
                $actualUid = $dataHandler->substNEWwithIDs[$id];
            } else {
                if (isset($dataHandler->errorLog) && is_array($dataHandler->errorLog) && $dataHandler->errorLog !== []) {
                    $errors = '';
                    foreach ($dataHandler->errorLog as $error) {
                        $errors .= $error . "\n";
                    }
                    throw new Exception($errors);
                } else {
                    throw new Exception (
                        "An unknown error occured while trying to save to the database.\n" .
                        "Please try to add the content manually to the database to check for exact errors."
                    );
                }
            }
            
        }

        // Check if the saved record is from the 'tt_content' table.
        if ($table === 'tt_content') {
            // Check if the data map contains 'tt_content' entries.
            if (isset($dataHandler->datamap) && is_array($dataHandler->datamap) && isset($dataHandler->datamap['tt_content'])) {
                if (isset($dataHandler->datamap['tt_content'][$id])) {
                    $data = $dataHandler->datamap['tt_content'][$id];
                    
                    // Check if the record contains a 'cb_index'.
                    if (isset($data['cb_index'])) {
                        $items = [];
                        $items['tt_content_uid'] = $actualUid;
                        if (isset($data['cb_preset'])) {
                            $items['preset'] = $data['cb_preset'];
                        }
                        if (isset($data['cb_classes'])) {
                            $items['classes'] = $data['cb_classes'];
                        }
                        if (isset($data['CType'])) {
                            $items['CType'] = $data['CType'];
                        }
                        
                        // Initialize a simple database query instance.
                        $sdq = new SimpleDatabaseQuery();
                        
                        // Handle new records by inserting into 'cb_table'.
                        if ($status === 'new') {

                            if ($actualUid === null) {
                                // Whoops, it seems the record could not be saved.
                                throw new Exception(
                                    "The record could not be saved. Please check the database to ensure all necessary fields are set and configured correctly."
                                );
                            }

                            $sdq->insert('cb_table', $items);
                            $res = $sdq->fetch('cb_table', 'uid', 'tt_content_uid==' . $actualUid);
                            if (isset($res['uid'])) {
                                $identifier = [];
                                $identifier['uid'] = $actualUid;
                                $items = [];
                                $items['cb_index'] = $res['uid'];
                                $sdq->update($table, $items, $identifier);
                            }
                        }
                        // Handle updated records by updating 'cb_table'.
                        elseif ($status === 'update') {
                            $identifier = [];
                            $identifier['uid'] = $data['cb_index'];
                            unset($items['tt_content_uid']);
                            $sdq->update('cb_table', $items, $identifier);
                        }
                    }
                }
            }
        }
    }
}