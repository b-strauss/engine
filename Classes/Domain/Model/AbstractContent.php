<?php

namespace BStrauss\Engine2\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class AbstractContent extends AbstractEntity {
  /**
   * @var \TYPO3\CMS\Extbase\Service\FlexFormService
   * @inject
   */
  protected $flexFormService;

  /**
   * @var \string
   */
  protected $piFlexform;

  /**
   * @return array
   */
  public function getFlexform() {
    return $this->flexFormService->convertFlexFormContentToArray($this->piFlexform);
  }
}