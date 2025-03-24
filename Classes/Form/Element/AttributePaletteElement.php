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

namespace DS\CbBuilder\Form\Element;

use DirectoryIterator;
use DS\CbBuilder\Config\CbBuilderConfig;
use DS\CbBuilder\Utility\SimpleDatabaseQuery;
use DS\CbBuilder\Utility\SimpleDatabaseQueryException;
use DS\CbBuilder\Utility\Utility;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Backend\Form\Element\SelectMultipleSideBySideElement;
use TYPO3\CMS\Backend\Form\Element\SelectSingleElement;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;


class AttributePaletteElement extends AbstractFormElement
{
    /**
     * Process the classes defined for fields in the fields.yaml recursively.
     *
     * This function processes an array of classes for different content blocks and fields.
     * It returns an array of all classes in a specific format, including dividers and class names.
     *
     * @param array $classes The array containing classes for content blocks and fields.
     * @param string $key The concatenated keys, formatted as "table(not tt_content):n tables:fieldIdentifier".
     * @return array An array of classes in the format: divider, table(not tt_content):n tables:fieldIdentifier:::className, ...
     */
    private function _processClasses(array $classes, string $key = ''): array
    {
        $processedClasses = [];

        // Add a divider for each field first
        $dividerAdded = false;
        foreach ($classes as $_key => $value) {
            if (is_array($value)) {
                // Recursively process nested arrays
                $processedClasses = array_merge($processedClasses, $this->_processClasses($value, "$key:$_key"));
            } else {
                // Add a divider if not already added
                if (!$dividerAdded) {
                    $processedClasses[] = Utility::trimAlwaysWhitespace($key, ':');
                    $dividerAdded = true;
                }
                // Append the class in the required format
                $processedClasses[] = Utility::trimAlwaysWhitespace($key, ':') . ":::$value";
            }
        }
        return $processedClasses;
    }


    /**
     * Render the content element palette with the current settings defined in the cbConfig.yaml for each content block.
     * Returns an empty array if neither presets nor classes are enabled.
     *
     * @return array An array containing the HTML code of the palette and elements, along with JavaScript modules.
     */
    public function render(): array
    {
        $row = $this->data['databaseRow'];
        $isNew = is_string($row['uid']);
        
        $cbNextUid = SimpleDatabaseQuery::getNextUniqueKey('cb_table');
        $cbEntry = [];
        $cbUid = 0;
        if ($cbNextUid <= 0) {
            throw new SimpleDatabaseQueryException (
                "Couldn't fetch the next available uid of table 'cb_table'.\n" .
                "Please proof that the table exists."
            );
        }
        
        if (!$isNew) {
            $sdq = new SimpleDatabaseQuery();
            $cbEntry = $sdq->fetch('cb_table', ['uid', 'preset', 'classes', 'tt_content_uid'], 'tt_content_uid==' . $row['uid']);
            if (isset($cbEntry[0])) {
                $cbEntry = $cbEntry[0];
            }
            if (isset($cbEntry['uid'])) {
                $cbUid = $cbEntry['uid'];
            } else {
                $cbUid = $cbNextUid;
            }
        } else {
            $cbUid = $cbNextUid;
        }

        $parameterArray = $this->data['parameterArray'];
        $fieldInformationResult = $this->renderFieldInformation();
        $fieldInformationHtml = $fieldInformationResult['html'];
        $resultArray = $this->mergeChildReturnIntoExistingResult($this->initializeResultArray(), $fieldInformationResult, false);

        $fieldId = StringUtility::getUniqueId('formengine-textarea-');

        $attributes = [
            'id' => $fieldId,
            'name' => htmlspecialchars($parameterArray['itemFormElName']),
            'data-formengine-input-name' => htmlspecialchars($parameterArray['itemFormElName']),
        ];

        $attributes['placeholder'] = 'Enter special value for user dennis.';
        $classes = [
            'form-control',
            't3js-formengine-textarea',
            'formengine-textarea',
            'hidden'
        ];
        $attributes['class'] = implode(' ', $classes);

        $cbPath = '';
        $identifier = $this->data['recordTypeValue'];
        
        $contentBlocks = CbBuilderConfig::loadGlobalConfig();
        
        
        if (isset($contentBlocks[$identifier])) {
            if (isset($contentBlocks[$identifier]['path']))
            $cbPath = $contentBlocks[$identifier]['path'] . "/ContentBlocks/$identifier";
        }

        $localSettings = CbBuilderConfig::loadLocalConfig($identifier);

        $filesystem = new Filesystem();

        $renderedPresetSelector = [];
        $renderedClassSelector = [];

        $renderedPresetSelector['html'] = '';
        $renderedClassSelector['html'] = '';
        $renderedPresetSelector['javaScriptModules'] = [];
        $renderedClassSelector['javaScriptModules'] = [];

        if (is_array($localSettings) && isset($localSettings['config'])) {
            if (isset($localSettings['config']['usePartials']) && $localSettings['config']['usePartials'] === true) {
                $presetFiles = [];
                $foundDefaultPreset = false;
                if ($filesystem->exists($cbPath . "/Partials")) {
                    $dir = new DirectoryIterator($cbPath . "/Partials");
                    foreach ($dir as $file) {
                        if ($file->isDot()) continue;
                        if ($file->isFile()) {
                            $fileInfo = GeneralUtility::revExplode('.', $file->getFilename(), 2);
                            if (is_array($fileInfo) && count($fileInfo) === 2 && strtolower($fileInfo[1]) === 'html') {
                                $oldFull = $file->getPathname();
                                $oldName = $file->getFilename();
                                $newName = strtoupper($oldName[0]) . substr($oldName, 1);
                                $newFull = $file->getPath() . '/' . $newName;
                                if ($oldFull !== $newFull) {
                                    $filesystem->rename($oldFull, $newFull);
                                }
                                $preset = GeneralUtility::revExplode('.', $newName, 2)[0];
                                if ($preset === 'DefaultPartial') {
                                    $foundDefaultPreset = true;
                                } else {
                                    $presetFiles[] = ['label' => $preset, 'value' => $preset];
                                }
                            }
                        }
                    }
                }
        
                if (!$foundDefaultPreset) {
                    $filesystem->copy(__DIR__ . "/../../../Templates/DefaultPartial.html", $cbPath . "/Partials/DefaultPartial.html");
                    $content = str_replace("{%%Identifier%%}", $identifier, $filesystem->readFile($cbPath . "/Partials/DefaultPartial.html"));
                    $filesystem->dumpFile($cbPath . "/Partials/DefaultPartial.html", $content);
                }

                array_unshift($presetFiles, ['label' => "DefaultPartial", 'value' => "DefaultPartial"]);

                $presetSelector = GeneralUtility::makeInstance(SelectSingleElement::class);
                $presetSelectorData = $this->data;
                $presetSelectorData['fieldName'] = 'cb_preset';
                $presetSelectorData['tableName'] = 'cb_table';
                $presetSelectorData['renderType'] = 'selectSingle';
                $presetSelectorData['elementBaseName'] = "data[tt_content][" . $row['uid'] . "][cb_preset]";
                $presetSelectorData['parameterArray']['fieldConf'] = [
                    'label' => "Preset selector",
                    "description" => "Select a partial for the output",
                    'config' => [
                        'type' => 'select',
                        'renderType' => 'selectSingle',
                        'items' => $presetFiles,
                    ]
                ];
                $presetSelectorData['parameterArray']['itemFormElName'] = "data[tt_content][" . $row['uid'] . "][cb_preset]";
                if (isset($cbEntry['preset'])) {
                    $presetSelectorData['parameterArray']['itemFormElValue'] = $cbEntry['preset'];
                }
        
                $presetSelectorData['processedTca']['columns']['cb_preset'] = [
                    'label' => "Preset selector",
                    "description" => "Select a partial for the output",
                    'config' => [
                        'type' => 'select',
                        'renderType' => 'selectSingle',
                        'items' => $presetFiles,
                    ]
                ];
        
                $presetSelector->setData($presetSelectorData);
                $renderedPresetSelector = $presetSelector->render();
            }
            if (isset($localSettings['config']['useClasses']) && $localSettings['config']['useClasses'] === true) {
                $classMapFile = __DIR__ . "/../../../Configuration/classesMap.yaml";
                $classSelector = GeneralUtility::makeInstance(SelectMultipleSideBySideElement::class);
                $classSelectorData = $this->data;
                $classSelectorData['fieldName'] = 'cb_classes';
                $classSelectorData['tableName'] = 'cb_table';
                $classSelectorData['renderType'] = 'selectMultipleSideBySide';
                $classSelectorData['elementBaseName'] = "data[tt_content][" . $row['uid'] . "][cb_classes]";
                $processedItems = [];
                if ($filesystem->exists($classMapFile)) {
                    $rawItems = Yaml::parseFile($classMapFile);
                    if (isset($rawItems[$identifier])) {
                        $rawItems = $rawItems[$identifier];
                        
                        $items = $this->_processClasses($rawItems);
                        foreach ($items as $item) {
                            if (!str_contains($item, ':::')) {
                                $processedItems[] = [
                                    "label" => '------ ' . $item . ' element ------',
                                    "value" => '--div--'
                                ];
                            } else {
                                $keyValPair = GeneralUtility::trimExplode(':::', $item);
                                $processedItems[] = [
                                    "label" => $keyValPair[1],
                                    "value" => $item
                                ];
                            }
                        }
                    }
                }
        
                $classSelectorData['parameterArray']['fieldConf'] = [
                    'label' => "Class selector",
                    "description" => "Select classes for elements",
                    'config' => [
                        'type' => 'select',
                        'renderType' => 'selectMultipleSideBySide',
                        'items' => $processedItems,
                        'maxitems' => PHP_INT_MAX
                    ]
                ];
        
                $classSelectorData['parameterArray']['itemFormElName'] = "data[tt_content][" . $row['uid'] . "][cb_classes]";
                if (isset($cbEntry['classes'])) {
                    $classSelectorData['parameterArray']['itemFormElValue'] = GeneralUtility::trimExplode(',', $cbEntry['classes']);
                    if (!is_array($classSelectorData['parameterArray']['itemFormElValue'])) {
                        $classSelectorData['parameterArray']['itemFormElValue'] = [$classSelectorData['parameterArray']['itemFormElValue']];
                    }
                }
        
                $classSelectorData['processedTca']['columns']['cb_classes'] = [
                    'label' => "Class selector",
                    "description" => "Select classes for elements",
                    'config' => [
                        'type' => 'select',
                        'renderType' => 'selectMultipleSideBySide',
                        'items' => $processedItems,
                        'maxitems' => PHP_INT_MAX
                    ]
                ];
        
                $classSelector->setData($classSelectorData);
                $renderedClassSelector = $classSelector->render();
            }
        }

        if (
            (
                isset($localSettings['config']['usePartials'])
                && $localSettings['config']['usePartials'] === true
            ) || (
                isset($localSettings['config']['useClasses'])
                && $localSettings['config']['useClasses'] === true
            )
        ) {
            $html = [];
            $html[] = $this->renderLabel($fieldId);
            $html[] = '<div class="formengine-field-item t3js-formengine-field-item" style="padding: 5px;">';
            $html[] = $fieldInformationHtml;
            $html[] =   '<div class="form-wizards-wrap">';
            $html[] =      '<div class="form-wizards-element">';
            $html[] =         '<div class="form-control-wrap">';
            $html[] =         '<BR />';
            $html[] =            '<input type="text" value="' . $cbUid . '" ';
            $html[] =               GeneralUtility::implodeAttributes($attributes, true);
            $html[] =            ' />';
            $html[] =           $renderedPresetSelector['html'];
            $html[] =         '<BR />';
            $html[] =           $renderedClassSelector['html'];
            $html[] =         '</div>';
            $html[] =      '</div>';
            $html[] =   '</div>';
            $html[] = '</div>';
            $resultArray['html'] = implode(LF, $html);
            $resultArray['javaScriptModules'] = array_merge_recursive (
                $resultArray['javaScriptModules'],
                $renderedPresetSelector['javaScriptModules'],
                $renderedClassSelector['javaScriptModules']
            );
            return $resultArray;
        }
        return [];
    }
}