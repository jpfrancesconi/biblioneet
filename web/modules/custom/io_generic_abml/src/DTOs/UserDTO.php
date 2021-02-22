<?php

namespace Drupal\io_generic_abml\DTOs;

class UserDTO {

    /**
     * User id
     *
     * @var Intenger
     */
    private $uid;

    /**
     * Username
     *
     * @var String
     */
    private $username;


    public function setUid($uid) {
        $this->uid = $uid;
    }
    public function getUid() {
        return $this->uid;
    }

    public function setUsername($username) {
        $this->username = $username;
    }
    public function getUsername() {
        return $this->username;
    }
}
