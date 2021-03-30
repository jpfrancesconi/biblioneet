<?php

namespace Drupal\io_generic_abml\DTOs;

use Drupal\io_generic_abml\DTOs\ArticleDTO;

class MultimediaDTO extends ArticleDTO {
  /**
   * ID Table Primary key
   *
   * @var Integer
   */
  protected $idMultimedia;

  /**
   * Multimedia descrition field
   *
   * @var String
   */
  protected $description;

  public function setIdMultimedia($idMultimedia) {
    $this->idMultimedia = $idMultimedia;
  }
  public function getIdMultimedia() {
    return $this->idMultimedia;
  }

  public function setDescription($description) {
    $this->description = $description;
  }
  public function getDescription() {
    return $this->description;
  }
}
