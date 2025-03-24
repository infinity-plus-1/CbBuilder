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

use DS\CbBuilder\Event\RecordDeletedEvent;
use DS\CbBuilder\Utility\SimpleDatabaseQuery;
use TYPO3\CMS\Core\Attribute\AsEventListener;

/**
 * Event listener for handling record deletions.
 */
final readonly class RecordDeletedListener
{
    /**
     * Listens for record deletion events.
     *
     * @param RecordDeletedEvent $event Record deletion event.
     */
    #[AsEventListener]
    public function __invoke(RecordDeletedEvent $event): void
    {
        $recordToDelete = $event->getRecordToDelete();
        $table = $event->getTable();
        
        // Check if the deleted record is from the 'tt_content' table.
        if ($table === 'tt_content') {
            if (isset($recordToDelete['cb_index'])) {
                $cbUid = $recordToDelete['cb_index'];
                $sdq = new SimpleDatabaseQuery();
                
                // Fetch the corresponding record from the 'cb_table'.
                $res = $sdq->fetch('cb_table', 'uid', 'uid==' . $cbUid);
                if ($res !== []) {
                    $identifier = [];
                    $identifier['uid'] = $cbUid;
                    $deleted = [];
                    $deleted['deleted'] = 1;
                    
                    // Update the 'cb_table' record to mark it as deleted.
                    $sdq->update('cb_table', $deleted, $identifier);
                }
            }
        }
    }
}