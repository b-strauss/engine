<?php

namespace BStrauss\Engine\ViewHelpers;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

class SlotViewHelper extends AbstractViewHelper {
  public function initializeArguments() {
    parent::initializeArguments();

    $this->registerArgument('name', 'string', 'Name of the slot. The result name inside the partial will be prefixed with "slot-".', true);
  }

  /**
   * @return string
   */
  public function render() {
    return $this->renderChildren();
  }
}