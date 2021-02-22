<?php

namespace Drupal\io_generic_abml\DTOs;

/**
 * Nationality DTO - Table: bn_countries
 */
class NationalityDTO {

  /**
   * id
   *
   * @var Intenger
   */
  private $id;

  /**
   * alpha_2_code
   *
   * @var String
   */
  private $alpha2Code;

  /**
   * alpha_3_code
   *
   * @var String
   */
  private $alpha3Code;

  /**
   * en_short_name
   *
   * @var String
   */
  private $enShortName;

  /**
   * nationality
   *
   * @var String
   */
  private $nationality;

  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }

  public function setAlpha2Code($alpha2Code) {
    $this->alpha2Code = $alpha2Code;
  }
  public function getAlpha2Code() {
    return $this->alpha2Code;
  }

  public function setAlpha3Code($alpha3Code) {
    $this->alpha3Code = $alpha3Code;
  }
  public function getAlpha3Code() {
    return $this->alpha3Code;
  }

  public function setEnShortName($enShortName) {
    $this->enShortName = $enShortName;
  }
  public function getEnShortName() {
    return $this->enShortName;
  }

  public function setNationality($nationality) {
    $this->nationality = $nationality;
  }
  public function getNationality() {
    return $this->nationality;
  }
}
