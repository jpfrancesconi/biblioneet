<?php

namespace Drupal\io_generic_abml\DTOs;

class InstanceStatusDTO extends GenericDTO {

    /**
     * ID Table Primary key
     *
     * @var Integer
     */
    protected $id;

    /**
     * Status name
     *
     * @var String
     */
    protected $statusName;

    /**
     * Lendable status
     *
     * @var Boolean
     */
    protected $lendable;

    /**
     * Instance Status status
     *
     * @var Boolean
     */
    protected $status;

    public function setId($id) {
        $this->id = $id;
    }
    public function getId() {
        return $this->id;
    }

    public function setStatusName($statusName) {
        $this->statusName = $statusName;
    }
    public function getStatusName() {
        return $this->statusName;
    }

    /**
     * Get lendable
     */
    public function getLendable() {
        return $this->lendable;
    }
    /**
     * Set lendable
     */
    public function setLendable($lendable) {
        $this->lendable = $lendable;
    }

    /**
     * Get estado
     */
    public function getStatus() {
        return $this->status;
    }
    /**
     * Set estado
     */
    public function setStatus($status) {
        $this->status = $status;
    }
    /**
     * Get estado to show to the user
     */
    public function getStatusString() {
        if ($this->activo) {
        return t('SI');
        }
        return t('NO');
    }
}