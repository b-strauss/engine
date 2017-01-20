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
            'php' => '7.0.0',
            'typo3' => '8.4.0',
            'vhs' => '3.1.0',
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
    'version' => '1.0.0',
];