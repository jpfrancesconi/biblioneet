<?php

namespace Drupal\io_generic_abml\DTOs;

class ClasificationDTO extends GenericDTO {

  /**
   * ID Table Primary key
   *
   * @var Integer
   */
  protected $id;

  /**
   * Clasification code
   *
   * @var String
   */
  protected $code;

    /**
   * Clasification materia
   *
   * @var String
   */
  protected $materia;

  /**
   * Clasificacione status
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

  public function setMateria($materia) {
    $this->materia = $materia;
  }
  public function getMateria() {
    return $this->materia;
  }

  public function setCode($code) {
    $this->code = $code;
  }
  public function getCode() {
    return $this->code;
  }

  /**
   * Get estado
   */
  public function getStatus() {
    return $this->status;
  }
  /**
   * Set estado
   */
  public function setStatus($status) {
    $this->status = $status;
  }
  /**
   * Get estado to show to the user
   */
  public function getStatusString() {
    if ($this->activo) {
      return t('SI');
    }
    return t('NO');
  }
}
