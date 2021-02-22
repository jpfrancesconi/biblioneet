<?php

namespace Drupal\io_generic_abml\DTOs;

use Drupal\io_generic_abml\DTOs\NationalityDTO;

class AuthorDTO extends GenericDTO {

  /**
   * ID del tipo de Equipo (Clave Primaria)
   *
   * @var Integer
   */
  protected $id;

  /**
   * Author first name
   *
   * @var String
   */
  protected $firstName;

  /**
   * Author last name
   *
   * @var String
   */
  protected $lastName;

  /**
   * Author description
   *
   * @var String
   */
  protected $description;

  /**
   * Author picture
   *
   * @var Integer
   */
  protected $picture;

  /**
   * Author Nationality
   *
   * @var NationalityDTO
   */
  protected $nationality;

  /**
   * Author status
   *
   */
  protected $status;

  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }

  public function setfirstName($firstName) {
    $this->firstName = $firstName;
  }
  public function getFirstName() {
    return $this->firstName;
  }

  public function setLastName($lastName) {
    $this->lastName = $lastName;
  }
  public function getLastName() {
    return $this->lastName;
  }

  public function setDescription($description) {
    $this->description = $description;
  }
  public function getDescription() {
    return $this->description;
  }

  public function setPicture($picture) {
    $this->picture = $picture;
  }
  public function getPicture() {
    return $this->picture;
  }

  public function setNationality($nationality) {
    $this->nationality = $nationality;
  }
  public function getNationality() {
    return $this->nationality;
  }

  /**
   * Get Author status
   */
  public function getStatus() {
    return $this->status;
  }

  /**
   * Set Author status
   */
  public function setStatus($status) {
    $this->status = $status;
  }

  /**
   * Get estado to show to the user
   */
  public function getStatusString() {
    if ($this->status) {
      return t('SI');
    }
    return t('NO');
  }
}
