<?php
namespace Drupal\io_generic_abml\DTOs;

class UnidadMedidaDTO extends GenericDTO {
  /**
   * Id de la Unidad de medida (Clave Primaria)
   *
   */
  protected $id;
  /**
   * Nombre de la unidad de medida
   *
   */
  protected $unidadMedida;


  /**
   * Get id de la Unidad de medida (Clave Primaria)
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set id de la Unidad de medida (Clave Primaria)
   *
   */
  public function setId($id) {
    $this->id = $id;
  }

  /**
   * Get nombre de la Unidad de medida
   */
  public function getUnidadMedida() {
    return $this->unidadMedida;
  }

  /**
   * Set nombre de la Unidad de medida
   */
  public function setUnidadMedida($unidad_medida) {
    $this->unidadMedida = $unidad_medida;
  }
}
