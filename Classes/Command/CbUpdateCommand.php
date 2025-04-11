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

use DS\CbBuilder\Config\CbBuilderConfig;
use DS\CbBuilder\FieldBuilder\FieldBuilder;
use DS\CbBuilder\FileCreater\FileCreater;
use DS\CbBuilder\SqlCreater\SqlCreater;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'cb:update',
    description: 'Update a content block.'
)]
class CbUpdateCommand extends Command
{

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $contentBlocks = CbBuilderConfig::loadGlobalConfig();
        
        foreach ($contentBlocks as $identifier => $contentBlock) {
            CbBuilderConfig::loadLocalConfig($identifier);
            FileCreater::makeCbExtLocalConf();
            FileCreater::updateCssAssets($identifier);
            FileCreater::updateJsAssets($identifier);
            SqlCreater::createCbTable($identifier);
            $GLOBALS['CbBuilder']['SqlCreater'] = new SqlCreater();
            $fieldBuilder = new FieldBuilder(CbBuilderConfig::getExtensionPath($identifier), $identifier);
            $fieldBuilder->buildFields();
            FileCreater::addIcon($identifier);
        }
        
        return Command::SUCCESS;
    }

}