<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'MySQL Dashboard Widget',
    'description' => 'Add DashBoard widgets to show MySQL status',
    'category' => 'plugin',
    'author' => 'Stefan Froemken',
    'author_email' => 'froemken@gmail.com',
    'author_company' => '',
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.1',
    'constraints' => [
        'depends' => [
            'php' => '8.1.0-8.3.99',
            'typo3' => '12.4.17-13.4.99',
            'dashboard' => '12.4.17-13.4.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
