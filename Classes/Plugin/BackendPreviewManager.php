<?php

namespace BStrauss\Engine2\Plugin;

use TYPO3\CMS\Backend\View\PageLayoutView;
use TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

class BackendPreviewManager implements PageLayoutViewDrawItemHookInterface {
  /**
   * @var array
   */
  static $mappings = [];

  /**
   * @var ObjectManager
   */
  private $objectManager;

  /**
   * Preprocesses the preview rendering of a content element.
   *
   * @param PageLayoutView $parentObject Calling parent object
   * @param bool $drawItem Whether to draw the item using the default functionalities
   * @param string $headerContent Header content
   * @param string $itemContent Item content
   * @param array $row Record row of tt_content
   */
  public function preProcess(PageLayoutView &$parentObject, &$drawItem, &$headerContent, &$itemContent, array &$row) {
    $drawItem = false;

    $config = self::$mappings[$row[ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT]];

    $class = $config['class'];
    $extensionKey = $config['extensionKey'];
    $pluginId = $config['pluginId'];

    $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);

    /** @var BackendPreviewRenderer $renderer */
    $renderer = $this->objectManager->get($class);
    $renderer->initialize($extensionKey, $pluginId, $row['uid']);
    $renderer->render();

    $itemContent = $renderer->view->render();
  }
}