<?php
namespace Drupal\io_generic_abml\DTOs;

class DanioTipoDTO extends GenericDTO {
  /**
   * Id del tipo de daño (Clave Primaria)
   *
   */
  protected $id;
  /**
   * Nombre del tipo de daño
   *
   */
  protected $danio;
  /**
   * Estado de tipo de daño
   *
   */
  protected $activo;


  /**
   * Get id del tipo de daño (Clave Primaria)
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set id del tipo de daño (Clave Primaria)
   *
   */
  public function setId($id) {
    $this->id = $id;
  }

  /**
   * Get nombre del tipo de daño
   */
  public function getDanio() {
    return $this->danio;
  }

  /**
   * Set nombre del tipo de daño
   */
  public function setDanio($danio) {
    $this->danio = $danio;
  }

  /**
   * Get estado de tipo de daño
   */
  public function getActivo()
  {
    return $this->activo;
  }

  /**
   * Set estado de tipo de daño
   */
  public function setActivo($activo)
  {
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
