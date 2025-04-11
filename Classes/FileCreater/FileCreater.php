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

namespace DS\CbBuilder\FileCreater;

use DirectoryIterator;
use DS\CbBuilder\Config\CbBuilderConfig;
use DS\CbBuilder\Updater\Updater;
use DS\CbBuilder\Utility\ArrayParser;
use DS\CbBuilder\Utility\CbPathUtility;
use DS\CbBuilder\Utility\Utility;
use DS\CbBuilder\Wrapper\Wrapper;
use Exception;
use InvalidArgumentException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Resource\ResourceCompressor;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

/**
 * Custom exception for file creation errors.
 */
class FileCreaterException extends Exception {}

/**
 * Class responsible for creating backend and frontend previews for files.
 */
final class FileCreater
{
    /**
     * Creates the templates dir of the content block.
     * 
     * @param string $identifier The identifier of the content block.
     */
    public static function makeTemplatesDir(string $identifier): void
    {
        $filesystem = new Filesystem();
        $path = CbBuilderConfig::getContentBlocksPath($identifier);
        $templatesPath = CbBuilderConfig::getTemplatesPath();
        if ($filesystem->exists($path)) {
            $filesystem->mkdir($templatesPath);
        } else {
            throw new DirectoryNotFoundException (
                "Content block directory of the given identifier does not exist."
            );
        }
    }

    /**
     * Creates the file fe_preview.html in the templates dir of the content block.
     * 
     * @param string $identifier The identifier of the content block.
     */
    public static function makeFePreviewHtml(string $identifier): void
    {
        $templatesPath = CbBuilderConfig::getTemplatesPath();
        $filesystem = new Filesystem();
        if (!$filesystem->exists($templatesPath)) {
            FileCreater::makeTemplatesDir($identifier);
        }
        $fePath = $templatesPath . '/fe_preview.html';
        $originFePath = __DIR__ . '/../../Templates/fe_preview.html';
        if (!$filesystem->exists($fePath) && $filesystem->exists($originFePath)) {
            $filesystem->touch($fePath);
            $filesystem->dumpFile($fePath, str_replace('{%%Identifier%%}', $identifier, $filesystem->readFile($originFePath)));
        }
    }

    /**
     * Creates the file fe_preview.html in the templates dir of the content block.
     * 
     * @param string $identifier The identifier of the content block.
     */
    public static function makeBePreviewHtml(string $identifier): void
    {
        $templatesPath = CbBuilderConfig::getTemplatesPath();
        $filesystem = new Filesystem();
        if (!$filesystem->exists($templatesPath)) {
            FileCreater::makeTemplatesDir($identifier);
        }
        $bePath = $templatesPath . '/be_preview.html';
        $originBePath = __DIR__ . '/../../Templates/be_preview.html';
        if (!$filesystem->exists($bePath) && $filesystem->exists($originBePath)) {
            $filesystem->touch($bePath);
            $filesystem->dumpFile($bePath, str_replace('{%%Identifier%%}', $identifier, $filesystem->readFile($originBePath)));
        }
    }

    /**
     * Creates the directory ExtensionName/Resources/Public/cb/ContentBlockIdentifier of the content block.
     * 
     * @param string $identifier The identifier of the content block.
     */
    public static function makePublicContentBlockFolder(string $identifier): void
    {
        $path = CbBuilderConfig::getExtensionPublicPath($identifier) . "/cb/$identifier";
        Utility::createIfNot($path);
    }

    /**
     * Creates the directories ExtensionName/ContentBlocks/ContentBlockIdentifier/assets/frontend
     *  and /backend plus the main.css files.
     * 
     * @param string $identifier The identifier of the content block.
     */
    public static function makeContentBlockAssets(string $identifier): void
    {
        $path = CbBuilderConfig::getContentBlocksPath($identifier) . "/$identifier";
        Utility::createIfNot($path . '/assets/frontend/main.css', false);
        Utility::createIfNot($path . '/assets/backend/main.css', false);
    }

    /**
     * Creates the symlink ExtensionName/Public/Resource/cb/ContentBlockIdentifier/assets pointing to
     *  ExtensionName/ContentBlocks/ContentBlockIdentifier/assets
     * 
     * @param string $identifier The identifier of the content block.
     */
    public static function createPublicSymlink(string $identifier): void
    {
        $filesystem = new Filesystem();
        FileCreater::makePublicContentBlockFolder($identifier);
        FileCreater::makeContentBlockAssets($identifier);
        $cbPath = CbBuilderConfig::getContentBlocksPath($identifier) . "/$identifier";
        $absPubPath = CbBuilderConfig::getExtensionPublicPath($identifier);
        $absPubIdentifierPath = $absPubPath . "/cb/$identifier";
        $cbRelPath = $filesystem->makePathRelative($cbPath, $absPubIdentifierPath);
        $cbRelAssetPath = $cbRelPath . '/assets';
        $absPubAssetPath = $absPubIdentifierPath . '/assets';
        $filesystem->symlink($cbRelAssetPath, $absPubAssetPath);
    }

    /**
     * Creates the symlink Root/Public/_Assets/HashedPath pointing to
     *  ExtensionName/Public/Resource
     * 
     * @param string $identifier The identifier of the content block.
     */
    public static function createPublicAssetSymlink(string $identifier): void
    {
        $filesystem = new Filesystem();
        $assetPath = PathUtility::getPublicResourceWebPath(CbBuilderConfig::getExtPublicPath($identifier), false);
        $hashed = explode('/', $assetPath)[1];
        $path = Environment::getProjectPath() . '/public/_assets';
        $pathWithHash = $path . '/' . $hashed;
        $absPubPath = CbBuilderConfig::getExtensionPublicPath($identifier);
        Utility::createIfNot($path);
        if (!$filesystem->exists($pathWithHash)) {
            GeneralUtility::mkdir_deep($path);
            $relPath = $filesystem->makePathRelative($absPubPath, $path);
            $filesystem->symlink($relPath, $pathWithHash);
        }
    }

    /**
     * Collect CSS files and update the corresponding fe- and be_preview.html files.
     * 
     * Symlinks are created if they do not exists.
     * Point from-to: Root/Public/_assets/PathHash -> Extension/Resources/Public -> ContentBlock assets
     * 
     * @param string $identifier The identifier of the ContentBlock
     */
    public static function updateCssAssets(string $identifier): void
    {
        $filesystem = new Filesystem();
        FileCreater::createPublicSymlink($identifier);
        FileCreater::createPublicAssetSymlink($identifier);
        $absPubAssetPath = CbBuilderConfig::getExtensionPublicPath($identifier) . "/cb/$identifier/assets";
        
        
        //Frontend
        $feExtPath = CbBuilderConfig::getExtPublicCbFrontendPath($identifier);
        $feFiles = '';
        $fePath = $absPubAssetPath . '/frontend';
        $cssFiles = CbPathUtility::scanForCssFiles(new DirectoryIterator($fePath));
        foreach ($cssFiles as $file) {
            $fileData = CbPathUtility::getFileData($file);
            if ($fileData) {
                if (isset($fileData['type']) && strtolower($fileData['type']) === 'css') {
                    if (isset($fileData['name'])) {
                        $fileIdentifier = "cb_$identifier" . '_fe_' . $fileData['name'] . '_' . $fileData['type'];
                        $filePath = $feExtPath . '/' . $fileData['name'] . '.' . $fileData['type'];
                        $feFiles .= '<f:asset.css identifier="' . $fileIdentifier . '" href="' . $filePath . '"' . " />\n";
                    }
                }
            }
        }
        $templatesPath = CbBuilderConfig::getTemplatesPath();
        $feHtmlPath = $templatesPath . '/fe_preview.html';
        if (!$filesystem->exists($templatesPath) || !$filesystem->exists($feHtmlPath)) {
            FileCreater::makeFePreviewHtml($identifier);
        }
        Wrapper::inject($feHtmlPath, $feFiles, true, '', $identifier . '_css', '<!--&%!§(§?%&', '&%?§)§!%& DO NOT DELTE THIS PART-->', '');

        //Backend
        $beExtPath = CbBuilderConfig::getExtPublicCbBackendPath($identifier);
        $beFiles = '';
        $bePath = $absPubAssetPath . '/backend';
        $cssFiles = CbPathUtility::scanForCssFiles(new DirectoryIterator($bePath));
        foreach ($cssFiles as $file) {
            $fileData = CbPathUtility::getFileData($file);
            if ($fileData) {
                if (isset($fileData['type']) && strtolower($fileData['type']) === 'css') {
                    if (isset($fileData['name'])) {
                        $fileIdentifier = "cb_$identifier" . '_be_' . $fileData['name'] . '_' . $fileData['type'];
                        $filePath = $beExtPath . '/' . $fileData['name'] . '.' . $fileData['type'];
                        $beFiles .= '<f:asset.css identifier="' . $fileIdentifier . '" href="' . $filePath . '"' . " />\n";
                    }
                }
            }
        }
        $templatesPath = CbBuilderConfig::getTemplatesPath();
        $beHtmlPath = $templatesPath . '/be_preview.html';
        if (!$filesystem->exists($templatesPath) || !$filesystem->exists($beHtmlPath)) {
            FileCreater::makeBePreviewHtml($identifier);
        }
        Wrapper::inject($beHtmlPath, $beFiles, true, '', $identifier . '_css', '<!--&%!§(§?%&', '&%?§)§!%& DO NOT DELTE THIS PART-->', '');
    }

    /**
     * Collect JS files and update the corresponding fe- and be_preview.html files.
     * 
     * Symlinks are created if they do not exists.
     * Point from-to: Root/Public/_assets/PathHash -> Extension/Resources/Public -> ContentBlock assets
     * 
     * @param string $identifier The identifier of the ContentBlock
     */
    public static function updateJsAssets(string $identifier): void
    {
        $filesystem = new Filesystem();
        FileCreater::createPublicSymlink($identifier);
        FileCreater::createPublicAssetSymlink($identifier);
        $absPubAssetPath = CbBuilderConfig::getExtensionPublicPath($identifier) . "/cb/$identifier/assets";
        
        
        //Frontend
        $feExtPath = CbBuilderConfig::getExtPublicCbFrontendPath($identifier);
        $feFiles = '';
        $fePath = $absPubAssetPath . '/frontend';
        $jsFiles = CbPathUtility::scanForJsFiles(new DirectoryIterator($fePath));
        foreach ($jsFiles as $file) {
            $fileData = CbPathUtility::getFileData($file);
            if ($fileData) {
                if (isset($fileData['type']) && strtolower($fileData['type']) === 'js') {
                    if (isset($fileData['name'])) {
                        $fileIdentifier = "cb_$identifier" . '_fe_' . $fileData['name'] . '_' . $fileData['type'];
                        $filePath = $feExtPath . '/' . $fileData['name'] . '.' . $fileData['type'];
                        $feFiles .= '<f:asset.script identifier="' . $fileIdentifier . '" src="' . $filePath . '"' . " />\n";
                    }
                }
            }
        }
        $templatesPath = CbBuilderConfig::getTemplatesPath();
        $feHtmlPath = $templatesPath . '/fe_preview.html';
        if (!$filesystem->exists($templatesPath) || !$filesystem->exists($feHtmlPath)) {
            FileCreater::makeFePreviewHtml($identifier);
        }
        Wrapper::inject($feHtmlPath, $feFiles, true, '', $identifier . '_js', '<!--&%!§(§?%&', '&%?§)§!%& DO NOT DELTE THIS PART-->', '');

        //Backend
        $beExtPath = CbBuilderConfig::getExtPublicCbBackendPath($identifier);
        $beFiles = '';
        $bePath = $absPubAssetPath . '/backend';
        $jsFiles = CbPathUtility::scanForJsFiles(new DirectoryIterator($bePath));
        foreach ($jsFiles as $file) {
            $fileData = CbPathUtility::getFileData($file);
            if ($fileData) {
                if (isset($fileData['type']) && strtolower($fileData['type']) === 'js') {
                    if (isset($fileData['name'])) {
                        $fileIdentifier = "cb_$identifier" . '_be_' . $fileData['name'] . '_' . $fileData['type'];
                        $filePath = $beExtPath . '/' . $fileData['name'] . '.' . $fileData['type'];
                        $beFiles .= '<f:asset.script identifier="' . $fileIdentifier . '" src="' . $filePath . '"' . " />\n";
                    }
                }
            }
        }
        $templatesPath = CbBuilderConfig::getTemplatesPath();
        $beHtmlPath = $templatesPath . '/be_preview.html';
        if (!$filesystem->exists($templatesPath) || !$filesystem->exists($beHtmlPath)) {
            FileCreater::makeBePreviewHtml($identifier);
        }
        Wrapper::inject($beHtmlPath, $beFiles, true, '', $identifier . '_js', '<!--&%!§(§?%&', '&%?§)§!%& DO NOT DELTE THIS PART-->', '');
    }

    /**
     * Creates a backend preview for the given path, extension, and identifier.
     *
     * @param string $path The path where the preview will be created.
     * @param string $extension The extension related to the preview.
     * @param string $identifier Unique identifier for the preview.
     *
     */
    public static function makeBackendPreview(string $path, string $extension, string $identifier): void
    {
        $extension = str_replace('-', '_', $extension);
        $setting = "\nmod.web_layout.tt_content.preview.$identifier = EXT:$extension/ContentBlocks/$identifier/Templates/be_preview.html\n";

        Utility::createIfNot($path . '/Configuration');

        $filesystem = new Filesystem();

        // Inject or create page.tsconfig with the necessary setting.
        if ($filesystem->exists($path . '/Configuration/page.tsconfig')) {
            Wrapper::inject($path . '/Configuration/page.tsconfig', $setting, true);
        } else {
            $filesystem->touch($path . '/Configuration/page.tsconfig');
            $filesystem->dumpFile($path . '/Configuration/page.tsconfig', Wrapper::wrap($setting));
        }

        FileCreater::makeBePreviewHtml($identifier);
    }

    /**
     * Creates a frontend preview for the given path, extension, and identifier.
     *
     * @param string $path The path where the preview will be created.
     * @param string $extension The extension related to the preview.
     * @param string $identifier Unique identifier for the preview.
     *
     */
    public static function makeFrontendPreview(string $path, string $extension, string $identifier): void
    {
        $extension = str_replace('-', '_', $extension);
        $templatesPath = __DIR__ . '/../../Templates/';
        $setupTyposcript = file_get_contents($templatesPath . 'setup.typoscript');

        Utility::createIfNot($path . '/Configuration/TypoScript');

        // Prepare the setup typoscript content.
        $setupTyposcript = str_replace('{%%Extension%%}', $extension, str_replace('{%%Identifier%%}', $identifier, $setupTyposcript));

        $filesystem = new Filesystem();
        // Inject or create setup.typoscript with the necessary content.
        if ($filesystem->exists($path . '/Configuration/TypoScript/setup.typoscript')) {
            Wrapper::inject($path . '/Configuration/TypoScript/setup.typoscript', $setupTyposcript, true);
        } else {
            $filesystem->touch($path . '/Configuration/TypoScript/setup.typoscript');
            $filesystem->dumpFile($path . '/Configuration/TypoScript/setup.typoscript', Wrapper::wrap($setupTyposcript));
        }

        // Update typoscript settings.
        Updater::updateTyposcript($path, $extension, $identifier);

        FileCreater::makeFePreviewHtml($identifier);
    }

    /**
     * Creates a tt_content configuration for the given path, identifier, name, description, placeAt, position, group, and include settings.
     *
     * @param string $path The path where the configuration will be created.
     * @param string $identifier Unique identifier for the configuration.
     * @param string $name The name of the configuration.
     * @param string $desc The description of the configuration.
     * @param string $placeAt The placeAt setting for the configuration.
     * @param string $position The position setting for the configuration.
     * @param string $group The group setting for the configuration.
     * @param string $include Whether to include additional settings ('yes' or 'no').
     *
     * @throws FileCreaterException If directory creation fails.
     */
    public static function makeTtContent(string $path, string $identifier, string $name, string $desc, string $placeAt, string $position, string $group, string $include): void
    {
        if (is_dir($path . '/Configuration')) {
            // Ensure the TCA directory exists.
            if (!is_dir($path . '/Configuration/TCA')) {
                if (!mkdir($path . '/Configuration/TCA', 0777, true)) {
                    throw new FileCreaterException('Failed to create TCA directory.');
                }
            }

            // Ensure the Overrides directory exists.
            if (!is_dir($path . '/Configuration/TCA/Overrides')) {
                if (!mkdir($path . '/Configuration/TCA/Overrides')) {
                    throw new FileCreaterException('Failed to create Overrides directory.');
                }
            }

            $filesystem = new Filesystem();
            $ttContentTemplate = file_get_contents(__DIR__ . '/../../Templates/tt_content.php');

            // Prepare the tt_content template with the given settings.
            $ttContentTemplate = str_replace(
                '{%%position%%}',
                $position,
                str_replace(
                    '{%%placeAt%%}',
                    $placeAt,
                    str_replace(
                        '{%%desc%%}',
                        $desc,
                        str_replace(
                            '{%%name%%}',
                            $name,
                            str_replace(
                                '{%%identifier%%}',
                                $identifier,
                                str_replace(
                                    '{%%icon%%}',
                                    $identifier . '_icon',
                                    str_replace(
                                        '{%%group%%}',
                                        $group,
                                        $ttContentTemplate
                                    )
                                )
                            )
                        )
                    )
                )
            );

            // Determine the include settings based on the $include parameter.
            $override = '';
            if ($include === 'yes') {
                $include = 'cb_index,--palette--;;header,bodytext';
                $override = file_get_contents(__DIR__ . '/../../Templates/columnsOverrides.temp');
            } else {
                $include = 'cb_index';
            }

            // Update the template with the include and override settings.
            $ttContentTemplate = str_replace(
                '{%%basicInclude%%}',
                $include,
                str_replace(
                    '{%%columnsOverrides%%}',
                    $override,
                    $ttContentTemplate
                )
            );

            // Inject or create the tt_content.php file.
            if ($filesystem->exists($path . '/Configuration/TCA/Overrides/tt_content.php')) {
                Wrapper::inject($path . '/Configuration/TCA/Overrides/tt_content.php', $ttContentTemplate, true);
            } else {
                $ttContent =    "<?php\n\ndeclare(strict_types=1);\n\ndefined('TYPO3') or die();\n\n";
                file_put_contents($path . '/Configuration/TCA/Overrides/tt_content.php', $ttContent . Wrapper::wrap($ttContentTemplate));
            }

            // Add the cb_index palette configuration.
            $cbSettingPalette = <<<'EOT'
                $GLOBALS['TCA']['tt_content']['columns']['cb_index'] = [
                    "label" => "Cb Settings",
                    "description" => "Modify the content element to your needings.",
                    "config" => [
                        "type" => "user",
                        "renderType" => "attributePalette"
                    ]
                ];
                EOT;
            Wrapper::inject(
                $path . '/Configuration/TCA/Overrides/tt_content.php',
                $cbSettingPalette,
                true,
                '//',
                "CB_INDEX_PALETTE",
                "§%!&(&?%§",
                "§%!&)&?%§",
                "DO NOT TOUCH THIS PART OF CODE UNLESS YOU WANT TO DEINSTALL THE WHOLE EXTENSION"
            );
        }
    }

    /**
     * Creates a cbConfig.yaml file for the given path and identifier.
     *
     * @param string $path The path where the configuration will be created.
     * @param string $identifier Unique identifier for the configuration.
     */
    public static function makeCbConfigYaml(string $path, string $identifier): void
    {
        $templatesPath = __DIR__ . '/../../Templates/';
        $cbConfig = file_get_contents($templatesPath . 'cbConfig.yaml');
        $cbConfig = str_replace('{%%Path%%}', $path, $cbConfig);
        file_put_contents($path . "/ContentBlocks/$identifier/cbConfig.yaml", $cbConfig);
    }

    /**
     * Creates a default partial for the given path and identifier.
     *
     * @param string $path The path where the partial will be created.
     * @param string $identifier Unique identifier for the partial.
     *
     * @throws FileCreaterException If directory creation fails.
     */
    public static function makeDefaultPartial(string $path, string $identifier): void
    {
        $templatesPath = __DIR__ . '/../../Templates/';
        $partialDefault = file_get_contents($templatesPath . 'DefaultPartial.html');
        $partialDefault = str_replace('{%%Identifier%%}', $identifier, $partialDefault);

        // Ensure the Partials directory exists.
        if (!is_dir($path . "/ContentBlocks/$identifier/Partials")) {
            if (!mkdir($path . "/ContentBlocks/$identifier/Partials", 0777, true)) {
                throw new FileCreaterException('Failed to create Partials directory.');
            }
        }

        file_put_contents($path . "/ContentBlocks/$identifier/Partials/DefaultPartial.html", $partialDefault);
    }

    /**
     * Creates a default layout for the given path and identifier.
     *
     * @param string $path The path where the layout will be created.
     * @param string $identifier Unique identifier for the layout.
     *
     * @throws FileCreaterException If directory creation fails.
     */
    public static function makeDefaultLayout(string $path, string $identifier): void
    {
        $templatesPath = __DIR__ . '/../../Templates/';
        $layoutDefault = file_get_contents($templatesPath . 'layoutDefault.html');

        // Ensure the Layouts directory exists.
        if (!is_dir($path . "/ContentBlocks/$identifier/Layouts")) {
            if (!mkdir($path . "/ContentBlocks/$identifier/Layouts", 0777, true)) {
                throw new FileCreaterException('Failed to create Layouts directory.');
            }
        }

        file_put_contents($path . "/ContentBlocks/$identifier/Layouts/Default.html", $layoutDefault);
    }

    /**
     * Creates a fields.yaml file for the given path, identifier, name, description, placeAt, position, group, and include settings.
     *
     * @param string $path The path where the fields.yaml will be created.
     * @param string $identifier Unique identifier for the fields.
     * @param string $name The name of the fields configuration.
     * @param string $desc The description of the fields configuration.
     * @param string $placeAt The placeAt setting for the fields.
     * @param string $position The position setting for the fields.
     * @param string $group The group setting for the fields.
     * @param string $include Whether to include basic fields ('yes' or 'no').
     */
    public static function makeFieldsYaml(string $path, string $identifier, string $name, string $desc, string $placeAt, string $position, string $group, string $include): void
    {
        $fields = file_get_contents(__DIR__ . '/../../Templates/fields.yaml');

        // Prepare the fields template with the given settings.
        $fields = str_replace(
            '{%%name%%}', $name, str_replace(
                '{%%identifier%%}', $identifier, str_replace(
                    '{%%desc%%}', $desc, str_replace(
                        '{%%group%%}', $group, str_replace(
                            '{%%placeAt%%}', $placeAt, str_replace(
                                '{%%position%%}', $position, $fields
                            )
                        )
                    )
                )
            )
        );

        // Determine the basic include settings based on the $include parameter.
        if ($include) {
            $basicInclude = "\n  - identifier: header\n    useExistingField: true\n    type: Text\n    required: true" .
                            "\n    description: The header for this element.\n  - identifier: linebreak\n    type: Linebreak" .
                            "\n  - identifier: bodytext" .
                            "\n    useExistingField: true\n    type: Textarea\n    enableRichtext: true" .
                            "\n    description: The main text area of this element.";
            $fields = str_replace('{%%basicInclude%%}', $basicInclude, $fields);
        } else {
            $fields = str_replace('{%%basicInclude%%}', '', $fields);
        }

        // Save the fields.yaml file.
        file_put_contents($path . "/ContentBlocks/$identifier/fields.yaml", $fields);
    }

    /**
     * Ensures the existence of the classesMap.yaml file for the given identifier.
     *
     * @param string $identifier Unique identifier (not used in this function).
     */
    public static function makeClassesMapYaml(string $identifier): void
    {
        $file = __DIR__ . "/../../Configuration/";
        $filesystem = new Filesystem();

        // Create the Configuration directory if it does not exist.
        if (!$filesystem->exists($file)) {
            $filesystem->mkdir($file);
        }

        $file .= "classesMap.yaml";

        // Create the classesMap.yaml file if it does not exist.
        if (!$filesystem->exists($file)) {
            $content = "# Do not touch this file\n";
            file_put_contents($file, $content);
        }
    }

    /**
     * Creates or updates the ext_localconf.php file at the specified path.
     *
     * @param string $path The path where the ext_localconf.php file should be created or updated.
     */
    public static function makeCbExtLocalConf(): void
    {
        $path = __DIR__ . '/../../ext_localconf.php';
        $filesystem = new Filesystem();

        $extLocalConfContent = <<<'EOT'
            = [
            'nodeName' => 'attributePalette',
            'priority' => 40,
            'class' => DS\CbBuilder\Form\Element\AttributePaletteElement::class,
        ];
        
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] =
            DS\CbBuilder\Hook\AttributePaletteHook::class;
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] =
            DS\CbBuilder\Hook\AttributePaletteHook::class;
        EOT;
        $extLocalConfContent = "\$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][" . time() . "]" . $extLocalConfContent;

        // Check if the file exists at the specified path.
        if (!$filesystem->exists($path)) {
            // Generate the content and wrap it with comments.
            $content = Wrapper::wrap(
                "call_user_func(function () {\n" . $extLocalConfContent . "\n});\n",
                '//',
                'EXT_LOCALCONF',
                '§%!&(&?%§',
                '§%!&)&?%§',
                'Do not modify this part of the code unless you want to uninstall the entire extension.'
            );
            // Create the file with the generated content.
            $filesystem->dumpFile($path, "<?php\n\n" . $content);
        } elseif (Wrapper::getWrapBoundary($filesystem->readFile($path), 'EXT_LOCALCONF', '§%!&(&?%§', '§%!&)&?%§') === false) {
            // If the file exists but lacks the necessary wrapper, inject the content.
            Wrapper::inject(
                $path,
                "call_user_func(function () {\n" . $extLocalConfContent . "\n});\n",
                true,
                '//',
                'EXT_LOCALCONF',
                '§%!&(&?%§',
                '§%!&)&?%§',
                'Do not modify this part of the code unless you want to uninstall the entire extension.'
            );
        }
    }

    /**
     * Check if the Configuration dir in the CbBuilder extension exists and creates it if not.
     * 
     * @return string The path to the Configuration folder
     */
    public static function makeLocalConfigurationDir(): string
    {
        $path = __DIR__ . '/../../Configuration';
        $filesystem = new Filesystem();
        if (!$filesystem->exists($path)) {
            $filesystem->mkdir($path);
        }
        return $path;
    }

    /**
     * Checks if the file Configuration/Icons.php exists and creates it if not.
     * 
     * @param string The standard content of the file before the icons array.
     * @return string The path to the Configuration/Icons.php file
     */
    public static function makeIconsPhp(string $stdContent): string
    {
        $iconsPath = self::makeLocalConfigurationDir() . '/Icons.php';
        $filesystem = new Filesystem();
        if (!$filesystem->exists($iconsPath)) {
            $filesystem->touch($iconsPath);
            $filesystem->dumpFile (
                $iconsPath,
                $stdContent . "return [];"
            );
        }
        return $iconsPath;
    }

    /**
     * Updates the file Configuration/Icons.php.
     * 
     * @param array $array The icons array to process and write.
     * @param string The standard content of the file before the icons array.
     */
    public static function updateIconsPhp(array $array, string $stdContent): void
    {
        //Nasty coding to bypass ArrayParser's lack of handling something like BitmapIconProvider::class
        $handleClassDeclarations = function (string &$processedArray) {
            $index = 0;
            $bitmapStrLen = strlen('"BitmapIconProvider::class"');
            while (($index = strpos($processedArray, '"BitmapIconProvider::class"', $index)) > 0) {
                $processedArray =
                    substr($processedArray, 0, $index) .
                    "BitmapIconProvider::class" .
                    substr($processedArray, $index+$bitmapStrLen);
            }

            $index = 0;
            $bitmapStrLen = strlen('"SvgIconProvider::class"');
            while (($index = strpos($processedArray, '"SvgIconProvider::class"', $index)) > 0) {
                $processedArray =
                    substr($processedArray, 0, $index) .
                    "SvgIconProvider::class" .
                    substr($processedArray, $index+$bitmapStrLen);
            }
        };
        $iconsPath = self::makeLocalConfigurationDir() . '/Icons.php';
        $filesystem = new Filesystem();
        $processedArray = ArrayParser::arrayToString($array);

        
        $handleClassDeclarations($processedArray);

        $filesystem->dumpFile (
            $iconsPath,
            $stdContent . "return $processedArray;"
        );
    }

    /**
     * Creates the symlink ExtensionName/Public/Resource/cb/ContentBlockIdentifier/assets pointing to
     *  ExtensionName/ContentBlocks/ContentBlockIdentifier/assets
     * 
     * @param string $identifier The identifier of the content block.
     */
    public static function createIconSymlink(string $identifier): void
    {
        $filesystem = new Filesystem();
        FileCreater::makePublicContentBlockFolder($identifier);
        
        $cbPath = CbBuilderConfig::getContentBlocksPath($identifier) . "/$identifier";
        $absPubPath = CbBuilderConfig::getExtensionPublicPath($identifier);
        $absPubIdentifierPath = $absPubPath . "/cb/$identifier";
        $cbRelPath = $filesystem->makePathRelative($cbPath, $absPubIdentifierPath);
        $cbRelAssetPath = $cbRelPath . 'Icon';
        Utility::createIfNot($absPubIdentifierPath);
        $absPubAssetPath = $absPubIdentifierPath . '/Icons';
        $filesystem->symlink($cbRelAssetPath, $absPubAssetPath);
    }

    /**
     * Adds an icon to the file Configuration/Icons.php.
     *
     * @param string $identifier The unique identifier of the icon.
     */
    public static function addIcon(string $identifier): void
    {
        $stdContent = "<?php\n\ndeclare(strict_types=1);\n\n" .
            "use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;\n\n" .
            "use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;\n\n";
        $iconsPhpPath = self::makeIconsPhp($stdContent);
        $parsedFile = ArrayParser::extractArraysFromFile($iconsPhpPath);
        if (is_array($parsedFile) && isset($parsedFile[0]) && is_array($parsedFile[0])) {
            $parsedFile = $parsedFile[0];
        } else {
            throw new ParseException (
                "Error: Could not parse 'EXT:CbBuilder/Configuration/Icons.php.\n" .
                "Tried in function 'addIcon' with method: 'ArrayParser::extractArraysFromFile($iconsPhpPath);'"
            );
        }
        $iconPath = CbBuilderConfig::getContentBlockPath() . '/Icon';
        $iconExtPath = CbBuilderConfig::getExtPublicCbContentBlockPath($identifier) . '/Icons';
        if (Utility::createIfNot($iconPath)) {
            $filesystem = new Filesystem();
            $filesystem->copy(__DIR__ . '/../../Templates/cb_icon.jpg', $iconPath . '/cb_icon.jpg');
            self::createIconSymlink($identifier);
        }

        $dirIterator = new DirectoryIterator($iconPath);
        $isBitmap = true;
        $counter = 0;
        $fileName = '';
        while ($dirIterator->valid()) {
            $counter += $dirIterator->getType() === 'file' ? 1 : 0;
            if ($dirIterator->getType() === 'file') {
                $fileName = $dirIterator->getFilename();
            }
            $dirIterator->next();
        }
        
        if ($counter > 1) {
            throw new FileException (
                "Error: Directory '$iconPath' must contain exactly one file.\n" .
                "$counter files detected."
            );
        } elseif ($counter === 1) {
            $splittedFilename = explode('.', $fileName);
            if ($splittedFilename !== [] && count($splittedFilename) > 1) {
                $splittedFilename = array_reverse($splittedFilename);
                $isBitmap = strtolower($splittedFilename[0]) !== 'svg';
            } else {
                throw new InvalidArgumentException (
                    "Error in icon path for the CType. Please check the path and filename of the icon file in " .
                    "content block '$identifier'."
                );
            }
            $identifier .= '_icon';
            $parsedFile[$identifier] = [
                'provider' => $isBitmap ? 'BitmapIconProvider::class' : 'SvgIconProvider::class',
                'source' => $iconExtPath . '/' . $fileName
            ];
            self::updateIconsPhp($parsedFile, $stdContent);
        }
    }

    const FILES_MAP_PATH = __DIR__ . '/../../Configuration/filesMap.yaml';

    public static function getFieldsMap(string $identifier): array
    {
        $filesystem = new Filesystem();
        if ($filesystem->exists(self::FILES_MAP_PATH)) {
            $parsedYaml = Yaml::parseFile(self::FILES_MAP_PATH);
            if (isset($parsedYaml[$identifier]) && is_array($parsedYaml[$identifier])) {
                return $parsedYaml[$identifier];
            } else {
                return [];
            }
        } else {
            return [];
        }
    }

    /**
     * Adds a file mapping to the file filesMap.yaml that contains all defined file fields of a content block.
     * Those will be used by the data processor to provide files information to the frontend.
     * 
     * @param string $identifier The identifier of the content block
     * @param array $fileFields All file fields of the content block, may be a multidimensional array
     *  if there are nested tables defined.
     */
    public static function addFileFieldsToMap(array $fileFields): void
    {
        //Create the file if it does not exist already
        Utility::createIfNot(self::FILES_MAP_PATH, false);

        $identifier = CbBuilderConfig::getIdentifier();
        $parsedYaml = Yaml::parseFile(self::FILES_MAP_PATH);
        $parsedYaml[$identifier] = $fileFields;
        $filesystem = new Filesystem();
        $filesystem->dumpFile(self::FILES_MAP_PATH, Yaml::dump($parsedYaml, PHP_INT_MAX, 2));
    }
}

?>