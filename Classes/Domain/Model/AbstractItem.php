<?php

namespace BStrauss\Engine\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class AbstractItem extends AbstractEntity {
  /**
   * @var int
   */
  protected $hidden;

  /**
   * @return int
   */
  public function getHidden() {
    return $this->hidden;
  }

  /**
   * @param int $hidden
   */
  public function setHidden($hidden) {
    $this->hidden = $hidden;
  }
}