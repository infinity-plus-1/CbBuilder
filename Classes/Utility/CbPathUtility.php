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

namespace DS\CbBuilder\Utility;

use DirectoryIterator;

/**
 * Utility class for handling paths and directories in the context of CbBuilder.
 */
final class CbPathUtility
{
    public static function getFileData(string $file): array|bool
    {
        $xploded = explode('/', $file);
        if (is_array($xploded) && count($xploded) > 1) {
            $xploded = array_reverse($xploded);
        }
        if (isset($xploded[0])) {
            $fileData = explode('.', $xploded[0]);
            if (count($fileData) === 2) {
                return ['name' => $fileData[0], 'type' => $fileData[1]];
            }
        }
        return false;
    }
    /**
     * Scans a directory for PHP files, optionally filtering by a specific file name.
     *
     * @param DirectoryIterator $directoryIterator The iterator for the directory to scan.
     * @param string $fileName Optional file name to filter results by. If empty, all PHP files are returned.
     *
     * @return array List of paths to PHP files matching the criteria.
     */
    public static function scanForPhpFiles(DirectoryIterator $directoryIterator, string $fileName = ''): array
    {
        $files = [];
        while ($directoryIterator->valid()) {
            if ($directoryIterator->isFile() && $directoryIterator->getExtension() === 'php') {
                if ($fileName === '') {
                    // Add all PHP files if no specific file name is provided.
                    $files[] = $directoryIterator->getPath() . '/' . $directoryIterator->getFilename();
                } else {
                    // If a specific file name is provided, return immediately if found.
                    if ($fileName === $directoryIterator->getFilename()) {
                        return [$directoryIterator->getPath() . '/' . $directoryIterator->getFilename()];
                    }
                }
            }
            $directoryIterator->next();
        }
        return $files;
    }

    /**
     * Scans a directory for CSS files, optionally filtering by a specific file name.
     *
     * @param DirectoryIterator $directoryIterator The iterator for the directory to scan.
     * @param string $fileName Optional file name to filter results by. If empty, all CSS files are returned.
     *
     * @return array List of paths to CSS files matching the criteria.
     */
    public static function scanForCssFiles(DirectoryIterator $directoryIterator, string $fileName = ''): array
    {
        $files = [];
        while ($directoryIterator->valid()) {
            if ($directoryIterator->isFile() && $directoryIterator->getExtension() === 'css') {
                if ($fileName === '') {
                    // Add all CSS files if no specific file name is provided.
                    $files[] = $directoryIterator->getPath() . '/' . $directoryIterator->getFilename();
                } else {
                    // If a specific file name is provided, return immediately if found.
                    if ($fileName === $directoryIterator->getFilename()) {
                        return [$directoryIterator->getPath() . '/' . $directoryIterator->getFilename()];
                    }
                }
            }
            $directoryIterator->next();
        }
        return $files;
    }

    /**
     * Scans a directory for JS files, optionally filtering by a specific file name.
     *
     * @param DirectoryIterator $directoryIterator The iterator for the directory to scan.
     * @param string $fileName Optional file name to filter results by. If empty, all JS files are returned.
     *
     * @return array List of paths to JS files matching the criteria.
     */
    public static function scanForJsFiles(DirectoryIterator $directoryIterator, string $fileName = ''): array
    {
        $files = [];
        while ($directoryIterator->valid()) {
            if ($directoryIterator->isFile() && $directoryIterator->getExtension() === 'js') {
                if ($fileName === '') {
                    // Add all JS files if no specific file name is provided.
                    $files[] = $directoryIterator->getPath() . '/' . $directoryIterator->getFilename();
                } else {
                    // If a specific file name is provided, return immediately if found.
                    if ($fileName === $directoryIterator->getFilename()) {
                        return [$directoryIterator->getPath() . '/' . $directoryIterator->getFilename()];
                    }
                }
            }
            $directoryIterator->next();
        }
        return $files;
    }

    /**
     * Retrieves a DirectoryIterator for the TCA overrides directory of an extension.
     *
     * @param string $extensionPath Path to the extension directory.
     *
     * @return DirectoryIterator|null Iterator for the overrides directory, or null if it does not exist.
     */
    public static function getOverride(string $extensionPath): ?DirectoryIterator
    {
        if (is_dir($extensionPath . '/Configuration/TCA/Overrides')) {
            return new DirectoryIterator($extensionPath . '/Configuration/TCA/Overrides');
        }
        return null;
    }

    /**
     * Retrieves a DirectoryIterator for the TCA configuration directory of an extension.
     *
     * @param string $extensionPath Path to the extension directory.
     *
     * @return DirectoryIterator|null Iterator for the configuration directory, or null if it does not exist.
     */
    public static function getConfiguration(string $extensionPath): ?DirectoryIterator
    {
        if (is_dir($extensionPath . '/Configuration/TCA')) {
            return new DirectoryIterator($extensionPath . '/Configuration/TCA');
        }
        return null;
    }

    /**
     * Scans the extensions folder and returns a list of paths to extension directories.
     *
     * @return array List of paths to extension directories.
     */
    public static function scanExtensionFolder(): array
    {
        $extensions = [];
        $path = $GLOBALS['CbBuilder']['config']['extensionsPath'];
        $iterator = new DirectoryIterator($path);
        while ($iterator->valid()) {
            if ($iterator->isDir() && $iterator->isReadable()) {
                $extensions[] = $path . '/' . $iterator->getFilename();
            }
            $iterator->next();
        }
        return $extensions;
    }
}