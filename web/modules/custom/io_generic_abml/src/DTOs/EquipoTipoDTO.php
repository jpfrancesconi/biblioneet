<?php

namespace Drupal\io_generic_abml\DTOs;

use Drupal\io_generic_abml\DTOs\UsuarioDTO;

class EquipoTipoDTO extends GenericDTO {

    /**
     * ID del tipo de Equipo (Clave Primaria)
     *
     * @var Integer
     */
    protected $id;

    /**
     * Nombre del tipo de Equipo
     *
     * @var String
     */
    protected $tipo;
    /**
     * Estado de Tipo de equipo
     *
     */
    protected $activo;

    public function setId($id) {
        $this->id = $id;
    }
    public function getId() {
        return $this->id;
    }

    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }
    public function getTipo() {
        return $this->tipo;
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
