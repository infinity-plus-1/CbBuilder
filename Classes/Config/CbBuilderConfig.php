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

namespace DS\CbBuilder\Config;

use DS\CbBuilder\Utility\Utility;
use Exception;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CbBuilderConfigException extends Exception {}

final class CbBuilderConfig
{
    /**
     * Returns the path to the extension in format 'EXT:extensionIdentifier'.
     *
     * @param string $identifier Identifier of the content block.
     * @return string Path to the extension.
     */
    public static function getExtPath(string $identifier): string
    {
        if (!isset($GLOBALS['CbBuilder']['contentBlocks'][$identifier])) {
            self::loadGlobalConfig();
        }
        return 'EXT:' . $GLOBALS['CbBuilder']['contentBlocks'][$identifier]['key'];
    }

    /**
     * Returns the path to the extension's Configuration dir in format 'EXT:extensionIdentifier/Configuration'.
     *
     * @param string $identifier Identifier of the content block.
     * @return string Path to the extension's Configuration directory.
     */
    public static function getExtConfigurationPath(string $identifier): string
    {
        return self::getExtPath($identifier) . '/Configuration';
    }

    /**
     * Returns the path to the extension's ContentBlocks dir in format 'EXT:extensionIdentifier/ContentBlocks'.
     *
     * @param string $identifier Identifier of the content block.
     * @return string Path to the extension's ContentBlocks directory.
     */
    public static function getExtContentBlocksPath(string $identifier): string
    {
        return self::getExtPath($identifier) . '/ContentBlocks';
    }

    /**
     * Returns the path to the dir of the given content block in format 'EXT:extensionIdentifier/ContentBlocks/$identifier'.
     *
     * @param string $identifier Identifier of the content block.
     * @return string Path to the content block directory.
     */
    public static function getExtContentBlockPath(string $identifier): string
    {
        return self::getExtPath($identifier) . '/ContentBlocks/' . $identifier;
    }

    /**
     * Returns the path to the asset dir of the given content block in format 'EXT:extensionIdentifier/ContentBlocks/$identifier/assets'.
     *
     * @param string $identifier Identifier of the content block.
     * @return string Path to the asset directory.
     */
    public static function getExtContentBlockAssetPath(string $identifier): string
    {
        return self::getExtPath($identifier) . '/ContentBlocks/' . $identifier . '/assets';
    }

    /**
     * Returns the path to the asset dir that contains the frontend files
     *  of the given content block in format 'EXT:extensionIdentifier/ContentBlocks/$identifier/assets/frontend'.
     *
     * @param string $identifier Identifier of the content block.
     * @return string Path to the frontend asset directory.
     */
    public static function getExtContentBlockFeAssetPath(string $identifier): string
    {
        return self::getExtPath($identifier) . '/ContentBlocks/' . $identifier . '/assets/frontend';
    }

    /**
     * Returns the path to the asset dir that contains the backend files
     *  of the given content block in format 'EXT:extensionIdentifier/ContentBlocks/$identifier/assets/frontend'.
     *
     * @param string $identifier Identifier of the content block.
     * @return string Path to the backend asset directory.
     */
    public static function getExtContentBlockBeAssetPath(string $identifier): string
    {
        return self::getExtPath($identifier) . '/ContentBlocks/' . $identifier . '/assets/backend';
    }

    /**
     * Returns the path to the extensions Resources/Public/cb directory.
     *
     * @param string|null $identifier Optional identifier for specific content blocks.
     * @return string Extension path.
     * @throws Exception If the path is not defined or does not exist.
     */
    public static function getExtPublicCbPath(string $identifier): string
    {
        return self::getExtPublicPath($identifier) . '/cb';
    }

    /**
     * Returns the path to the extensions Resources/Public/cb/$identifier directory.
     *
     * @param string|null $identifier Optional identifier for specific content blocks.
     * @return string Extension path.
     * @throws Exception If the path is not defined or does not exist.
     */
    public static function getExtPublicCbContentBlockPath(string $identifier): string
    {
        return self::getExtPublicCbPath($identifier) . '/' . $identifier;
    }

    /**
     * Returns the path to the extensions Resources/Public/cb/$identifier/assets directory.
     *
     * @param string|null $identifier Optional identifier for specific content blocks.
     * @return string Extension path.
     * @throws Exception If the path is not defined or does not exist.
     */
    public static function getExtPublicCbAssetsPath(string $identifier): string
    {
        return self::getExtPublicCbContentBlockPath($identifier) . '/assets';
    }

    /**
     * Returns the path to the extensions Resources/Public/cb/$identifier/assets/frontend directory.
     *
     * @param string|null $identifier Optional identifier for specific content blocks.
     * @return string Extension path.
     * @throws Exception If the path is not defined or does not exist.
     */
    public static function getExtPublicCbFrontendPath(string $identifier): string
    {
        return self::getExtPublicCbAssetsPath($identifier) . '/frontend';
    }

    /**
     * Returns the path to the extensions Resources/Public/cb/$identifier/assets/backend directory.
     *
     * @param string|null $identifier Optional identifier for specific content blocks.
     * @return string Extension path.
     * @throws Exception If the path is not defined or does not exist.
     */
    public static function getExtPublicCbBackendPath(string $identifier): string
    {
        return self::getExtPublicCbAssetsPath($identifier) . '/backend';
    }

    /**
     * Returns the path to the extensions Resources/Public directory.
     *
     * @param string|null $identifier Optional identifier for specific content blocks.
     * @return string Extension path.
     * @throws Exception If the path is not defined or does not exist.
     */
    public static function getExtPublicPath(string $identifier): string
    {
        return self::getExtPath($identifier) . '/Resources/Public';
    }

    /**
     * Returns the path to the assets.
     *
     * @param string $identifier Identifier of the content block.
     * @return string Path to the assets.
     */
    public static function getAssetPath(string $identifier): string
    {
        return CbBuilderConfig::getContentBlocksPath($identifier) . "/$identifier/assets";
    }

    /**
     * Returns the path to the content block.
     *
     * @param string|null $identifier Optional identifier to specify the content block directly.
     * @return string Path to the content block.
     * @throws Exception If the path is not defined or does not exist.
     */
    public static function getContentBlockPath(?string $identifier = NULL): string
    {
        $identifier = $identifier ?? self::getIdentifier();
        return self::getContentBlocksPath($identifier) . "/$identifier";
    }

    /**
     * Returns the path to the content blocks.
     *
     * @param string|null $identifier Optional identifier for specific content blocks.
     * @return string Path to the content blocks.
     * @throws Exception If the path is not defined or does not exist.
     */
    public static function getContentBlocksPath(?string $identifier = ''): string
    {
        return self::getExtensionPath($identifier) . '/ContentBlocks';
    }

    /**
     * Returns the path to the extensions Resources/Public directory.
     *
     * @param string|null $identifier Optional identifier for specific content blocks.
     * @return string Extension path.
     * @throws Exception If the path is not defined or does not exist.
     */
    public static function getExtensionPublicPath(?string $identifier = ''): string
    {
        $path = CbBuilderConfig::getExtensionPath($identifier) . '/Resources/Public';
        $filesystem = new Filesystem();
        if (!$filesystem->exists($path)) {
            GeneralUtility::mkdir_deep($path);
        }
        return $path;
    }

    /**
     * Returns the extension path.
     *
     * @param string|null $identifier Optional identifier for specific content blocks.
     * @return string Extension path.
     * @throws Exception If the path is not defined or does not exist.
     */
    public static function getExtensionPath(?string $identifier = ''): string
    {
        $path = '';
        if ($identifier === '') {
            if (!isset($GLOBALS['CbBuilder']) || !isset($GLOBALS['CbBuilder']['extensionPath'])) {
                throw new Exception(
                    "Error: No path is defined for this content block. Please manually check the 'contentBlocks.yaml' file to ensure the path exists for this content block."
                );
            }
            $path = $GLOBALS['CbBuilder']['extensionPath'];
        } else {
            if (
                !isset($GLOBALS['CbBuilder']['contentBlocks'][$identifier])
                || !isset($GLOBALS['CbBuilder']['contentBlocks'][$identifier]['path'])
            ) {
                $globalConfig = CbBuilderConfig::loadGlobalConfig();
                if (
                    !isset($globalConfig[$identifier])
                    || !isset($globalConfig[$identifier]['path'])
                ) {
                    throw new Exception(
                        "Error: No path is defined for the content block with identifier '$identifier'. Please manually check the 'contentBlocks.yaml' file to ensure the path exists for this content block."
                    );
                }
            }
            $path = $GLOBALS['CbBuilder']['contentBlocks'][$identifier]['path'];
        }
        
        $filesystem = new Filesystem();
        if (!$filesystem->exists($path)) {
            throw new Exception(
                "Error: The extension path '$path' is not valid. Please manually check the 'contentBlocks.yaml' file to ensure the path leads to this content block."
            );
        }
        return $path;
    }

    /**
     * Returns the configuration path.
     *
     * @return string Configuration path.
     * @throws Exception If the path is not defined or does not exist.
     */
    public static function getConfigurationPath(): string
    {
        if (!isset($GLOBALS['CbBuilder']) || !isset($GLOBALS['CbBuilder']['extensionPath'])) {
            throw new Exception(
                "Error: No path is defined for this content block. Please manually check the 'contentBlocks.yaml' file to ensure the path exists for this content block."
            );
        }
        $path = $GLOBALS['CbBuilder']['extensionPath']. '/Configuration';
        $filesystem = new Filesystem();
        if (!$filesystem->exists($path)) {
            throw new Exception(
                "Error: The extension path '$path' is not valid. Please manually check the 'contentBlocks.yaml' file to ensure the path leads to this content block."
            );
        }
        return $path;
    }

    /**
     * Returns the TCA path.
     *
     * @return string TCA path.
     * @throws Exception If the path is not defined or does not exist.
     */
    public static function getTCAPath(): string
    {
        if (!isset($GLOBALS['CbBuilder']) || !isset($GLOBALS['CbBuilder']['extensionPath'])) {
            throw new Exception(
                "Error: No path is defined for this content block. Please manually check the 'contentBlocks.yaml' file to ensure the path exists for this content block."
            );
        }
        $path = $GLOBALS['CbBuilder']['extensionPath']. '/Configuration/TCA';
        $filesystem = new Filesystem();
        if (!$filesystem->exists($path)) {
            throw new Exception(
                "Error: The extension path '$path' is not valid. Please manually check the 'contentBlocks.yaml' file to ensure the path leads to this content block."
            );
        }
        return $path;
    }

    /**
     * Returns the overrides path.
     *
     * @return string Overrides path.
     * @throws Exception If the path is not defined or does not exist.
     */
    public static function getOverridesPath(): string
    {
        if (!isset($GLOBALS['CbBuilder']) || !isset($GLOBALS['CbBuilder']['extensionPath'])) {
            throw new Exception(
                "Error: No path is defined for this content block. Please manually check the 'contentBlocks.yaml' file to ensure the path exists for this content block."
            );
        }
        $path = $GLOBALS['CbBuilder']['extensionPath']. '/Configuration/TCA/Overrides';
        $filesystem = new Filesystem();
        if (!$filesystem->exists($path)) {
            throw new Exception(
                "Error: The extension path '$path' is not valid. Please manually check the 'contentBlocks.yaml' file to ensure the path leads to this content block."
            );
        }
        return $path;
    }

    /**
     * Returns the templates path.
     *
     * @return string Templates path.
     * @throws Exception If the path is not defined or does not exist.
     */
    public static function getTemplatesPath(?string $identifier = NULL): string
    {
        if (!isset($GLOBALS['CbBuilder']) || !isset($GLOBALS['CbBuilder']['extensionPath'])) {
            throw new Exception(
                "Error: No path is defined for this content block. Please manually check the 'contentBlocks.yaml' file to ensure the path exists for this content block."
            );
        }
        $identifier = $identifier ?? CbBuilderConfig::getIdentifier();
        $path = $GLOBALS['CbBuilder']['extensionPath'] . '/ContentBlocks/' . $identifier . '/Templates';
        return $path;
    }

    /**
     * Returns the identifier for the content block.
     *
     * @return string Identifier.
     * @throws CbBuilderConfigException If the identifier is not defined.
     */
    public static function getIdentifier(): string
    {
        if (!isset($GLOBALS['CbBuilder']['identifier'])) {
            throw new CbBuilderConfigException(
                "Error: No identifier is defined for this content block. Please manually check the 'contentBlocks.yaml' file to ensure the identifier is set for this content block."
            );
        }
        $identifier = $GLOBALS['CbBuilder']['identifier'];
        $path = $GLOBALS['CbBuilder']['extensionPath']. '/ContentBlocks';
        $filesystem = new Filesystem();
        if (!$filesystem->exists($path)) {
            throw new Exception(
                "Error: No 'ContentBlocks' folder is found at path '$path'. Please manually check the 'contentBlocks.yaml' file to ensure the path leads to a 'ContentBlocks' folder in your extension. Further check if your extension has that folder at all."
            );
        }
        if (!$filesystem->exists($path . '/' . $identifier)) {
            throw new Exception(
                "Error: The identifier '$identifier' seems not valid. Please manually check the 'contentBlocks.yaml' file to ensure the identifier is valid for this content block and a corresponding directory exists at the path: 'EXT:/ContentBlocks/'"
            );
        }
        return $identifier;
    }

    /**
     * Loads the global configuration from the 'contentBlocks.yaml' file.
     *
     * @return array Global configuration.
     * @throws RuntimeException If the configuration file is missing or malformed.
     * @throws FileException If the file cannot be found.
     */
    public static function loadGlobalConfig(): array
    {
        $filesystem = new Filesystem();
        if ($filesystem->exists(__DIR__ . "/../../Configuration/contentBlocks.yaml")) {
            $contentBlocks = Yaml::parseFile(__DIR__ . "/../../Configuration/contentBlocks.yaml");
            $contentBlocks = Utility::skipZeroIndexed($contentBlocks);
            if (isset($contentBlocks['contentBlocks'])) {
                if ($contentBlocks['contentBlocks'] === []) {
                    throw new RuntimeException(
                        "The file 'cbBuilder/Configuration/contentBlocks.yaml' does not contain any content blocks. Please run 'cb:make' before running 'cb:update'."
                    );
                }
                
                $GLOBALS['CbBuilder']['contentBlocks'] = $contentBlocks['contentBlocks'];
            } else {
                throw new RuntimeException(
                    "The file 'cbBuilder/Configuration/contentBlocks.yaml' does not contain a 'contentBlocks' key. Please run 'cb:repair' in the console."
                );
            }
        } else {
            throw new FileException(
                "The file 'cbBuilder/Configuration/contentBlocks.yaml' could not be found. Please repair via composer."
            );
        }
        return $GLOBALS['CbBuilder']['contentBlocks'];
    }

    /**
     * Loads the local configuration for a specific content block.
     *
     * @param string $identifier Identifier of the content block.
     * @return array Local configuration.
     * @throws RuntimeException If the configuration cannot be loaded.
     */
    public static function loadLocalConfig(string $identifier): array
    {
        $globalConfig = [];
        if (!isset($GLOBALS['CbBuilder']['contentBlocks'])) {
            $globalConfig = CbBuilderConfig::loadGlobalConfig();
        } else {
            $globalConfig = $GLOBALS['CbBuilder']['contentBlocks'];
        }
        if (isset($globalConfig[$identifier])) {
            $globalConfig = $globalConfig[$identifier];
        } else {
            throw new RuntimeException(
                "The configuration '\$GLOBALS['CbBuilder']['contentBlocks']' could neither be set nor loaded. Please try a repair via composer first and then run 'cb:repair' in your console."
            );
        }
        $path = $globalConfig['path'];
        $cbConfig = Yaml::parseFile($globalConfig['path'] . '/ContentBlocks/' . $identifier . '/cbConfig.yaml');
        $GLOBALS['CbBuilder'] = array_merge($GLOBALS['CbBuilder'], $cbConfig);
        $GLOBALS['CbBuilder']['extensionPath'] = $path;
        $GLOBALS['CbBuilder']['contentBlockPath'] = $path . '/ContentBlocks/' . $identifier;
        $GLOBALS['CbBuilder']['identifier'] = $identifier;
        return $cbConfig;
    }

    /**
     * Returns a local configuration setting for the current content block.
     *
     * @param string $identifier Identifier of the content block.
     * @return mixed Local configuration setting or NULL if it does not exist.
     */
    public static function getLocalConfig(string $config): mixed
    {
        if (isset($GLOBALS['CbBuilder']['config'][$config])) {
            return $GLOBALS['CbBuilder']['config'][$config];
        } else {
            return NULL;
        }
    }

    /**
     * Returns the value of 'crossParsing' defined in cbConfig.yaml.
     * 
     * If true, the parser will also alter the fields.yaml file, so cross-development against tt_content.php
     *  is possible.
     * 
     * @param string $identifier Identifier of the content block.
     * 
     * @return bool The value of 'crossParsing' defined in cbConfig.yaml.
     */
    public static function isCrossParsing(?string $identifier = NULL): bool
    {
        if (isset($GLOBALS['CbBuilder']['config']['crossParsing'])) {
            return $GLOBALS['CbBuilder']['config']['crossParsing'];
        } elseif ($identifier) {
            $config = CbBuilderConfig::loadLocalConfig($identifier);
            if (isset($config['config']['crossParsing'])) {
                return $GLOBALS['config']['crossParsing'];
            } else {
                throw new Exception (
                    "Could not load local config or the configuration 'crossParsing' for ContentBlock " .
                    "with identifier '$identifier'.\nError occured in function: 'isCrossParsing'"
                );
            }
        } else {
            throw new Exception (
                "Could not load local config or the configuration 'crossParsing'.\n" .
                "Error occured in function: 'isCrossParsing'"
            );
        }
    }
}