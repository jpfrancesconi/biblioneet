<?php

namespace Drupal\io_generic_abml\DTOs;

class AcquisitionConditionDTO extends GenericDTO {

  /**
   * ID Table Primary key
   *
   * @var Integer
   */
  protected $id;

  /**
   * Condition name
   *
   * @var String
   */
  protected $condition;

  /**
   * Item status
   *
   * @var Boolean
   */
  protected $status;

  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }

  public function setCondition($condition) {
    $this->condition = $condition;
  }
  public function getCondition() {
    return $this->condition;
  }

  public function setStatus($status) {
    $this->status = $status;
  }
  public function getStatus() {
    return $this->status;
  }
}
