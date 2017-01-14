<?php

namespace BStrauss\Engine\Utils;

class LineBreakUtility {
  /**
   * @param string $text
   * @param string $match
   * @return string
   */
  static function hardBreak($text, $match = '\|') {
    return mb_ereg_replace($match, '<br>', $text);
  }

  /**
   * @param string $text
   * @param string $match
   * @return string
   */
  static function softBreak($text, $match = '\|') {
    return mb_ereg_replace($match, '&shy;', $text);
  }

  /**
   * @param string $text
   * @param string $match
   * @return string
   */
  static function wordBreak($text, $match = '\|') {
    return mb_ereg_replace($match, '<wbr>', $text);
  }
}