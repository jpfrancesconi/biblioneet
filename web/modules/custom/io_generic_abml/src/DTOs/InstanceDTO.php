<?php

namespace Drupal\io_generic_abml\DTOs;

class InstanceDTO extends GenericDTO {

    /**
     * ID Table Primary key
     *
     * @var Integer
     */
    protected $id;

    /**
     * Inv Code name
     *
     * @var String
     */
    protected $invCode;

    /**
     * Signature field
     *
     * @var String
     */
    protected $signature;

    /**
     * item_id field
     *
     * @var itemDTO
     */
    protected $item;

    /**
     * instance_status_id field
     *
     * @var instanceStatusDTO
     */
    protected $instanceStatus;

    public function setId($id) {
        $this->id = $id;
    }
    public function getId() {
        return $this->id;
    }

    public function setInvCode($invCode) {
        $this->invCode = $invCode;
    }
    public function getInvCode() {
        return $this->invCode;
    }

    public function setSignature($signature) {
        $this->signature = $signature;
    }
    public function getSignature() {
        return $this->signature;
    }

    public function setItem($item) {
        $this->item = $item;
    }
    public function getItem() {
        return $this->item;
    }

    public function setInstanceStatus($instanceStatus) {
        $this->instanceStatus = $instanceStatus;
    }
    public function getInstanceStatus() {
        return $this->instanceStatus;
    }
    
}