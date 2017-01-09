<?php

namespace BStrauss\Engine\ViewHelpers;

use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\ChildNodeAccessInterface;

class PartialViewHelper extends AbstractViewHelper implements ChildNodeAccessInterface
{
    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * An array containing child nodes
     *
     * @var array<\TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\AbstractNode>
     */
    private $childNodes = array();

    /**
     * @var bool
     */
    private $noExtension = false;

    /**
     * Setter for ChildNodes - as defined in ChildNodeAccessInterface
     *
     * @param array $childNodes Child nodes of this syntax tree node
     *
     * @return void
     */
    public function setChildNodes(array $childNodes)
    {
        $this->childNodes = $childNodes;
    }

    /**
     * @return void
     */
    public function initializeArguments()
    {
        // path (EXT:abc), arguments

        // {namespace ng=BStrauss\Engine\ViewHelpers}
        // <ng:slot name="header"/> --> {slot-header}
        // <ng:slot name="footer"/> --> {slot-footer}

        $this->registerArgument('partial', 'string', 'path to partial file', true);
        $this->registerArgument('extension', 'string', 'extension name', false);
        $this->registerArgument('arguments', 'array', 'arguments to inject into the partial', false);
        $this->registerArgument('id', 'string', 'id of this viewhelper to reference when using with partialselector', false);
        $this->registerArgument('as', 'string', 'name of the variable inside the partial html that the defaul content gets injected to', false);
    }

    public function render()
    {
        $extensionName = $this->arguments['extension'];
        $partialName = $this->arguments['partial'];
        $argumentsArray = $this->arguments['arguments'];
        $argumentsArray = $this->renderChildSelectors($argumentsArray);

        $hasExtensionName = strlen(trim($extensionName)) > 0;

        if ($hasExtensionName)
        {
            if (strlen(trim($this->controllerContext->getRequest()->getControllerExtensionKey())) === 0)
            {
                $this->noExtension = true;
            }

            $request = clone $this->controllerContext->getRequest();
            $request->setControllerExtensionName($extensionName);
            $controllerContext = clone $this->controllerContext;
            $controllerContext->setRequest($request);
            $this->setPartialRootPath($controllerContext);
        }

        $content = $this->viewHelperVariableContainer->getView()->renderPartial($partialName, null, $argumentsArray);

        if ($hasExtensionName)
        {
            $this->resetPartialRootPath();
        }

        return $content;
    }

    private function renderChildSelectors($argumentsArray)
    {
        $viewHelperId = $this->arguments['id'];

        $count = 0;

        foreach ($this->childNodes as $childNode)
        {
            if ($childNode instanceof ViewHelperNode
                && $childNode->getViewHelperClassName() === PartialViewHelper::class
            )
            {
                $arguments = $childNode->getArguments();

                /** @var \TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\TextNode $as */
                $as = $arguments['as'];

                /** @var \TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\TextNode $for */
                $for = $arguments['for'];

                if ($for->getText() === $viewHelperId)
                {
                    $argumentsArray[$as->getText()] = $childNode->evaluate($this->renderingContext);
                    $count++;
                }
            }
        }

        if ($count === 0)
        {
            $variableName = $this->arguments['as'] != false ? $this->arguments['as'] : 'content';

            $argumentsArray[$variableName] = $this->renderChildren();
        }

        return $argumentsArray;
    }

    /**
     * Set partial root path by controller context
     *
     * @param ControllerContext $controllerContext
     *
     * @return void
     */
    protected function setPartialRootPath(ControllerContext $controllerContext)
    {
        $this->viewHelperVariableContainer->getView()->setPartialRootPath(
            'typo3conf/ext/' . $controllerContext->getRequest()->getControllerExtensionKey() . '/Resources/Private/Partials/'
        );
    }

    /**
     * Resets the partial root path to original controller context
     *
     * @return void
     */
    protected function resetPartialRootPath()
    {
        if ($this->noExtension)
        {
            $this->viewHelperVariableContainer->getView()->setPartialRootPath('fileadmin/Resources/Private/Partials/');
        }
        else
        {
            $this->setPartialRootPath($this->controllerContext);
        }
    }
}