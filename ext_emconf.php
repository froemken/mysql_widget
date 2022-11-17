<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'MySQL Widget',
    'description' => 'Add DashBoard widgets to show MySQL status',
    'category' => 'plugin',
    'author' => 'Stefan Froemken',
    'author_email' => 'froemken@gmail.com',
    'author_company' => '',
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'version' => '0.2.1',
    'constraints' => [
        'depends' => [
            'php' => '7.4.0-8.1.99',
            'typo3' => '10.4.32-11.5.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
