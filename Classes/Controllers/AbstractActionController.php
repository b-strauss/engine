<?php

namespace BStrauss\Engine2\Controllers;

use BStrauss\Engine2\Utils\LocalizationUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class AbstractActionController extends ActionController {
  /**
   * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager
   * @inject
   */
  protected $configurationManager;

  /**
   * @var array
   */
  protected $data;

  protected function initializeAction() {
    $this->data = $this->configurationManager->getContentObject()->data;
  }

  /**
   * @param \TYPO3\CMS\Extbase\Persistence\Repository $contentRepository
   * @return \BStrauss\Engine2\Domain\Model\AbstractContent
   */
  protected function getContent($contentRepository) {
    return $contentRepository->findByUid($this->data['uid']);
  }

  /**
   * @return TypoScriptFrontendController
   */
  protected function getTsfe() {
    return $GLOBALS['TSFE'];
  }

  /**
   * @return string
   */
  protected function getBaseURL() {
    return $this->getTsfe()->config['config']['baseURL'];
  }

  /**
   * @param string $key
   * @param string|null $extensionName
   * @param array|null $arguments
   * @param bool $htmlEscape
   * @return string
   */
  protected function translate($key, $extensionName = null, array $arguments = null, $htmlEscape = false) {
    if (empty($extensionName) === true) {
      $extensionName = $this->controllerContext->getRequest()->getControllerExtensionName();
    }

    return LocalizationUtility::translate($key, $extensionName, $arguments, $htmlEscape);
  }
}