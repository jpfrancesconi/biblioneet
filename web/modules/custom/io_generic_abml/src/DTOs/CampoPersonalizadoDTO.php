<?php

namespace Drupal\io_generic_abml\DTOs;

use Drupal\io_generic_abml\DTOs\UsuarioDTO;
use Drupal\io_generic_abml\DTOs\CampoPersonalizadoTipoDTO;

class CampoPersonalizadoDTO extends GenericDTO {

    /**
     * ID (Clave Primaria)
     *
     * @var Integer
     */
    protected $id;

    /**
     * Etiqueta del Campo Personalizado
     *
     * @var String
     */
    protected $etiqueta;

    /**
     * Campo Personalizado Tipo
     *
     * @var CampoPersonalizadoTipoDTO
     */
    protected $campoPersonalizadoTipo;
    
    public function setId($id) {
        $this->id = $id;
    }
    public function getId() {
        return $this->id;
    }

    public function setEtiqueta($etiqueta) {
        $this->etiqueta = $etiqueta;
    }
    public function getEtiqueta() {
        return $this->etiqueta;
    }

    public function setCampoPersonalizadoTipo($campoPersonalizadoTipo) {
        $this->campoPersonalizadoTipo = $campoPersonalizadoTipo;
    }
    public function getCampoPersonalizadoTipo() {
        return $this->campoPersonalizadoTipo;
    }
}
