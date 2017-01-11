<?php

namespace BStrauss\Engine\ViewHelpers;

use FluidTYPO3\Vhs\ViewHelpers\Render\AbstractRenderViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\ChildNodeAccessInterface;

class RenderViewHelper extends AbstractRenderViewHelper implements ChildNodeAccessInterface {
  /**
   * @var array<\TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\AbstractNode>
   */
  private $childNodes = [];

  /**
   * Sets the direct child nodes of the current syntax tree node.
   *
   * @param array<\TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\AbstractNode> $childNodes
   * @return void
   */
  public function setChildNodes(array $childNodes) {
    $this->childNodes = $childNodes;
  }

  public function initializeArguments() {
    parent::initializeArguments();

    $this->registerArgument('path', 'string', 'Path to the partial file, EXT:myext/... paths supported.', true);
    $this->registerArgument('arguments', 'array', 'Arguments to pass to the partial.', false, []);
  }

  /**
   * @return string
   */
  public function render() {
    $path = GeneralUtility::getFileAbsFileName($this->arguments['path']);
    $arguments = array_merge($this->arguments['arguments'], $this->renderSlots());

    $view = $this->getPreparedView();
    $view->setTemplatePathAndFilename($path);
    $view->assignMultiple($arguments);

    return $this->renderView($view);
  }

  /**
   * @return array
   */
  private function renderSlots() {
    $renderedSlots = [];

    foreach ($this->childNodes as $childNode) {
      if ($childNode instanceof ViewHelperNode
          && $childNode->getViewHelperClassName() === SlotViewHelper::class
      ) {
        $slotArguments = $childNode->getArguments();

        /** @var \TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\TextNode $name */
        $name = $slotArguments['name'];
        $nameString = $name->getText();

        $slotName = "slot-$nameString";

        $renderedSlots[$slotName] = $childNode->evaluate($this->renderingContext);
      }
    }

    return $renderedSlots;
  }
}