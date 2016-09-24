<?php

namespace BStrauss\Engine\Tca;

/**
 * https://docs.typo3.org/typo3cms/TCAReference/Reference/Index.html
 *
 * Default fields:
 * - uid
 * - created_at
 * - created_by
 * - modified_at
 * - deleted
 * - hidden
 * - starttime
 * - endtime
 * - sorting
 * - tt_content
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
  private $explicitLocalization;

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
   * @var array
   */
  private $showItem = [];

  /**
   * @param string $tableName
   * @param string $title
   * @param string $label
   * @param bool $explicitLocalization
   * @param string $labelAlt
   * @param bool $labelAltForce
   */
  function __construct(
      $tableName,
      $title,
      $label,
      $explicitLocalization = false,
      $labelAlt = '',
      $labelAltForce = false
  ) {
    $this->tableName = $tableName;
    $this->title = $title;
    $this->label = $label;
    $this->explicitLocalization = $explicitLocalization;
    $this->labelAlt = $labelAlt;
    $this->labelAltForce = $labelAltForce;

    $this->showRecordFieldList[] = 'hidden';

    $this->columns['hidden'] = [
        'exclude' => 1,
        'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
        'config' => [
            'type' => 'check',
        ],
    ];

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

    $this->columns['tt_content'] = [
        'label' => 'tt_content',
        'config' => [
            'type' => 'passthrough',
        ],
    ];

    if ($this->explicitLocalization) {
      $this->showRecordFieldList[] = 'sys_language_uid';

      $this->columns['sys_language_uid'] = [
          'exclude' => 1,
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
          'exclude' => 1,
          'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
          'config' => [
              'type' => 'select',
              'items' => [
                  ['', 0],
              ],
              'foreign_table' => $tableName,
              'foreign_table_where' => "AND $tableName.uid=###CURRENT_PID### AND $tableName.sys_language_uid IN (-1,0)",
          ],
      ];
    }
  }

  /**
   * @param string $name
   * @param string $label
   * @param array $config
   * @param bool $searchable
   * @param string|null $defaultExtras
   * @param int $exclude
   * @param null|string|array $displayCondition
   */
  private function buildColumnField($name, $label, $config, $searchable = true, $defaultExtras = null,
                                    $exclude = 1, $displayCondition = null) {
    $array = [
        'label' => $label,
        'config' => $config,
    ];

    $this->showRecordFieldList[] = $name;
    $this->showItem[] = $name;

    if ($searchable)
      $this->searchFields[] = $name;

    if (!is_null($defaultExtras) && is_string($defaultExtras))
      $array['defaultExtras'] = $defaultExtras;

    $array['exclude'] = $exclude;

    if (!is_null($displayCondition) && (is_string($displayCondition) || is_array($displayCondition)))
      $array['displayCond'] = $displayCondition;

    $this->columns[$name] = $array;
  }

  /**
   * @param string $name
   * @param string $label
   * @param string $eval
   * @param string $format
   * @param int $max
   * @param string $placeholder
   * @param array $range
   * @param bool $searchable
   * @param string|null $defaultExtras
   * @param int $exclude
   * @param null|string|array $displayCondition
   */
  private function buildInput($name, $label, $eval = '', $format = '', $max = 255, $placeholder = '', $range = [],
                              $searchable = true, $defaultExtras = null, $exclude = 1, $displayCondition = null) {
    $this->buildColumnField($name, $label, [
        'type' => 'input',
        'size' => 30,
        'eval' => $eval,
        'format' => $format,
        'max' => $max,
        'placeholder' => $placeholder,
        'range' => $range,
    ], $searchable, $defaultExtras, $exclude, $displayCondition);
  }

  /**
   * @param string $name
   * @param string $label
   * @param string $eval
   * @param string $format
   * @param string $placeholder
   * @param int $cols
   * @param int $rows
   * @param bool $searchable
   * @param string|null $defaultExtras
   * @param int $exclude
   * @param null|string|array $displayCondition
   */
  private function buildText($name, $label, $eval = '', $format = '', $placeholder = '', $cols = 30, $rows = 5,
                             $searchable = true, $defaultExtras = null, $exclude = 1, $displayCondition = null) {
    $this->buildColumnField($name, $label, [
        'type' => 'text',
        'eval' => $eval,
        'format' => $format,
        'placeholder' => $placeholder,
        'cols' => $cols,
        'rows' => $rows,
    ], $searchable, $defaultExtras, $exclude, $displayCondition);
  }

  /**
   * @return array
   */
  private function buildCtrl() {
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
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
    ];

    if ($this->explicitLocalization) {
      $ctrl['languageField'] = 'sys_language_uid';
      $ctrl['transOrigPointerField'] = 'l18n_parent';
      $ctrl['transOrigDiffSourceField'] = 'l18n_diffsource';
    }

    return $ctrl;
  }

  /**
   * @param string $name
   * @param string $label
   * @param string $eval
   * @param string $placeholder
   * @param bool $searchable
   * @param int $exclude
   * @param null|string|array $displayCondition
   */
  public function addInput($name, $label, $eval = '', $placeholder = '',
                           $searchable = true, $exclude = 1, $displayCondition = null) {
    $this->buildInput($name, $label, $eval, '', 255, $placeholder,
                      [], $searchable, null, $exclude, $displayCondition);
  }

  /**
   * @param string $name
   * @param string $label
   * @param string $eval
   * @param string $placeholder
   * @param bool $searchable
   * @param int $exclude
   * @param null|string|array $displayCondition
   */
  public function addText($name, $label, $eval = '', $placeholder = '',
                          $searchable = true, $exclude = 1, $displayCondition = null) {
    $this->buildText($name, $label, $eval, '', $placeholder, 30, 5,
                     $searchable, null, $exclude, $displayCondition);
  }

  public function build() {
    if ($this->explicitLocalization)
      $this->showItem[] = 'sys_language_uid';

    $GLOBALS['TCA'][$this->tableName] = [
        'ctrl' => $this->buildCtrl(),
        'columns' => $this->columns,
        'interface' => [
            'showRecordFieldList' => implode(',', $this->showRecordFieldList),
        ],
        'types' => [
            '1' => [
                'showitem' => implode(',', $this->showItem),
            ],
        ],
        'palettes' => [
            '1' => ['showitem' => ''],
        ],
    ];
  }
}