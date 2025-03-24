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

use DS\CbBuilder\BasicBuilder\BasicBuilder;
use DS\CbBuilder\SqlCreater\SqlCreater;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

#[AsCommand(
    name: 'cb:make',
    description: 'Kickstart a new content block.'
)]
class CbBuilderCommand extends Command
{
    protected function configure(): void
    {
        $this->setHelp('This command initializes a new content block.');
    }

    /**
     * Executes the command to create a new content block.
     *
     * @param InputInterface $input Input interface.
     * @param OutputInterface $output Output interface.
     * @return int Command execution status.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        // Prompt for the identifier of the new content block.
        $identifier = $io->ask('Enter an identifier for the new content block.', 'new_content_block', function ($identifier) {
            $matches = [];
            preg_match("/[\w]{2,}/", $identifier, $matches);
            if (!isset($matches[0]) || $matches[0] !== $identifier) {
                throw new Exception(
                    "Error: Invalid identifier. Only alphanumeric characters and '_' are allowed, with a minimum length of 2."
                );
            }

            $configurationPath = __DIR__ . "/../../Configuration/";

            $filesystem = new Filesystem();
            if (!$filesystem->exists($configurationPath)) {
                $filesystem->mkdir($configurationPath);
            }

            $yamlPath = $configurationPath . "contentBlocks.yaml";

            if (!$filesystem->exists($yamlPath)) {
                $filesystem->dumpFile($yamlPath, "#Do not touch this file!\ncontentBlocks:\n");
            }
            
            $contentBlocks = Yaml::parseFile($yamlPath);

            if (is_array($contentBlocks['contentBlocks']) && array_key_exists($identifier, $contentBlocks['contentBlocks'])) {
                throw new Exception(
                    "Error: A content block with that identifier already exists. Please choose a different identifier."
                );
            }
            return $identifier;
        });

        // Convert the identifier to a readable name.
        $splittedIdentifier = preg_split("/[_]{1}/", $identifier);
        $name = '';
        foreach ($splittedIdentifier as $value) {
            $name .= strtoupper($value[0]) . substr($value, 1) . ' ';
        }
        $name = trim($name);

        // Prompt for the name of the content block.
        $name = $io->ask('Enter the name for the new content block.', $name, function ($name) {
            $matches = [];
            preg_match("/[\w ]{2,}/", $name, $matches);
            if (!isset($matches[0]) || $matches[0] !== $name) {
                throw new Exception(
                    "Error: Invalid name. Only alphanumeric characters, '_' and ' ' are allowed, with a minimum length of 2."
                );
            }
            return $name;
        });

        // // Prompt for the namespace (vendor directory path).
        // $namespace = $io->ask('Enter the vendor directory path of your extensions.', 'packages', function ($namespace) {
        //     $matches = [];
        //     preg_match("/[a-zA-Z_][\w]+(\/)*([a-zA-Z_][\w\/]+)*/", $namespace, $matches);
        //     if (!isset($matches[0]) || $matches[0] !== $namespace) {
        //         throw new Exception(
        //             "Error: Invalid namespace. It should start with a letter or an underscore, followed by alphanumeric characters and '/' for subdirectories."
        //         );
        //     }
        //     return $namespace;
        // });

        // // List available extensions in the specified namespace.
        // $dir = dir(Environment::getProjectPath() . '/' . $namespace);
        // $extensions = [];
        // if ($dir) {
        //     while (false !== ($obj = $dir->read())) {
        //         if ($obj !== '.' && $obj !== '..') {
        //             $extensions[] = $obj;
        //         }
        //     }
        // }

        $installedExtensions = ExtensionManagementUtility::getLoadedExtensionListArray();
        $extensions = [];
        foreach ($installedExtensions as $extension) {
            if (!str_contains(realpath(ExtensionManagementUtility::extPath($extension)), Environment::getProjectPath() . '/vendor')) {
                $extensions[] = $extension;
            }
        }

        // Check if there are any extensions available.
        $extensionCount = count($extensions);
        if ($extensionCount <= 0) {
            throw new Exception('Error: No extensions are available. Please kickstart an extension first.');
        }

        // Prompt to choose an extension.
        $question = new ChoiceQuestion(
            'Please choose the extension to add the content block to.',
            $extensions,
            $extensions[0]
        );
        $extensionCount--;
        $question->setErrorMessage("Error: Please choose an extension within the range 0-$extensionCount");
        
        $extension = $io->askQuestion($question);

        // Set the path to the chosen extension.
        // $path = $dir->path . '/' . $extension;
        $extPath = ExtensionManagementUtility::extPath($extension);
        $path = realpath($extPath);
        $splittedPath = array_reverse(explode('/', $path));
        $extensionName = $splittedPath[0];
        // Check if a folder with the identifier already exists.
        if (is_dir($path . "/ContentBlocks/$identifier")) {
            throw new Exception("Error: A folder with that identifier already exists in the extension.");
        }

        // Prompt for a description of the content block.
        $desc = $io->ask('Please describe your content block.', "This is the content block $name");

        // Prompt for the CType where the content block will be placed.
        $placeAt = $io->ask('Choose an existing CType where to place the new content block.', 'textmedia');

        // Prompt for the position relative to the existing CType.
        $question = new ChoiceQuestion(
            'Do you want to place the new content block \'before\' or \'after\' the existing CType?',
            ['after', 'before'],
            0
        );
        $question->setErrorMessage("Error: Please choose between 'before' or 'after'");
        
        $position = $io->askQuestion($question);

        // Prompt for the group to which the CType will be added.
        $group = $io->ask('Which group shall the CType get added to?.', 'default');

        // Prompt to include default fields in the content block.
        $question = new ChoiceQuestion(
            'Do you wish to include a header and textarea to your content block by default?',
            ['yes', 'no'],
            0
        );
        $question->setErrorMessage("Error: Please choose between 'yes' or 'no'");
        
        $include = $io->askQuestion($question);

        // Create the content block structure.
        BasicBuilder::makeContentBlocksDir($path);
        BasicBuilder::makeContentBlocksIdentifierDir($path, $identifier);
        BasicBuilder::buildBasicStructure($path, $extensionName, $extension, $name, $identifier, $desc, $placeAt, $position, $group, $include);
        SqlCreater::createCbTable($identifier);
        return Command::SUCCESS;
    }
}
