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

namespace DS\CbBuilder\Command;

use DS\CbBuilder\Collector\Collector;
use DS\CbBuilder\Config\CbBuilderConfig;
use DS\CbBuilder\FileDestroyer\FileDestroyer;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

#[AsCommand(
    name: 'cb:delete',
    description: 'Delete a content block.'
)]
class CbDeleteCommand extends Command
{
    /**
     * Removes a content block from the 'contentBlocks.yaml' list.
     *
     * @param string $identifier Identifier of the content block to remove.
     */
    private static function _removeContentBlockFromList(string $identifier): void
    {
        $yamlPath = __DIR__ . "/../../Configuration/contentBlocks.yaml";
        $list = Yaml::parseFile($yamlPath);
        if (isset($list['contentBlocks']) && isset($list['contentBlocks'][$identifier])) {
            unset($list['contentBlocks'][$identifier]);
            file_put_contents($yamlPath, Yaml::dump($list, PHP_INT_MAX, 2));
        }
    }

    /**
     * Deletes the folder of a content block.
     *
     * @param string $path Path to the content block's parent directory.
     * @param string $identifier Identifier of the content block.
     */
    private static function _deleteFolder(string $path, string $identifier): void
    {
        $path .= "/ContentBlocks/$identifier";
        if (is_dir($path)) {
            (new Filesystem())->remove($path);
        }
    }

    /**
     * Deletes a content block's entry from the 'classesMap.yaml' file.
     *
     * @param string $identifier Identifier of the content block.
     */
    private static function _deleteClassesMap(string $identifier): void
    {
        $file = __DIR__ . "/../../Configuration/classesMap.yaml";
        $filesystem = new Filesystem();
        if ($filesystem->exists($file)) {
            $map = Yaml::parseFile($file);
            unset($map[$identifier]);
            $filesystem->dumpFile($file, Yaml::dump($map, PHP_INT_MAX, 2));
        }
    }

    /**
     * Deletes a content block.
     *
     * @param array $contentBlock Content block configuration.
     * @throws Exception If the content block's path or identifier is missing.
     */
    private static function _deleteContentBlock(array $contentBlock): void
    {
        $path = $contentBlock['path'] ?? NULL;
        $identifier = $contentBlock['identifier'] ?? NULL;
        if ($path === NULL || $identifier === NULL) {
            throw new Exception(
                "Error: Content block's path or identifier is missing. Cannot delete the content block."
            );
        }
        self::_deleteFolder($path, $identifier);
        self::_removeContentBlockFromList($identifier);
        self::_deleteClassesMap($identifier);
    }

    protected function configure(): void
    {
        $this->setHelp('This command deletes a content block.');
    }

    /**
     * Executes the command to delete a content block.
     *
     * @param InputInterface $input Input interface.
     * @param OutputInterface $output Output interface.
     * @return int Command execution status.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $contentBlocks = Collector::collectContentBlocks();
        $question = new ChoiceQuestion(
            'Please select the content block to delete.',
            $contentBlocks,
            array_key_first($contentBlocks)
        );

        $contentBlocksCount = count($contentBlocks) - 1;

        $question->setErrorMessage("Error: Please choose a content block within the range 0-$contentBlocksCount");

        $contentBlock = $io->askQuestion($question);
        $contentBlock = $contentBlocks[$contentBlock];

        $question = new ChoiceQuestion(
            "Are you sure that you want to delete the content block $contentBlock?",
            ['no', 'yes'],
            0
        );
        $question->setErrorMessage("Error: Please choose 'yes' or 'no'.");

        $submit = $io->askQuestion($question);

        if ($submit === 'no') return Command::SUCCESS;

        $contentBlocks = Collector::collectContentBlocks(false);
        
        $contentBlock = explode(' -> ', $contentBlock);
        $name = $contentBlock[0];
        $identifier = $contentBlock[1];
        $contentBlock = array_filter($contentBlocks, function ($element) use ($name, $identifier) {
            return $name === $element['name'] && $identifier === $element['identifier'];
        });

        if (is_array($contentBlock) && count($contentBlock) === 1) {
            CbBuilderConfig::loadLocalConfig($identifier);
            FileDestroyer::clearFiles($identifier);
            self::_deleteContentBlock($contentBlock[$identifier]);
        } else {
            throw new Exception(
                "Error: No content block could be fetched. Please delete it manually. Refer to the FAQ for instructions."
            );
        }

        return Command::SUCCESS;
    }
}
