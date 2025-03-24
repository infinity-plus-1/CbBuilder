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

namespace DS\CbBuilder\Updater;

use DS\CbBuilder\Config\CbBuilderConfig;
use DS\CbBuilder\FieldBuilder\FieldBuilder;
use DS\CbBuilder\Wrapper\Wrapper;
use Exception;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Custom exception class for updater-related errors.
 */
class UpdaterException extends Exception {}

/**
 * Class responsible for updating TypoScript and fields in the extension.
 */
final class Updater
{
    /**
     * Updates the TypoScript setup for the specified extension.
     *
     * @param string $path        Path to the extension directory.
     * @param string $extension   Name of the extension.
     * @param string $identifier  Unique identifier for the configuration.
     *
     * @throws UpdaterException If the TypoScript file does not exist.
     */
    public static function updateTyposcript(string $path, string $extension, string $identifier): void
    {
        $typoscriptPath = $path . '/Configuration/TypoScript/setup.typoscript';
        $filesystem = new Filesystem();
        if (!$filesystem->exists($typoscriptPath)) {
            throw new UpdaterException('TypoScript file not found.');
        }
        $typoscriptContent = Wrapper::extract(file_get_contents($typoscriptPath));
        
        // Function to add TypoScript via ExtensionManagementUtility
        $func = 
        "call_user_func(function () {\n" .
        "\t\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(\n" .
        "\t'$extension',\n" .
        "\t'setup',\n" .
        "\t'$typoscriptContent',\n" .
        "\t'defaultContentRendering'\n" .
        "\t);\n" .
        "});";

        if ($filesystem->exists($path . '/ext_localconf.php')) {
            Wrapper::inject($path . '/ext_localconf.php', $func, true);
        } else {
            $extLocalConfContent = "<?php\n\ndeclare(strict_types=1);\n\ndefined('TYPO3') or die();\n\n";
            $filesystem->touch($path . '/ext_localconf.php');
            $filesystem->dumpFile($path . '/ext_localconf.php', $extLocalConfContent . Wrapper::wrap($func));
        }
    }

    /**
     * Updates the fields for the specified path and identifier.
     *
     * @param string $path        Path to the extension directory.
     * @param string $identifier  Unique identifier for the configuration.
     */
    public static function updateFields(string $path, string $identifier): void
    {
        CbBuilderConfig::loadLocalConfig($identifier);
        $fieldBuilder = new FieldBuilder($path, $identifier);
        $fieldBuilder->buildFields();
    }
}
