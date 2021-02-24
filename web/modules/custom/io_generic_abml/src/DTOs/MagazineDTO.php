<?php

namespace Drupal\io_generic_abml\DTOs;

use Drupal\io_generic_abml\DTOs\ArticleDTO;

class MagazineDTO extends ArticleDTO {
  /**
   * ID Table Primary key
   *
   * @var Integer
   */
  protected $idMagazine;

  /**
   * Magazine edition number
   *
   * @var Integer
   */
  protected $numero;

  public function setIdMagazine($idMagazine) {
    $this->idMagazine = $idMagazine;
  }
  public function getIdMagazine() {
    return $this->idMagazine;
  }

  public function setNumero($numero) {
    $this->numero = $numero;
  }
  public function getNumero() {
    return $this->numero;
  }

}
