<?php
namespace Drupal\io_generic_abml\DTOs;

class RegimenTipoDTO extends GenericDTO {
  /**
   * Id del Tipo de regimen (Clave Primaria)
   *
   */
  protected $id;
  /**
   * Nombre del Tipo de regimen
   *
   */
  protected $tipoRegimen;
  /**
   * Estado del Tipo de regimen
   *
   */
  protected $activo;

  /**
   * Get id del Tipo de regimen (Clave Primaria)
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set id del Tipo de regimen (Clave Primaria)
   *
   */
  public function setId($id) {
    $this->id = $id;
  }

  /**
   * Get nombre del Tipo de regimen
   */
  public function getTipoRegimen() {
    return $this->tipoRegimen;
  }

  /**
   * Set nombre del Tipo de regimen
   */
  public function setTipoRegimen($tipo_regimen) {
    $this->tipoRegimen = $tipo_regimen;
  }

  /**
   * Get estado de Tipo de regimen
   */
  public function getActivo() {
      return $this->activo;
  }

  /**
   * Set estado de Tipo de regimen
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
