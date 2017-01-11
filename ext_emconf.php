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
            'php' => '5.6.0',
            'typo3' => '7.6.0-7.6.999',
            'vhs' => '2.4.0',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'state' => 'experimental',
    'uploadfolder' => false,
    'createDirs' => '',
    'clearCacheOnLoad' => true,
    'author' => 'Benjamin Strauß',
    'author_email' => '',
    'author_company' => '',
    'version' => '0.1.0',
];