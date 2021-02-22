<?php

namespace Drupal\io_generic_abml\DTOs;

use Drupal\io_generic_abml\DTOs\UsuarioDTO;

abstract class GenericDTO {

    /**
     * Auditory Field: Record creator
     *
     * @var \Drupal\io_generic_abml\DTOs\Usuario\UsuarioDTO
     */
    protected $createdBy;

    /**
     * Auditory Field: Record creation datetime
     *
     * @var \Date
     */
    protected $createdOn;

    /**
     * Auditory Field: Record last updater
     *
     * @var \Drupal\io_generic_abml\DTOs\Usuario\UsuarioDTO
     */
    protected $updatedBy;

    /**
     * Auditory Field: Record last update datetime
     *
     * @var \Date
     */
    protected $updatedOn;

    public function setCreatedBy($createdBy) {
        $this->createdBy = $createdBy;
    }
    public function getCreatedBy() {
        return $this->createdBy;
    }

    public function setCreatedOn($createdOn) {
        $this->createdOn = $createdOn;
    }
    public function getCreatedOn() {
        $date = date_create($this->createdOn);
        return $date->format('d/m/Y H:i');
    }

    public function setUpdatedBy($updatedBy) {
        $this->updatedBy = $updatedBy;
    }
    public function getUpdatedBy() {
        return $this->updatedBy;
    }

    public function setUpdatedOn($updatedOn) {
        $this->updatedOn = $updatedOn;
    }
    public function getUpdatedOn() {
        if(isset($this->updatedOn)) {
            $date = date_create($this->updatedOn);
            return $date->format('d/m/Y H:i');
        } else {
            return null;
        }

    }
}
