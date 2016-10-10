<?php

namespace BStrauss\Engine\Utils;

use TYPO3\CMS\Core\Utility\DebugUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility as Typo3LocalizationUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Lang\LanguageService;

class LocalizationUtility {
  /**
   * @param string $key
   * @param string $extensionName
   * @param array|null $arguments
   * @param bool $htmlEscape
   * @return string
   */
  static function translate($key, $extensionName, array $arguments = null, $htmlEscape = false) {
    $value = Typo3LocalizationUtility::translate($key, $extensionName, $arguments);

    if (empty($value) === true) {
      $value = $key;
    } else if ($htmlEscape === true) {
      $value = htmlspecialchars($value);
    }

    return $value;
  }

  /**
   * @param string $key
   * @param string $extensionName
   * @param array|null $arguments
   * @param bool $htmlEscape
   * @return string
   */
  static function translateBackend($key, $extensionName, array $arguments = null, $htmlEscape = false) {
    //$language = LocalizationUtility::getBackendLanguage();
    //$prefix = $language === 'default' ? '' : "$language.";
    //$path = "LLL:EXT:$extensionName/Resources/Private/Language/{$prefix}locallang_be.xlf:$key";
    $path = "LLL:EXT:$extensionName/Resources/Private/Language/locallang_be.xlf:$key";

    $value = Typo3LocalizationUtility::translate(
        $path,
        null,
        $arguments
    );

    if (empty($value) === true) {
      $value = $key;
    } else if ($htmlEscape === true) {
      $value = htmlspecialchars($value);
    }

    return $value;
  }

  /**
   * @return TypoScriptFrontendController
   */
  static function getTypoScriptFrontendController() {
    return $GLOBALS['TSFE'];
  }

  /**
   * @return LanguageService
   */
  static function getLanguageService() {
    return $GLOBALS['LANG'];
  }

  /**
   * @return string
   */
  static function getBackendLanguage() {
    return LocalizationUtility::getLanguageService()->lang;
  }

  /**
   * @return array
   */
  static function getTypoScriptConfig() {
    return LocalizationUtility::getTypoScriptFrontendController()->config;
  }

  /**
   * @return int
   */
  static function getSysLanguageUid() {
    $ts = LocalizationUtility::getTypoScriptConfig();

    return intval($ts['config']['sys_language_uid']);
  }

  /**
   * @return string
   */
  static function getLanguage() {
    $ts = LocalizationUtility::getTypoScriptConfig();

    return $ts['config']['language'];
  }

  /**
   * @return string
   */
  static function getHtmlLanguage() {
    $ts = LocalizationUtility::getTypoScriptConfig();

    return $ts['config']['htmlTag_langKey'];
  }

  /**
   * @return string
   */
  static function getLocale() {
    $ts = LocalizationUtility::getTypoScriptConfig();

    return $ts['config']['locale_all'];
  }
}