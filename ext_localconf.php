<?php

if (!defined('TYPO3_MODE'))
  die('Access denied.');

// writes the base url into the default typoscript constants
$protocol = $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$address = str_replace(
    basename($_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME']),
    '',
    $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME']
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('config.baseURL = ' . $protocol . $address);