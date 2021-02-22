<?php
namespace Drupal\io_generic_abml\DTOs;

use Drupal\io_generic_abml\DTOs\EquipoTipoDTO;

class FallaTipoDTO extends GenericDTO {
  /**
   * Id del tipo de daño (Clave Primaria)
   *
   */
  protected $id;
  /**
   * Nombre del tipo de daño
   *
   */
  protected $falla;
  /**
   * Tipo de equipo relacionado al tipo de falla
   *
   * @var \Drupal\io_generic_abml\DTOs\EquipoTipoDTO
   */
  protected $tipoEquipo;
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
  public function getFalla() {
    return $this->falla;
  }

  /**
   * Set nombre del tipo de daño
   */
  public function setFalla($falla) {
    $this->falla = $falla;
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

  /**
   * Get tipo de equipo relacionado al tipo de falla
   *
   * @return  \Drupal\io_generic_abml\DTOs\EquipoTipoDTO
   */
  public function getTipoEquipo()
  {
    return $this->tipoEquipo;
  }

  /**
   * Set tipo de equipo relacionado al tipo de falla
   *
   * @param  \Drupal\io_generic_abml\DTOs\EquipoTipoDTO
   *   $tipoEquipo  Tipo de equipo relacionado al tipo de falla
   */
  public function setTipoEquipo(EquipoTipoDTO $tipoEquipo)
  {
    $this->tipoEquipo = $tipoEquipo;
  }
}
