<?php

namespace BStrauss\Engine\Plugin;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

abstract class BackendPreviewRenderer {
  /**
   * @var string
   */
  private $extensionKey;

  /**
   * @var string
   */
  private $pluginId;

  /**
   * @var int
   */
  private $contentUid;

  /**
   * @var \TYPO3\CMS\Extbase\Object\ObjectManager
   * @inject
   */
  protected $objectManager;

  /**
   * @var \TYPO3\CMS\Fluid\View\StandaloneView
   */
  public $view;

  /**
   * @param string $extensionKey
   * @param string $pluginId
   * @param int $contentUid
   */
  public function initialize($extensionKey, $pluginId, $contentUid) {
    $this->extensionKey = $extensionKey;
    $this->pluginId = $pluginId;
    $this->contentUid = $contentUid;

    /** @var \TYPO3\CMS\Fluid\View\StandaloneView $contentView */
    $this->view = $this->objectManager->get(StandaloneView::class);

    $fluidRootPath = GeneralUtility::getFileAbsFileName("EXT:$this->extensionKey/Resources/Private/");

    $this->view->setTemplateRootPaths([$fluidRootPath . 'Templates']);
    $this->view->setLayoutRootPaths([$fluidRootPath . 'Layouts']);
    $this->view->setPartialRootPaths([$fluidRootPath . 'Partials']);

    $this->view->setTemplatePathAndFilename($fluidRootPath . "Templates/Previews/$this->pluginId.html");

    $this->view->getRequest()->setControllerExtensionName($this->extensionKey);
  }

  abstract public function render();

  /**
   * @param \TYPO3\CMS\Extbase\Persistence\Repository $contentRepository
   * @return \BStrauss\Engine\Domain\Model\AbstractContent
   */
  protected function getContent($contentRepository) {
    return $contentRepository->findByUid($this->contentUid);
  }

  /**
   * @return string
   */
  protected function getExtensionKey() {
    return $this->extensionKey;
  }

  /**
   * @return string
   */
  protected function getPluginId() {
    return $this->pluginId;
  }

  /**
   * @return int
   */
  protected function getContentUid() {
    return $this->contentUid;
  }
}