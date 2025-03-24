<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'cb_builder',
    'description' => 'Kickstart content blocks via the console and add fields using YAML or corresponding PHP files. This approach focuses on reusability for content blocks and includes handy PHP classes and FLUID helpers.',
    'constraints' => [
        'depends' => [
            'typo3' => '13.0.0-13.4.99',
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'DS\\CbBuilder\\' => 'Classes/',
        ],
    ],
];
