<?php
namespace BStrauss\Engine\Tca;

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * https://docs.typo3.org/typo3cms/TCAReference/WhatIsTca/Index.html
 * https://docs.typo3.org/typo3cms/TCAReference/Reference/Index.html
 *
 * Default fields:
 * - uid
 * - created_at
 * - created_by
 * - modified_at
 * - deleted
 * - sorting
 * - tt_content
 *
 * Optional fields:
 * - hidden
 * - starttime
 * - endtime
 */
class Builder {
  /**
   * @var string
   */
  private $tableName;

  /**
   * @var string
   */
  private $title;

  /**
   * @var string
   */
  private $label;

  /**
   * @var bool
   */
  private $hideable;

  /**
   * @var bool
   */
  private $timeable;

  /**
   * @var bool
   */
  private $explicitLocalization = false;

  /**
   * @var string
   */
  private $labelAlt;

  /**
   * @var bool
   */
  private $labelAltForce;

  /**
   * @var array
   */
  private $columns = [];

  /**
   * @var array
   */
  private $searchFields = [];

  /**
   * @var array
   */
  private $showRecordFieldList = [];

  /**
   * @var string
   */
  private $showItem = '';

  /**
   * @param string $tableName
   * @param string $title
   * @param string $label
   * @param bool $hideable
   * @param bool $timeable
   * @param string $labelAlt
   * @param bool $labelAltForce
   */
  public function __construct($tableName, $title, $label, $hideable = false, $timeable = false, $labelAlt = '',
      $labelAltForce = false) {
    $this->tableName = $tableName;
    $this->title = $title;
    $this->label = $label;
    $this->hideable = $hideable;
    $this->timeable = $timeable;
    $this->labelAlt = $labelAlt;
    $this->labelAltForce = $labelAltForce;

    if ($this->hideable) {
      $this->showRecordFieldList[] = 'hidden';

      $this->columns['hidden'] = [
          'exclude' => 1,
          'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
          'config' => [
              'type' => 'check',
          ],
      ];
    }

    if ($this->timeable) {
      $this->columns['starttime'] = [
          'exclude' => 1,
          'l10n_mode' => 'mergeIfNotBlank',
          'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
          'config' => [
              'type' => 'input',
              'size' => 13,
              'max' => 20,
              'eval' => 'datetime',
              'checkbox' => 0,
              'default' => 0,
              'range' => [
                  'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y')),
              ],
          ],
      ];

      $this->columns['endtime'] = [
          'exclude' => 1,
          'l10n_mode' => 'mergeIfNotBlank',
          'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
          'config' => [
              'type' => 'input',
              'size' => 13,
              'max' => 20,
              'eval' => 'datetime',
              'checkbox' => 0,
              'default' => 0,
              'range' => [
                  'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y')),
              ],
          ],
      ];
    }

    $this->columns['tt_content'] = [
        'label' => 'tt_content',
        'config' => [
            'type' => 'passthrough',
        ],
    ];
  }

  /**
   * @param string $label
   * @return $this
   */
  public function addTab($label) {
    $this->showItem .= "--div--;$label,";

    return $this;
  }

  /**
   * @param string $name
   * @param string $label
   * @param array $config
   * @param bool $searchable
   * @param string|null $defaultExtras
   * @param bool $exclude
   * @param null|string|array $displayCondition
   * @return $this
   */
  public function addColumn($name, $label, $config,
      $searchable = false, $defaultExtras = null, $exclude = false, $displayCondition = null) {
    $array = [
        'label' => $label,
        'config' => $config,
        'exclude' => $exclude ? 1 : 0,
    ];

    $this->showRecordFieldList[] = $name;
    $this->showItem .= "$name,";

    if ($searchable)
      $this->searchFields[] = $name;

    if (!is_null($defaultExtras) && is_string($defaultExtras))
      $array['defaultExtras'] = $defaultExtras;

    if (!is_null($displayCondition) && (is_string($displayCondition) || is_array($displayCondition)))
      $array['displayCond'] = $displayCondition;

    $this->columns[$name] = $array;

    return $this;
  }

  /**
   * @param string $name
   * @param string $label
   * @param string $eval
   * @param string $placeholder
   * @param bool $searchable
   * @param string|null $defaultExtras
   * @param bool $exclude
   * @param null|string|array $displayCondition
   * @return $this
   */
  public function addInput($name, $label, $eval = '', $placeholder = '',
      $searchable = false, $defaultExtras = null, $exclude = false, $displayCondition = null) {
    $this->addColumn($name, $label, [
        'type' => 'input',
        'size' => 30,
        'eval' => $eval,
        'format' => '',
        'max' => 255,
        'placeholder' => $placeholder,
        'range' => [],
    ], $searchable, $defaultExtras, $exclude, $displayCondition);

    return $this;
  }

  /**
   * @param string $name
   * @param string $label
   * @param string $eval
   * @param string $placeholder
   * @param bool $searchable
   * @param string|null $defaultExtras
   * @param bool $exclude
   * @param null|string|array $displayCondition
   * @return $this
   */
  public function addText($name, $label, $eval = '', $placeholder = '',
      $searchable = false, $defaultExtras = null, $exclude = false, $displayCondition = null) {
    $this->addColumn($name, $label, [
        'type' => 'text',
        'eval' => $eval,
        'format' => '',
        'placeholder' => $placeholder,
        'cols' => 30,
        'rows' => 5,
    ], $searchable, $defaultExtras, $exclude, $displayCondition);

    return $this;
  }

  /**
   * @param string $name
   * @param string $label
   * @param bool $searchable
   * @param string|null $defaultExtras
   * @param bool $exclude
   * @param null|string|array $displayCondition
   * @return $this
   */
  public function addCheckbox($name, $label,
      $searchable = false, $defaultExtras = null, $exclude = false, $displayCondition = null) {
    $this->addColumn($name, $label, [
        'type' => 'check',
    ], $searchable, $defaultExtras, $exclude, $displayCondition);

    return $this;
  }

  /**
   * @param string $name
   * @param string $label
   * @param array $items
   * @param bool $searchable
   * @param string|null $defaultExtras
   * @param bool $exclude
   * @param null|string|array $displayCondition
   * @return $this
   */
  public function addRadioButtons($name, $label, $items,
      $searchable = false, $defaultExtras = null, $exclude = false, $displayCondition = null) {
    $this->addColumn($name, $label, [
        'type' => 'radio',
        'items' => $items,
    ], $searchable, $defaultExtras, $exclude, $displayCondition);

    return $this;
  }

  /**
   * @param string $name
   * @param string $label
   * @param array $items
   * @param bool $searchable
   * @param string|null $defaultExtras
   * @param bool $exclude
   * @param null|string|array $displayCondition
   * @return $this
   */
  public function addSelect($name, $label, $items,
      $searchable = false, $defaultExtras = null, $exclude = false, $displayCondition = null) {
    $this->addColumn($name, $label, [
        'type' => 'select',
        'renderType' => 'selectSingle',
        'items' => $items,
    ], $searchable, $defaultExtras, $exclude, $displayCondition);

    return $this;
  }

  /**
   * @param string $name
   * @param string $label
   * @param array $items
   * @param int $minitems
   * @param int $maxitems
   * @param bool $searchable
   * @param string|null $defaultExtras
   * @param bool $exclude
   * @param null|string|array $displayCondition
   * @return $this
   */
  public function addMultiSelect($name, $label, $items, $minitems = 0, $maxitems = 10000,
      $searchable = false, $defaultExtras = null, $exclude = false, $displayCondition = null) {
    $this->addColumn($name, $label, [
        'type' => 'select',
        'renderType' => 'selectMultipleSideBySide',
        'items' => $items,
        'minitems' => $minitems,
        'maxitems' => $maxitems,
    ], $searchable, $defaultExtras, $exclude, $displayCondition);

    return $this;
  }

  /**
   * @param string $name
   * @param string $label
   * @param int $maxItems
   * @param string $fileTypes
   * @param bool $searchable
   * @param string|null $defaultExtras
   * @param bool $exclude
   * @param null|string|array $displayCondition
   * @return $this
   */
  public function addImage($name, $label, $maxItems = 1, $fileTypes = 'png',
      $searchable = false, $defaultExtras = null, $exclude = false, $displayCondition = null) {
    $this->addColumn($name, $label, ExtensionManagementUtility::getFileFieldTCAConfig(
        $this->tableName . '.' . $name,
        [
            'maxitems' => $maxItems,
            'foreign_types' => [
                File::FILETYPE_IMAGE => [
                    'showitem' => '--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,--palette--;;filePalette',
                ],
            ],
            'appearance' => [
                'fileUploadAllowed' => false,
                'enabledControls' => [
                    'new' => true,
                    'sort' => true,
                ],
            ],
        ],
        $fileTypes
    ), $searchable, $defaultExtras, $exclude, $displayCondition);

    return $this;
  }

  /**
   * @param string $name
   * @param string $label
   * @param bool $searchable
   * @param bool $exclude
   * @param null|string|array $displayCondition
   * @return $this
   */
  public function addRte($name, $label,
      $searchable = false, $exclude = false, $displayCondition = null) {
    $this->addColumn($name, $label, [
        'type' => 'text',
        'cols' => 40,
        'rows' => 15,
    ], $searchable, 'richtext:rte_transform', $exclude, $displayCondition);

    return $this;
  }

  /**
   * @param bool $exclude
   * @return $this
   */
  public function addLanguageSelect($exclude = false) {
    $this->explicitLocalization = true;
    $excludeFlag = $exclude ? 1 : 0;

    $this->showItem .= 'sys_language_uid, l18n_parent,';

    $this->showRecordFieldList[] = 'sys_language_uid';

    $this->columns['sys_language_uid'] = [
        'exclude' => $excludeFlag,
        'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
        'config' => [
            'type' => 'select',
            'foreign_table' => 'sys_language',
            'foreign_table_where' => 'ORDER BY sys_language.title',
            'items' => [
                ['LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1],
                ['LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0],
            ],
        ],
    ];

    $this->columns['l18n_diffsource'] = [
        'config' => [
            'type' => 'passthrough',
        ],
    ];

    $this->columns['l18n_parent'] = [
        'displayCond' => 'FIELD:sys_language_uid:>:0',
        'exclude' => $excludeFlag,
        'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
        'config' => [
            'type' => 'select',
            'items' => [
                ['', 0],
            ],
            'foreign_table' => $this->tableName,
            'foreign_table_where' => "AND $this->tableName.pid=###CURRENT_PID### AND $this->tableName.sys_language_uid IN (-1,0)",
        ],
    ];

    return $this;
  }

  /**
   * @return array
   */
  public function build() {
    $enablecolumns = [];

    if ($this->hideable)
      $enablecolumns['disabled'] = 'hidden';

    if ($this->timeable) {
      $enablecolumns['starttime'] = 'starttime';
      $enablecolumns['endtime'] = 'endtime';
    }

    $ctrl = [
        'title' => $this->title,
        'label' => $this->label,
        'label_alt' => $this->labelAlt,
        'label_alt_force' => $this->labelAltForce,
        'iconfile' => 'EXT:engine/Resources/Public/Icons/tca_model_element.svg',
        'searchFields' => implode(',', $this->searchFields),
      // default fields
        'sortby' => 'sorting',
        'tstamp' => 'modified_at',
        'crdate' => 'created_at',
        'cruser_id' => 'created_by',
        'delete' => 'deleted',
        'enablecolumns' => $enablecolumns,
    ];

    if ($this->explicitLocalization) {
      $ctrl['languageField'] = 'sys_language_uid';
      $ctrl['transOrigPointerField'] = 'l18n_parent';
      $ctrl['transOrigDiffSourceField'] = 'l18n_diffsource';
    }

    return [
        'ctrl' => $ctrl,
        'columns' => $this->columns,
        'interface' => [
            'showRecordFieldList' => implode(',', $this->showRecordFieldList),
        ],
        'types' => [
            '0' => [
                'showitem' => $this->showItem,
            ],
        ],
    ];
  }
}