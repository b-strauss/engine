<?php

namespace BStrauss\Engine\Plugin;

use BStrauss\Engine\Utils\LocalizationUtility;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

class PluginUtility {
  /**
   * @var string
   */
  protected $extensionKey;

  /**
   * @var string
   */
  protected $pluginId;

  /**
   * @var string
   */
  protected $pluginTitle;

  /**
   * @var string
   */
  protected $pluginDescription;

  /**
   * @var string
   */
  protected $pluginType;

  /**
   * @var string
   */
  protected $pluginSignature;

  /**
   * @param string $vendorPrefix
   * @param string $extensionKey
   * @param string $pluginId
   * @param string $pluginTitle
   * @param string $pluginDescription
   * @param string $pluginType
   */
  public function __construct($vendorPrefix, $extensionKey, $pluginId, $pluginTitle, $pluginDescription,
      $pluginType = ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT) {
    $this->extensionKey = $extensionKey;
    $this->pluginId = $pluginId;
    $this->pluginTitle = $pluginTitle;
    $this->pluginDescription = $pluginDescription;
    $this->pluginType = $pluginType;

    $extensionName = GeneralUtility::underscoredToUpperCamelCase($this->extensionKey);

    $this->pluginSignature = mb_strtolower($extensionName) .
        '_' .
        mb_strtolower($this->pluginId);

    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$this->pluginSignature]
        = 'layout,recursive,select_key,pages';

    ExtensionUtility::registerPlugin(
        "$vendorPrefix.$extensionName",
        $this->pluginId,
        $this->pluginTitle
    );
  }

  /**
   * @param bool|string $flexform
   * @param array $additionalConfig
   * @param string $group
   * @param string $nameOfGroup
   */
  public function addBackendConfiguration($flexform = false, $additionalConfig = [],
      $group = 'common', $nameOfGroup = null) {
    $shouldIncludeFlexform = false;

    if (is_bool($flexform) && $flexform === true) {
      $this->addFlexform();
      $shouldIncludeFlexform = true;
    } else if (is_string($flexform)) {
      $this->addFlexform($flexform);
      $shouldIncludeFlexform = true;
    }

    $this->addNewContentElement($group, $nameOfGroup);
    $this->addTtContentConfiguration($shouldIncludeFlexform, $additionalConfig);
  }

  /**
   * Registers a class to render the backend preview. The class needs to extend BackendPreviewManager.
   *
   * @see {\BStrauss\Engine\Plugin\BackendPreviewManager}
   *
   * The render method can be used to set template variables.
   *
   * The BackendPreviewManager will automatically load a template in:
   *
   * EXT:<name_of_your_extension>/Resources/Private/Templates/Previews/<plugin_id>.html
   *
   * @param string $class
   */
  public function registerBackendPreviewRenderer($class) {
    BackendPreviewManager::$mappings[$this->pluginSignature] = [
        'class' => $class,
        'extensionKey' => $this->extensionKey,
        'pluginId' => $this->pluginId,
    ];

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem']
    [$this->pluginSignature] = BackendPreviewManager::class;
  }

  /**
   * The name of the xml file must be same as the plugin identifier
   * and the file is intended to be located in 'Resources/Private/Flexform' directory
   *
   * @param string|null $flexformString
   */
  private function addFlexform($flexformString = null) {
    $piKeyToMatch = '';

    if ($this->pluginType == 'list') {
      $piKeyToMatch = $this->pluginSignature;
      $cTypeToMatch = $this->pluginType;
      $GLOBALS['TCA']['tt_content']['types'][$this->pluginType]['subtypes_addlist'][$this->pluginSignature]
          = 'pi_flexform';
    } else {
      $cTypeToMatch = $this->pluginSignature;
      $GLOBALS['TCA']['tt_content']['types'][$this->pluginSignature] = str_replace(
          ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
          ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT . ', pi_flexform, ',
          $GLOBALS['TCA']['tt_content']['types'][1]
      );
    }

    $flexformValue = 'FILE:EXT:' . $this->extensionKey . '/Configuration/Flexforms/' . $this->pluginId . '.xml';

    if (!is_null($flexformString) && is_string($flexformString))
      $flexformValue = $flexformString;

    ExtensionManagementUtility::addPiFlexFormValue(
        $piKeyToMatch,
        $flexformValue,
        $cTypeToMatch
    );
  }

  /**
   * Documentation: @link {https://docs.typo3.org/typo3cms/TSconfigReference/PageTsconfig/Mod/Index.html#new-content-element-wizard-mod-wizards-newcontentelement}
   *
   * Adds configuration for showing the plugin item in the new content element wizard.
   *
   * @param string $group the type of the group.
   *                      The four default groups are: "common", "special", "forms" and "plugins".
   * @param string $nameOfGroup the localized name of the group, if the group is not one of the default groups
   */
  private function addNewContentElement($group = 'common', $nameOfGroup = null) {
    $cType = $this->pluginSignature;
    $listType = '';

    if ($this->pluginType == 'list') {
      $cType = $this->pluginType;
      $listType = $this->pluginSignature;
    }

    $header = '';

    if ($group != 'common'
        && $group != 'special'
        && $group != 'forms'
        && $group != 'plugins'
        && !is_null($nameOfGroup)
    ) {
      $header = "header = $nameOfGroup";
    }

    $addToList = $group != 'plugins' ?
        "show := addToList($this->pluginSignature)" :
        '';

    /** @var IconRegistry $iconRegistry */
    $iconRegistry = GeneralUtility::makeInstance(IconRegistry::class);
    $iconRegistry->registerIcon(
        $this->pluginSignature,
        SvgIconProvider::class,
        ['source' => "EXT:$this->extensionKey/Resources/Public/Icons/$this->pluginId.svg"]
    );

    ExtensionManagementUtility::addPageTSConfig("
      mod.wizards.newContentElement {
        renderMode = tabs
        wizardItems {
          $group {
            $header
            elements {
              $this->pluginSignature {
                icon = ../typo3conf/ext/$this->extensionKey/ext_icon.png
                iconIdentifier = $this->pluginSignature
                title = $this->pluginTitle
                description = $this->pluginDescription
                tt_content_defValues {
                  CType = $cType
                  list_type = $listType
                }
              }
            }
            $addToList
          }
        }
      }
    ");
  }

  /**
   * @param bool $includeFlexform
   * @param array $additionalConfig
   */
  private function addTtContentConfiguration($includeFlexform = false, $additionalConfig = []) {
    $default = [
        'showitem' => PluginUtility::buildBackendTabs($includeFlexform),
    ];

    $GLOBALS['TCA']['tt_content']['types'][$this->pluginSignature] = array_merge($default, $additionalConfig);
  }

  /**
   * @param string $extensionKey
   */
  static function includeTypoScriptConstants($extensionKey) {
    ExtensionManagementUtility::addTypoScriptConstants(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $extensionKey . '/Configuration/TypoScripts/constants.ts">'
    );
  }

  /**
   * @param string $extensionKey
   */
  static function includeTypoScriptSetup($extensionKey) {
    ExtensionManagementUtility::addTypoScriptSetup(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $extensionKey . '/Configuration/TypoScripts/setup.ts">'
    );
  }

  /**
   * @param string $vendorPrefix
   * @param string $extensionKey
   * @param string $pluginId
   * @param array $controllerActions
   * @param array $nonCacheableControllerActions
   * @param string $pluginType
   */
  static function configurePlugin($vendorPrefix,
      $extensionKey,
      $pluginId,
      $controllerActions,
      array $nonCacheableControllerActions = [],
      $pluginType = ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT) {
    $extensionName = GeneralUtility::underscoredToUpperCamelCase($extensionKey);

    ExtensionUtility::configurePlugin(
        "$vendorPrefix.$extensionName",
        $pluginId,
        $controllerActions,
        $nonCacheableControllerActions,
        $pluginType
    );
  }

  /**
   * Configuration of the displayed order of fields in TCEforms.
   *
   * The whole string is divided by tokens according to a - unfortunately - complex ruleset.
   * #1: Overall the value is divided by a "comma" ( , ). Each part represents the configuration for a single field.
   * #2: Each of the field configurations is further divided by a semi- colon ( ; ).
   *     Each part of this division has a special significance.
   *
   * Part 1: Field name reference ( Required! )
   * Part 2: Alternative field label (string or LLL reference)
   * Part 3: Palette number (referring to an entry in the "palettes" section).
   * Part 4: (Deprecated since TYPO3 7.3) Special configuration (split by colon ( : )).
   *         This was moved to columnsOverrides as defaultExtras
   *
   * @see https://docs.typo3.org/typo3cms/TCAReference/singlehtml/Index.html#showitem
   *
   * @param bool $includeFlexform
   * @return string
   */
  static function buildBackendTabs($includeFlexform = false) {
    $contentAccess = LocalizationUtility::translateBackend('backend_tab.content_access', 'engine');
    $general = LocalizationUtility::translateBackend('backend_tab.general', 'engine');

    $configString = '';

    if ($includeFlexform) {
      $pluginConfiguration = LocalizationUtility::translateBackend('backend_tab.plugin_configuration', 'engine');

      $configString .= "
        --div--;$pluginConfiguration,
          pi_flexform,
      ";
    }

    $configString .= "
      --div--;$contentAccess,
        --palette--;;content_access,
      --div--;$general,
        --palette--;;general,
    ";

    return $configString;
  }
}