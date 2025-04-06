<?php

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

declare(strict_types=1);

namespace DS\CbBuilder\DataProcessing;

use DS\CbBuilder\ChildTableFetcher\ChildTableFetcher;
use DS\CbBuilder\Config\CbBuilderConfig;
use DS\CbBuilder\FileCreater\FileCreater;
use DS\CbBuilder\Utility\SimpleDatabaseQuery;
use DS\CbBuilder\Utility\Utility;
use DS\CbBuilder\ViewHelpers\DataprocessorViewHelper;
use Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * Data processor for content blocks.
 */
class CbProcessor implements DataProcessorInterface
{
    /**
     * Processes data for content blocks.
     *
     * @param ContentObjectRenderer $cObj Content object renderer.
     * @param array $contentObjectConfiguration Content object configuration.
     * @param array $processorConfiguration Processor configuration.
     * @param array $processedData Processed data.
     * @return array Processed data with content block information.
     */

    private function _populateFiles(array &$data, int $uid, string $tableName, array|string $fileFields): void
    {
        foreach ($data as $key => &$value) {
            if (is_array($value)) {
                foreach ($value as &$entry) {
                    if (is_array($entry) && array_key_exists('uid', $entry) && isset($entry['uid']))
                    $this->_populateFiles($entry, $entry['uid'], $key, $fileFields[$key]);
                }
            } else {
                if ((is_array($fileFields) && in_array($key, $fileFields)) || $key === $fileFields) {
                    $ctf = new ChildTableFetcher('sys_file_reference', '*', "tablenames=='$tableName'&&uid_foreign==$uid");
                    $files = $ctf->fetchChilds($ctf->basicFetch());
                    $data[$key] = [];
                    foreach ($files as $file) {
                        $data[$key][] = $file['file'];
                    }
                }
            }
        }
    }


    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData,
    ): array {
        $processedData['cbData'] = [];
        if (isset($processedData['data']['cb_index']) && is_int($processedData['data']['cb_index'])) {
            $cbIndex = $processedData['data']['cb_index'];
            $uid = $processedData['data']['uid'];
            $sdq = new SimpleDatabaseQuery();
            $res = $sdq->fetch('cb_table', ['preset', 'classes', 'CType'], "uid==$cbIndex&&tt_content_uid==$uid");
            $res = Utility::skipZeroIndexed($res);
            $identifier = $res['CType'] ?? '';
            
            $localConf = NULL;

            if ($identifier !== '') {
                try {
                    $localConf = CbBuilderConfig::loadLocalConfig($identifier);
                } catch (Exception $e) {
                    throw new Exception (
                        "Config could not get loaded. Original message:\n" . $e->getMessage()
                    );
                }
            } else {
                throw new Exception (
                    "CType could not be fetched from the database record. Please check the record " .
                    "'$cbIndex' in the 'cb_table' table."
                );
            }

            if (isset($res['preset'])) {
                if (isset($localConf['config']['usePartials']) && $localConf['config']['usePartials'] === true) {
                    $processedData['cbData']['partial'] = $res['preset'];
                } else {
                    $processedData['cbData']['partial'] = NULL;
                }
            }
            if (isset($res['classes'])) {
                if (isset($localConf['config']['useClasses']) && $localConf['config']['useClasses'] === true) {
                    $processedData['cbData']['classes'] = [];
                    $tmp = GeneralUtility::trimExplode(',', $res['classes']);
                    foreach ($tmp as $class) {
                        $keyValPair = GeneralUtility::trimExplode(':::', $class);
                        if (is_array($keyValPair) && isset($keyValPair[0]) && $keyValPair[0] !== '' && isset($keyValPair[1]) && $keyValPair[1] !== '') {
                            if (isset($processedData['cbData']['classes'][$keyValPair[0]])) {
                                $processedData['cbData']['classes'][$keyValPair[0]][] = $keyValPair[1];
                            } else {
                                $processedData['cbData']['classes'][$keyValPair[0]] = [$keyValPair[1]];
                            }
                        }
                    }
                } else {
                    $processedData['cbData']['classes'] = NULL;
                }
            }
        }

        $ctf = new ChildTableFetcher('tt_content', '*', "uid==$uid");
        $processedData['data'] = $ctf->fetchChilds($ctf->basicFetch());
        if (isset($processedData['data']) && is_array($processedData['data']) && count($processedData['data']) === 1) {
            if (isset($processedData['data'][0])) {
                $processedData['data'] = array_merge($processedData['data'], $processedData['data'][0]);
                unset($processedData['data'][0]);
            }
        }

        $fileFields = FileCreater::getFieldsMap($identifier);

        if ($fileFields !== []) {
            $this->_populateFiles($processedData['data'], $uid, 'tt_content', $fileFields);
        }

        return $processedData;
    }
}
