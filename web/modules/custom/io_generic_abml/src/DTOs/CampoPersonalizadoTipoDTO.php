<?php

namespace Drupal\io_generic_abml\DTOs;

use Drupal\io_generic_abml\DTOs\UsuarioDTO;
//use Drupal\io_generic_abml\DTOs\CampoPersonalizadoTipoDTO;

class CampoPersonalizadoTipoDTO extends GenericDTO {

    /**
     * ID (Clave Primaria)
     *
     * @var Integer
     */
    protected $id;

    /**
     * Tipo de campo del Campo Personalizado
     *
     * @var String
     */
    protected $tipoCampo;
    
    public function setId($id) {
        $this->id = $id;
    }
    public function getId() {
        return $this->id;
    }

    public function setTipoCampo($tipoCampo) {
        $this->tipoCampo = $tipoCampo;
    }
    public function getTipoCampo() {
        return $this->tipoCampo;
    }
}
