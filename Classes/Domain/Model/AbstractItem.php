<?php

namespace BStrauss\Engine\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class AbstractItem extends AbstractEntity {
  /**
   * @var boolean
   */
  protected $hidden;

  /**
   * @return boolean
   */
  public function getHidden() {
    return $this->hidden;
  }

  /**
   * @param boolean $hidden
   */
  public function setHidden($hidden) {
    $this->hidden = $hidden;
  }
}