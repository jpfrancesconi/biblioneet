<?php
namespace Drupal\io_generic_abml\DTOs;

class TrabajoTipoDTO extends GenericDTO {
  /**
   * Id del Tipo de trabajo (Clave Primaria)
   *
   */
  protected $id;
  /**
   * Nombre del Tipo de trabajo
   *
   */
  protected $tipoTrabajo;
  /**
   * Estado de tipo de trabajo
   *
   */
  protected $activo;


  /**
   * Get id del Tipo de trabajo (Clave Primaria)
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set id del Tipo de trabajo (Clave Primaria)
   *
   */
  public function setId($id) {
    $this->id = $id;
  }

  /**
   * Get nombre del Tipo de trabajo
   */
  public function getTipoTrabajo() {
    return $this->tipoTrabajo;
  }

  /**
   * Set nombre del Tipo de trabajo
   */
  public function setTipoTrabajo($tipo_trabajo) {
    $this->tipoTrabajo = $tipo_trabajo;
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
