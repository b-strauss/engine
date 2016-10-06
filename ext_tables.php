<?php

if (!defined('TYPO3_MODE'))
  die('Access denied.');

// add access options to extension backends
$TCA['tt_content']['palettes']['content_access'] = [
    'showitem' => 'hidden, starttime, endtime',
    'canNotCollapse' => true,
    'isHiddenPalette' => false,
];

// Only display fields 'title' and 'alternative' for uploaded images
$TCA['sys_file_reference']['palettes']['imageoverlayPalette']['showitem'] = 'title,alternative';