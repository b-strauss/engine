<?php

namespace BStrauss\Engine\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3Fluid\Fluid\Core\Compiler\TemplateCompiler;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\NodeInterface;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\TextNode;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInterface;

class RenderViewHelper extends AbstractViewHelper implements ViewHelperInterface {
  /**
   * @var NodeInterface[]
   */
  protected $childNodes = [];

  protected $escapeChildren = false;

  protected $escapeOutput = false;

  /**
   * Sets the direct child nodes of the current syntax tree node.
   *
   * @param NodeInterface[] $childNodes
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
   * @param string $path
   * @param array $arguments
   * @return string
   */
  private static function renderTemplate($path, $arguments) {
    /** @var ObjectManager $objectManager */
    $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

    /** @var StandaloneView $view */
    $view = $objectManager->get(StandaloneView::class);
    $view->setTemplatePathAndFilename($path);
    $view->assignMultiple($arguments);

    try {
      $content = $view->render();
    } catch (\Exception $error) {
      $content = $error->getMessage() . ' (' . $error->getCode() . ')';
    }

    return $content;
  }

  /**
   * @param ViewHelperNode $node
   * @return string
   */
  private static function getSlotName($node) {
    $arguments = $node->getArguments();
    /** @var TextNode $nameNode */
    $nameNode = $arguments['name'];
    $slotName = sprintf('slot-%s', $nameNode->getText());

    return $slotName;
  }

  /**
   * @return string
   */
  public function render() {
    $path = GeneralUtility::getFileAbsFileName($this->arguments['path']);

    $renderedSlots = [];

    foreach ($this->childNodes as $childNode) {
      if ($childNode instanceof ViewHelperNode
          && $childNode->getViewHelperClassName() === SlotViewHelper::class
      ) {
        $slotName = self::getSlotName($childNode);

        $renderedSlots[$slotName] = $childNode->evaluate($this->renderingContext);
      }
    }

    if (count($renderedSlots) === 0)
      $renderedSlots['slot-default'] = $this->renderChildren();

    return self::renderTemplate($path, array_merge($this->arguments['arguments'], $renderedSlots));
  }

  /**
   * @param array $arguments
   * @param \Closure $renderChildrenClosure
   * @param RenderingContextInterface $renderingContext
   * @return string the resulting string which is directly shown
   */
  public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext) {
    $path = GeneralUtility::getFileAbsFileName($arguments['path']);
    $slots = $arguments['__slotClosures'];

    $renderedSlots = [];

    if (count($slots) === 0) {
      $renderedSlots['slot-default'] = $renderChildrenClosure();
    } else {
      foreach ($slots as $slotName => $slotClosure)
        $renderedSlots[$slotName] = $slotClosure();
    }

    return self::renderTemplate($path, array_merge($arguments['arguments'], $renderedSlots));
  }

  /**
   * @param string $argumentsName
   * @param string $closureName
   * @param string $initializationPhpCode
   * @param ViewHelperNode $node
   * @param TemplateCompiler $compiler
   * @return string
   */
  public function compile($argumentsName, $closureName, &$initializationPhpCode, ViewHelperNode $node, TemplateCompiler $compiler) {
    $initializationPhpCode .= sprintf('%s[\'__slotClosures\'] = [];', $argumentsName) . chr(10);

    foreach ($node->getChildNodes() as $childNode) {
      if ($childNode instanceof ViewHelperNode
          && $childNode->getViewHelperClassName() === SlotViewHelper::class
      ) {
        $slotName = self::getSlotName($childNode);

        $childNodesAsClosure = $compiler->wrapChildNodesInClosure($childNode);
        $initializationPhpCode .= sprintf('%s[\'__slotClosures\'][\'%s\'] = %s;', $argumentsName, $slotName, $childNodesAsClosure) . chr(10);
      }
    }

    return parent::compile($argumentsName, $closureName, $initializationPhpCode, $node, $compiler);
  }
}