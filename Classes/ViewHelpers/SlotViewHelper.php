<?php

namespace BStrauss\Engine\ViewHelpers;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class SlotViewHelper extends AbstractViewHelper {
  use CompileWithRenderStatic;

  public function initializeArguments() {
    parent::initializeArguments();

    $this->registerArgument('name', 'string', 'Name of the slot. The result name inside the partial will be prefixed with "slot-".', true);
  }

  public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext) {
    return $renderChildrenClosure();
  }
}