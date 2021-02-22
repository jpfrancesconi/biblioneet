<?php
namespace Drupal\io_generic_abml\DTOs;

class FallaCausaDTO extends GenericDTO {
  /**
   * Id del Causa de falla (Clave Primaria)
   *
   */
  protected $id;
  /**
   * Nombre del Causa de falla
   *
   */
  protected $causaFalla;
  /**
   * Estado de tipo de trabajo
   *
   */
  protected $activo;


  /**
   * Get id del Causa de falla (Clave Primaria)
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set id del Causa de falla (Clave Primaria)
   *
   */
  public function setId($id) {
    $this->id = $id;
  }

  /**
   * Get nombre del Causa de falla
   */
  public function getCausaFalla() {
    return $this->causaFalla;
  }

  /**
   * Set nombre del Causa de falla
   */
  public function setCausaFalla($causa_falla) {
    $this->causaFalla = $causa_falla;
  }

  /**
   * Get estado de tipo de trabajo
   */
  public function getActivo() {
    return $this->activo;
  }

  /**
   * Set estado de tipo de trabajo
   */
  public function setActivo($activo) {
    $this->activo = $activo;
  }

  /**
   * Get estado to show to the user
   */
  public function getActivoString()
  {
    if ($this->activo) {
      return t('SI');
    }
    return t('NO');
  }
}
