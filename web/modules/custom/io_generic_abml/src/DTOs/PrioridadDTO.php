<?php
namespace Drupal\io_generic_abml\DTOs;

class PrioridadDTO extends GenericDTO {
  /**
   * Id de la Prioridad (Clave Primaria)
   *
   */
  protected $id;
  /**
   * Nombre de la prioridad
   *
   */
  protected $prioridad;
  /**
   * Estado de Tipo de frecuencia fecha
   *
   */
  protected $activo;

  /**
   * Get id de la Prioridad (Clave Primaria)
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set id de la Prioridad (Clave Primaria)
   *
   */
  public function setId($id) {
    $this->id = $id;
  }

  /**
   * Get nombre de la prioridad
   */
  public function getPrioridad() {
    return $this->prioridad;
  }

  /**
   * Set nombre de la prioridad
   */
  public function setPrioridad($prioridad) {
    $this->prioridad = $prioridad;
  }

  /**
   * Get estado de Tipo de frecuencia fecha
   */
  public function getActivo() {
      return $this->activo;
  }

  /**
   * Set estado de Tipo de frecuencia fecha
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
