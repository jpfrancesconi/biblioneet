<?php
namespace Drupal\io_generic_abml\DTOs;

class EquipoClasificacionDTO extends GenericDTO {
  /**
   * Id de la Clasificacion de actividad (Clave Primaria)
   *
   */
  protected $id;
  /**
   * Nombre de la Clasificacion de actividad
   *
   */
  protected $clasificacion;
  /**
   * Estado del Clasificacion de actividad
   *
   */
  protected $activo;


  /**
   * Get id de la Clasificacion de actividad (Clave Primaria)
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set id de la Clasificacion de actividad (Clave Primaria)
   *
   */
  public function setId($id) {
    $this->id = $id;
  }

  /**
   * Get nombre de la Clasificacion de actividad
   */
  public function getClasificacion() {
    return $this->clasificacion;
  }

  /**
   * Set nombre de la Clasificacion de actividad
   */
  public function setClasificacion($clasificacion) {
    $this->clasificacion = $clasificacion;
  }

  /**
   * Get estado de Clasificacion de actividad
   */
  public function getActivo() {
      return $this->activo;
  }

  /**
   * Set estado de Clasificacion de actividad
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
