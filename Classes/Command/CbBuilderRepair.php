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



/**
 * NOT IMPLEMENTED YET!
 */

namespace DS\CbBuilder\Command;


use DS\CbBuilder\Config\CbBuilderConfig;
use DS\CbBuilder\FieldBuilder\FieldBuilder;
use DS\CbBuilder\FileCreater\FileCreater;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'cb:repair',
    description: 'Try to repair a content block.'
)]
class CbBuilderRepair extends Command
{
    const REPAIR_OPTIONS = [
        'tt_content'
    ];

    protected function configure(): void
    {
        $this->setDefinition (
            new InputDefinition([
                new InputOption('tt_content', 't', InputOption::VALUE_REQUIRED)
            ])
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $options = $input->getOptions();
        $globalConf = CbBuilderConfig::loadGlobalConfig();
        foreach ($options as $option => $contentBlock) {
            switch ($option) {
                case 'tt_content':
                    if (!array_key_exists($contentBlock, $globalConf)) {
                        throw new Exception (
                            "Content block '$contentBlock' could not be found. Check for typos, the existence of the folders and files and " .
                            "an entry in the 'CbBuilder/Configuration/ContentBlocks.yaml' file."
                        );
                    }
                    
                    FileCreater::makeTtContent (
                        $globalConf[$contentBlock]['path'],
                        $globalConf[$contentBlock]['identifier'],
                        $globalConf[$contentBlock]['name'],
                        $globalConf[$contentBlock]['description'],
                        $globalConf[$contentBlock]['placeAt'],
                        $globalConf[$contentBlock]['position'],
                        $globalConf[$contentBlock]['group'],
                        'no'
                    );
                    break;
                
                default:
                    // skip
                    break;
            }
        }
        return Command::SUCCESS;
    }

}