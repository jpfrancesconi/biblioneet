<?php

namespace Drupal\io_generic_abml\DTOs;

use Drupal\io_generic_abml\DTOs\UsuarioDTO;

class ProveedorDTO extends GenericDTO {

    /**
     * ID del tipo de Equipo (Clave Primaria)
     *
     * @var Integer
     */
    protected $id;

    /**
     * Nombre del Proveedor del Equipo
     *
     * @var String
     */
    protected $proveedor;

    /**
     * Web del Proveedor del Equipo
     *
     * @var String
     */
    protected $web;

    /**
     * Estado de Tipo de equipo
     * 
     * @var Boolean
     */
    protected $activo;

    public function setId($id) {
        $this->id = $id;
    }
    public function getId() {
        return $this->id;
    }

    public function setProveedor($proveedor) {
        $this->proveedor = $proveedor;
    }
    public function getProveedor() {
        return $this->proveedor;
    }

    public function setWeb($web) {
        $this->web = $web;
    }
    public function getWeb() {
        return $this->web;
    }

    /**
     * Get estado de Tipo de equipo
     */
    public function getActivo() {
        return $this->activo;
    }

    /**
     * Set estado de Tipo de equipo
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
