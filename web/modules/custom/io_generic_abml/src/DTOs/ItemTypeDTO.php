<?php

namespace Drupal\io_generic_abml\DTOs;

class ItemTypeDTO extends GenericDTO {

  /**
   * ID Table Primary key
   *
   * @var Integer
   */
  protected $id;

  /**
   * Item type
   *
   * @var String
   */
  protected $type;

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

  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }

  public function setStatus($status) {
    $this->status = $status;
  }
  public function getStatus() {
    return $this->status;
  }
}
