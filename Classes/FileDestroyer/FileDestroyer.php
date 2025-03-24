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

namespace DS\CbBuilder\FileDestroyer;

use DS\CbBuilder\Config\CbBuilderConfig;
use DS\CbBuilder\FileCreater\FileCreater;
use DS\CbBuilder\Utility\ArrayParser;
use DS\CbBuilder\Wrapper\Wrapper;
use DS\CbBuilder\Utility\SimpleDatabaseQuery;
use DS\CbBuilder\Utility\Utility;
use Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

/**
 * Custom exception for file destroyer operations.
 */
class FileDestroyerException extends Exception {}

/**
 * Class responsible for destroying files based on specific configurations.
 */
final class FileDestroyer
{
    /**
     * List of files to be processed.
     */
    const FILE_LIST = [
        ['dir' => '', 'file' => '/ext_localconf.php'],
        ['dir' => '', 'file' => '/ext_tables.sql'],
        ['dir' => '/Configuration', 'file' => '/page.tsconfig'],
        ['dir' => '/Configuration/TypoScript', 'file' => '/setup.typoscript'],
        ['dir' => '/Configuration/TCA/Overrides', 'file' => '/tt_content.php']
    ];

    /**
     * Checks if a file is empty, excluding PHP opening and closing tags.
     *
     * @param string $file The path to the file to check.
     *
     * @return bool Whether the file is empty.
     *
     * @throws Exception If the file does not exist.
     * @throws FileException If the file cannot be opened.
     */
    public static function fileIsEmpty(string $file): bool
    {
        $filesystem = new Filesystem();
        if (!$filesystem->exists($file)) {
            throw new Exception("File does not exist.");
        }
        $fileHandle = fopen($file, "r");
        if (!$fileHandle) {
            throw new FileException("Could not open file '$file'");
        }
        $hasContent = false;
        while (($line = fgets($fileHandle)) !== false) {
            $line = trim($line);
            if ($line !== '' && !preg_match('/^<\?php$/', $line) && !preg_match('/^<\?php\s*$/', $line) && !preg_match('/^\?>$/', $line)) {
                $hasContent = true;
                break;
            }
        }
        fclose($fileHandle);
        return !$hasContent;
    }

    /**
     * Checks if a table can be removed based on the file's emptiness.
     *
     * @param string $file The path to the file to check.
     */
    public static function couldRemoveTable(string $file): void
    {
        $filesystem = new Filesystem();
        if (self::fileIsEmpty($file)) {
            $filesystem->remove($file);
            $splittedPath = array_reverse(Utility::stringSafeTrimExplode("/", $file));
            if ($splittedPath[1] !== 'Overrides') {
                $sdq = new SimpleDatabaseQuery();
                $table = GeneralUtility::revExplode('.', $splittedPath[0], 2)[0];
                if ($sdq->tableExists($table)) {
                    // ToDo: Drop table and corresponding ref fields from db automatically?
                    // If a specific configuration is set to true, maybe?
                }
            }
        }
    }

    /**
     * Removes the asset symlinks in EXT:Resources/Public/cb/$identifier and root/public/_assets
     * 
     * @param string $identifier The identifier of the content block
     */
    public static function destroySymlinks(string $identifier): void
    {
        $assetPath = Environment::getProjectPath() . '/public/' .
            PathUtility::getPublicResourceWebPath(CbBuilderConfig::getExtPublicPath($identifier), false);
        $extensionPubCbPath = CbBuilderConfig::getExtensionPublicPath($identifier) . "/cb/$identifier";
        $filesystem = new Filesystem();
        if ($filesystem->exists($extensionPubCbPath)) {
            $filesystem->remove($extensionPubCbPath);
        }
        if ($filesystem->exists($assetPath)) {
            $filesystem->remove($assetPath);
        }
    }

    /**
     * Removes an icon from the file Configuration/Icons.php.
     *
     * @param string $identifier The unique identifier of the icon.
     * @param string $path The path where the icon is stored. Must have the format
     *  EXT:my_extension/Resources/Public/Icons/icon.xxx
     */
    public static function removeIcon(string $identifier): void
    {
        $stdContent = "<?php\n\ndeclare(strict_types=1);\n\n" .
            "use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;\n\n" .
            "use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;\n\n";
        $iconsPhpPath = FileCreater::makeIconsPhp('');
        $parsedFile = ArrayParser::extractArraysFromFile($iconsPhpPath);
        if (is_array($parsedFile) && isset($parsedFile[0]) && is_array($parsedFile[0])) {
            $parsedFile = $parsedFile[0];
        } else {
            throw new ParseException (
                "Error: Could not parse 'EXT:CbBuilder/Configuration/Icons.php.\n" .
                "Tried in function 'removeIcon' with method: 'ArrayParser::extractArraysFromFile($iconsPhpPath);'"
            );
        }
        $identifier .= '_icon';
        unset($parsedFile[$identifier]);
        FileCreater::updateIconsPhp($parsedFile, $stdContent);
    }

    /**
     * Clears a file from the specified path.
     *
     * @param string $path The base path.
     * @param string $dir The directory.
     * @param string $file The file name.
     * @param string $identifier The identifier for erasing.
     */
    private static function _clearFromFile(string $path, string $dir, string $file, string $identifier): void
    {
        $path .= $dir;
        $filesystem = new Filesystem();
        if ($filesystem->exists($path)) {
            $path .= $file;
            if ($filesystem->exists($path)) {
                Wrapper::erase($path, true, $identifier);
            }
        }
    }

    /**
     * Clears files based on the provided identifier.
     *
     * @param string $identifier The identifier for clearing files.
     */
    public static function clearFiles(string $identifier): void
    {
        /**
         * Recursively gets table definitions from fields.
         *
         * @param array $fields The fields to process.
         * @param array $tables The tables found.
         */
        function getTableDefinitions(array $fields, array &$tables): void
        {
            foreach ($fields as $field) {
                if (isset($field['type']) && $field['type'] === 'Collection') {
                    if (isset($field['fields']) && is_array($field['fields'])) {
                        getTableDefinitions($field['fields'], $tables);
                    }
                    if (isset($field['identifier']) && is_string($field['identifier'])) {
                        $tables[$field['identifier']] = $field['identifier'];
                    }
                }
            }
        }

        if (!isset($GLOBALS['CbBuilder']['extensionPath'])) {
            CbBuilderConfig::loadLocalConfig($identifier);
        }

        $path = $GLOBALS['CbBuilder']['extensionPath'];
        foreach (self::FILE_LIST as $entry) {
            self::_clearFromFile($path, $entry['dir'], $entry['file'], $identifier);
        }

        $tables = [];
        $fields = [];
        $filesystem = new Filesystem();

        if ($filesystem->exists($path . "/ContentBlocks/$identifier/fields.yaml")) {
            $fields = Yaml::parseFile($path . "/ContentBlocks/$identifier/fields.yaml");
            if (isset($fields['fields'])) {
                $fields = $fields['fields'];
            }
        }

        $path .= "/Configuration/TCA";

        getTableDefinitions($fields, $tables);

        foreach ($tables as $table) {
            $file = "$path/$table.php";
            if ($filesystem->exists($file)) {
                Wrapper::erase($file, true, $identifier);
                self::couldRemoveTable($file);
            } elseif ($filesystem->exists("$path/Overrides/$table.php")) {
                $file = "$path/Overrides/$table.php";
                Wrapper::erase($file, true, $identifier);
                self::couldRemoveTable($file);
            }
        }
        FileDestroyer::destroySymlinks($identifier);
        FileDestroyer::removeIcon($identifier);
    }
}