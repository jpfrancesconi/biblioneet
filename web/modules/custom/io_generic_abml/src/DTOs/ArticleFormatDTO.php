<?php

namespace Drupal\io_generic_abml\DTOs;

class ArticleFormatDTO extends GenericDTO {

  /**
   * ID Table Primary key
   *
   * @var Integer
   */
  protected $id;

  /**
   * Article format
   *
   * @var String
   */
  protected $format;

  /**
   * Article status
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

  public function setFormat($format) {
    $this->format = $format;
  }
  public function getFormat() {
    return $this->format;
  }

  public function setStatus($status) {
    $this->status = $status;
  }
  public function getStatus() {
    return $this->status;
  }
}