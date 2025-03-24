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

namespace DS\CbBuilder\Wrapper;

use DS\CbBuilder\Collector\Collector;
use DS\CbBuilder\Config\CbBuilderConfig;
use DS\CbBuilder\Utility\Utility;
use Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

/**
 * Custom exception class for wrapper-related issues.
 */
class WrapperException extends Exception {}

/**
 * Class for handling content block wrapping and token management.
 */
final class Wrapper
{
    /**
     * List of files where tokens might be found.
     */
    const TOKEN_FILE_LIST = [
        '/ext_localconf.php',
        '/Configuration/page.tsconfig',
        '/Configuration/TypoScript/setup.typoscript',
        '/Configuration/TCA/Overrides/tt_content.php'
    ];

    /**
     * Changes the content block wrap tokens in specified files.
     *
     * @param array $contentBlock Content block information.
     * @param string $startToken New start token.
     * @param string $endToken New end token.
     * @param string $oldStartToken Old start token.
     * @param string $oldEndToken Old end token.
     */
    private static function _changeContentBlockWrapTokens(
        array $contentBlock,
        string $startToken,
        string $endToken,
        string $oldStartToken,
        string $oldEndToken
    ): void
    {
        $path = $contentBlock['path'];
        foreach (Wrapper::TOKEN_FILE_LIST as $file) {
            if (file_exists($path . $file)) {
                $fileContent = file_get_contents($path . $file);
                $fileContent = str_replace($oldStartToken, $startToken, str_replace($oldEndToken, $endToken, $fileContent));
                file_put_contents($path . $file, $fileContent);
            }
        }
    }

    /**
     * Changes the wrap tokens globally for all content blocks.
     *
     * @param string $startToken New start token.
     * @param string $endToken New end token.
     */
    public static function changeWrapTokens(string $startToken, string $endToken): void
    {
        $identifier = CbBuilderConfig::getIdentifier();
        $path = CbBuilderConfig::getContentBlocksPath($identifier);
        $cbConfig = Yaml::parseFile("$path/$identifier/cbConfig.yaml");
        $oldStartToken = $cbConfig['config']['Tokens']['identifierOpeningToken'];
        $oldEndToken = $cbConfig['config']['Tokens']['identifierClosingToken'];
        $cbConfig['config']['Tokens']['identifierOpeningToken'] = $startToken;
        $cbConfig['config']['Tokens']['identifierClosingToken'] = $endToken;
        $contentBlocks = Collector::collectContentBlocks(false);
        foreach ($contentBlocks as $contentBlock) {
            Wrapper::_changeContentBlockWrapTokens($contentBlock, $startToken, $endToken, $oldStartToken, $oldEndToken);
        }
    }

    /**
     * Retrieves the current wrap tokens from the configuration.
     *
     * @return array Tokens configuration.
     */
    public static function getWrapTokens(): array
    {
        $identifier = CbBuilderConfig::getIdentifier();
        $path = CbBuilderConfig::getContentBlocksPath($identifier);
        return Yaml::parseFile("$path/$identifier/cbConfig.yaml")['config']['Tokens'];
    }

    /**
     * Finds the boundary of a wrapped content block in a given content string.
     *
     * @param string $content Content to search in.
     * @param string|null $identifier Optional identifier, defaults to the global identifier.
     * @param string|null $startToken Optional start token, defaults to the configured start token.
     * @param string|null $endToken Optional end token, defaults to the configured end token.
     *
     * @return array|bool Boundary information or false if not found.
     */
    public static function getWrapBoundary(
        string $content,
        ?string $identifier = null,
        ?string $startToken = null,
        ?string $endToken = null
    ): array|bool {
        $identifier = $identifier ?? CbBuilderConfig::getIdentifier();
        $tokens = Wrapper::getWrapTokens();
        $startToken = $startToken ?? $tokens['identifierOpeningToken'];
        $endToken = $endToken ?? $tokens['identifierClosingToken'];
        $start = strpos($content, $startToken . $identifier . $endToken);
        if (!is_int($start)) {
            return false;
        }
        $start = Utility::stringSafeStrposBackward($content, "\n", $start) + 1;
        if (!is_int($start)) {
            return false;
        }
        $startNewline = strpos($content, "\n", $start);
        if (!is_int($startNewline)) {
            return false;
        }
        if ($startNewline === false) {
            throw new WrapperException(
                "Content block area not found in a file of content block '$identifier'. " .
                "Restore from a backup or run 'cb:repair --tt_content' in the CLI to restore " .
                "the default settings and then run 'cb:update'. Custom settings may be lost."
            );
        }
        $end = strpos($content, $startToken . $identifier . $endToken, $startNewline);
        if (!is_int($end)) {
            return false;
        }
        $end = Utility::stringSafeStrposBackward($content, "\n", $end) + 1;
        if (!is_int($end)) {
            return false;
        }
        $endNewline = strpos($content, "\n", $end);
        if (!is_int($endNewline)) {
            $endNewline = strlen($content);
        }
        return ['start' => $start, 'startNewline' => $startNewline, 'end' => $end, 'endNewline' => $endNewline];
    }

    /**
     * Wraps content with specified tokens and comments.
     *
     * @param string $content Content to wrap.
     * @param string $commentSyntax Comment syntax to use (e.g., '//', '#', etc.).
     * @param string|null $identifier Optional identifier, defaults to the global identifier.
     * @param string|null $startToken Optional start token, defaults to the configured start token.
     * @param string|null $endToken Optional end token, defaults to the configured end token.
     * @param string $startText Text to include after the start token.
     *
     * @return string Wrapped content.
     */
    public static function wrap(
        string $content,
        string $commentSyntax = '//',
        ?string $identifier = null,
        ?string $startToken = null,
        ?string $endToken = null,
        string $startText = "NEVER DELETE THIS STARTING AND THE ENDING COMMENT - NEVER USE THE TOKEN CHARACTER SETS IN THAT ORDER!"
    ): string {
        $identifier = $identifier ?? CbBuilderConfig::getIdentifier();
        $tokens = Wrapper::getWrapTokens();
        $startToken = $startToken ?? $tokens['identifierOpeningToken'];
        $endToken = $endToken ?? $tokens['identifierClosingToken'];
        return $commentSyntax . $startToken . $identifier . $endToken . ' ' . $startText . "\n" . $content . "\n" .
               $commentSyntax . $startToken . $identifier . $endToken;
    }

    /**
     * Extracts the content within the wrapped boundary.
     *
     * @param string $content Content to extract from.
     * @param string|null $identifier Optional identifier, defaults to the global identifier.
     * @param string|null $startToken Optional start token, defaults to the configured start token.
     * @param string|null $endToken Optional end token, defaults to the configured end token.
     *
     * @return string Extracted content.
     */
    public static function extract(string $content, ?string $identifier = null, ?string $startToken = null, ?string $endToken = null): string
    {
        $identifier = $identifier ?? CbBuilderConfig::getIdentifier();
        $boundary = Wrapper::getWrapBoundary($content, $identifier, $startToken, $endToken);
        if ($boundary === false) {
            return $content;
        }
        return substr($content, $boundary['startNewline'], ($boundary['end'] - $boundary['startNewline']));
    }

    /**
     * Erases the wrapped content block from the given content.
     *
     * @param string $content Content to erase from.
     * @param bool $isFile Whether the content is a file path.
     * @param string|null $identifier Optional identifier, defaults to the global identifier.
     * @param string|null $startToken Optional start token, defaults to the configured start token.
     * @param string|null $endToken Optional end token, defaults to the configured end token.
     *
     * @return string Modified content.
     */
    public static function erase(
        string $content,
        bool $isFile = false,
        ?string $identifier = null,
        ?string $startToken = null,
        ?string $endToken = null
    ): string {
        $identifier = $identifier ?? CbBuilderConfig::getIdentifier();
        $filesystem = new Filesystem();
        $file = '';
        if ($isFile && $filesystem->exists($content)) {
            $file = $content;
            $content = $filesystem->readFile($content);
        } elseif ($isFile && !$filesystem->exists($content)) {
            return '';
        }

        $boundary = Wrapper::getWrapBoundary($content, $identifier, $startToken, $endToken);
        if ($boundary === false) {
            return $content;
        }
        $content = substr($content, 0, $boundary['start']) . substr($content, $boundary['endNewline']);
        if ($isFile && $filesystem->exists($file)) {
            $filesystem->dumpFile($file, $content);
        }
        return $content;
    }

    /**
     * Injects new content into the wrapped boundary.
     *
     * @param string $content Content to inject into.
     * @param string $needle Content to inject.
     * @param bool $isFile Whether the content is a file path.
     * @param string $commentSyntax Comment syntax to use (e.g., '//', '#', etc.).
     * @param string|null $identifier Optional identifier, defaults to the global identifier.
     * @param string|null $startToken Optional start token, defaults to the configured start token.
     * @param string|null $endToken Optional end token, defaults to the configured end token.
     * @param string $startText Text to include after the start token.
     *
     * @return string|bool Injected content or true if successful file operation, false otherwise.
     */
    public static function inject(
        string $content,
        string $needle,
        bool $isFile = false,
        string $commentSyntax = '//',
        ?string $identifier = null,
        ?string $startToken = null,
        ?string $endToken = null,
        string $startText = "NEVER DELETE THIS STARTING AND THE ENDING COMMENT - NEVER USE THE TOKEN CHARACTER SETS IN THAT ORDER!"
    ): string|bool {
        $identifier = $identifier ?? CbBuilderConfig::getIdentifier();
        $filesystem = new Filesystem();
        $path = $before = $after = '';
        if ($isFile === true) {
            $path = $content;
            if ($filesystem->exists($content)) {
                $content = file_get_contents($content);
                if ($content === false) {
                    return false;
                }
            }
        }
        $endsWithNewline = Utility::endsWithNewline($content);
        $boundary = Wrapper::getWrapBoundary($content, $identifier, $startToken, $endToken);
        if ($boundary === false) {
            $content .= $endsWithNewline
                ? Wrapper::wrap($needle, $commentSyntax, $identifier, $startToken, $endToken, $startText)
                : "\n" . Wrapper::wrap($needle, $commentSyntax, $identifier, $startToken, $endToken, $startText);
        } else {
            $before = $endsWithNewline
                ? substr($content, 0, $boundary['start'])
                : substr($content, 0, $boundary['start']) . "\n";
            $after = $endsWithNewline
                ? substr($content, $boundary['endNewline'])
                : substr($content, $boundary['endNewline']) . "\n";
            $content = $before . Wrapper::wrap($needle, $commentSyntax, $identifier, $startToken, $endToken, $startText) . $after;
        }
        if ($isFile === true) {
            if (file_put_contents($path, $content) === false) {
                return false;
            }
        }
        return $isFile === true ? true : $before .
            Wrapper::wrap($needle, $commentSyntax, $identifier, $startToken, $endToken, $startText) . $after;
    }
}