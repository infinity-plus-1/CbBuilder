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

namespace DS\CbBuilder\BasicBuilder;

use DS\CbBuilder\Config\CbBuilderConfig;
use DS\CbBuilder\FileCreater\FileCreater;
use Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class BasicBuilderException extends Exception {}

final class BasicBuilder
{
    /**
     * Creates the 'ContentBlocks' directory within the specified path.
     *
     * @param string $path Path where the 'ContentBlocks' directory will be created.
     * @throws BasicBuilderException If the directory cannot be created.
     */
    public static function makeContentBlocksDir(string $path): void
    {
        if (is_dir($path)) {
            if (!is_dir($path . '/' . 'ContentBlocks')) {
                $created = mkdir($path . '/' . 'ContentBlocks');
                if (!$created) {
                    throw new BasicBuilderException(
                        "Error: Unable to create the 'ContentBlocks' directory within the path '$path'. Please check the path and permissions, then try again."
                    );
                }
            }
        } else {
            throw new BasicBuilderException(
                "Error: No extension found within the path '$path'. Please check the path and permissions, then try again."
            );
        }
    }

    /**
     * Creates a directory for a specific content block identifier within the 'ContentBlocks' directory.
     *
     * @param string $path Path to the 'ContentBlocks' directory.
     * @param string $identifier Identifier of the content block.
     * @throws BasicBuilderException If the directory cannot be created.
     */
    public static function makeContentBlocksIdentifierDir(string $path, string $identifier): void
    {
        if (is_dir($path)) {
            $path .= "/ContentBlocks";
            if (is_dir($path)) {
                $created = mkdir($path . "/$identifier");
                if (!$created) {
                    throw new BasicBuilderException(
                        "Error: Unable to create the directory '$identifier' within the path '$path'. Please check the path and permissions, then try again."
                    );
                }
            } else {
                throw new BasicBuilderException(
                    "Error: The 'ContentBlocks' directory could not be found within the path '$path'. Please check the path and permissions, then try again."
                );
            }
        } else {
            throw new BasicBuilderException(
                "Error: No extension found within the path '$path'. Please check the path and permissions, then try again."
            );
        }
    }

    /**
     * Adds a content block to the list in the 'contentBlocks.yaml' file.
     *
     * @param string $path Path to the content block.
     * @param string $extension Extension name.
     * @param string $namespace Namespace of the content block.
     * @param string $name Name of the content block.
     * @param string $identifier Identifier of the content block.
     * @param string $desc Description of the content block.
     * @param string $placeAt Position where the content block will be placed.
     * @param string $position Position of the content block.
     * @param string $group Group of the content block.
     */
    private static function _addContentBlockToList(
        string $path,
        string $extensionName,
        string $extension,
        string $name,
        string $identifier,
        string $desc,
        string $placeAt,
        string $position,
        string $group
    ): void
    {
        $configurationPath = __DIR__ . "/../../Configuration/";

        $filesystem = new Filesystem();

        if (!$filesystem->exists($configurationPath)) {
            $filesystem->mkdir($configurationPath);
        }

        $yamlPath = $configurationPath . "contentBlocks.yaml";
        $entry = [
            'identifier' => $identifier,
            'name' => $name,
            'key' => $extension,
            'path' => $path,
            'extension' => $extensionName,
            'description' => $desc,
            'placeAt' => $placeAt,
            'position' => $position,
            'group' => $group
        ];

        if (!$filesystem->exists($yamlPath)) {
            $filesystem->dumpFile($yamlPath, "#Do not touch this file!\ncontentBlocks:\n");
        }
        $yaml = Yaml::parseFile($yamlPath);
        $yaml['contentBlocks'][$identifier] = $entry;
        $yaml = Yaml::dump($yaml, PHP_INT_MAX, 2);
        file_put_contents($yamlPath, $yaml);
    }

    /**
     * Builds the basic structure for a content block.
     *
     * @param string $path Path to the content block.
     * @param string $extension Extension name.
     * @param string $namespace Namespace of the content block.
     * @param string $name Name of the content block.
     * @param string $identifier Identifier of the content block.
     * @param string $desc Description of the content block.
     * @param string $placeAt Position where the content block will be placed.
     * @param string $position Position of the content block.
     * @param string $group Group of the content block.
     * @param string $include Whether to include additional files.
     */
    public static function buildBasicStructure (
        string $path,
        string $extensionName,
        string $extension,
        string $name,
        string $identifier,
        string $desc,
        string $placeAt,
        string $position,
        string $group,
        string $include
    ): void {
        BasicBuilder::_addContentBlockToList($path, $extensionName, $extension, $name, $identifier, $desc, $placeAt, $position, $group);
        FileCreater::makeCbConfigYaml($path, $identifier);
        FileCreater::makeDefaultPartial($path, $identifier);
        FileCreater::makeDefaultLayout($path, $identifier);
        CbBuilderConfig::loadGlobalConfig();
        CbBuilderConfig::loadLocalConfig($identifier);
        FileCreater::makeBackendPreview($path, $extensionName, $identifier);
        FileCreater::makeFrontendPreview($path, $extensionName, $identifier);
        FileCreater::makeTtContent($path, $identifier, $name, $desc, $placeAt, $position, $group, $include);
        FileCreater::makeFieldsYaml($path, $identifier, $name, $desc, $placeAt, $position, $group, $include);
        FileCreater::makeClassesMapYaml($path, $identifier);
        FileCreater::makeCbExtLocalConf();
        FileCreater::makeCbExtLocalConf();
        FileCreater::updateCssAssets($identifier);
        FileCreater::updateJsAssets($identifier);
        FileCreater::addIcon($identifier);
    }
}