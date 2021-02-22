<?php
namespace Drupal\io_generic_abml\DTOs;

class ContactoTipoDTO extends GenericDTO {
  /**
   * Id del Tipo de contacto (Clave Primaria)
   *
   */
  protected $id;
  /**
   * Nombre del Tipo de contacto
   *
   */
  protected $tipoContacto;
  /**
   * Estado de tipo de contacto
   *
   */
  protected $activo;


  /**
   * Get id del Tipo de contacto (Clave Primaria)
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set id del Tipo de contacto (Clave Primaria)
   *
   */
  public function setId($id) {
    $this->id = $id;
  }

  /**
   * Get nombre del Tipo de contacto
   */
  public function getTipoContacto() {
    return $this->tipoContacto;
  }

  /**
   * Set nombre del Tipo de contacto
   */
  public function setTipoContacto($tipo_contacto) {
    $this->tipoContacto = $tipo_contacto;
  }

  /**
   * Get estado de tipo de contacto
   */
  public function getActivo() {
    return $this->activo;
  }

  /**
   * Set estado de tipo de contacto
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
