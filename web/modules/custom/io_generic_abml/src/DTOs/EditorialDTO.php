<?php

namespace Drupal\io_generic_abml\DTOs;

class EditorialDTO extends GenericDTO {

  /**
   * ID Table Primary key
   *
   * @var Integer
   */
  protected $id;

  /**
   * Editorial name
   *
   * @var String
   */
  protected $editorial;

  /**
   * Editorial status
   *
   * @var Boolean
   */
  protected $activo;

  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }

  public function setEditorial($editorial) {
    $this->editorial = $editorial;
  }
  public function getEditorial() {
    return $this->editorial;
  }
  /**
   * Get estado
   */
  public function getActivo() {
    return $this->activo;
  }
  /**
   * Set estado
   */
  public function setActivo($activo) {
    $this->activo = $activo;
  }
  /**
   * Get estado to show to the user
   */
  public function getActivoString() {
    if ($this->activo) {
      return t('SI');
    }
    return t('NO');
  }
}
