<?php
/**
 * See {@link https://docs.typo3.org/typo3cms/CoreApiReference/ExtensionArchitecture/DeclarationFile/Index.html}
 * for a full documentation.
 */

$EM_CONF[$_EXTKEY] = [
    'title' => 'Engine',
    'description' => 'Typo3 extension utilities',
    'category' => 'misc',
    'constraints' => [
        'depends' => [
            'php' => '7.0.0-7.1.99',
            'typo3' => '8.5.0-8.5.99',
            'vhs' => '4.0.0',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'state' => 'stable',
    'uploadfolder' => false,
    'createDirs' => '',
    'clearCacheOnLoad' => true,
    'author' => 'Benjamin Strauß',
    'author_email' => 'benmastra@gmail.com',
    'author_company' => '',
    'version' => '1.0.1',
];