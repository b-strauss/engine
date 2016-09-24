<?php

namespace BStrauss\Engine\Utils;

class LineBreakUtility {
  static $hardBreakReplacement = '|';

  static $softBreakReplacement = '|';

  static $wordBreakReplacement = '|';

  /**
   * @param string $text
   * @return string
   */
  static function hardBreak($text) {
    return str_replace(self::$hardBreakReplacement, '<br>', $text);
  }

  /**
   * @param string $text
   * @return string
   */
  static function softBreak($text) {
    return str_replace(self::$softBreakReplacement, '&shy;', $text);
  }

  /**
   * @param string $text
   * @return string
   */
  static function wordBreak($text) {
    return str_replace(self::$wordBreakReplacement, '<wbr>', $text);
  }
}