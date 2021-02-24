<?php

namespace Drupal\io_generic_abml\DTOs;

class ArticleDTO extends GenericDTO {

  /**
   * ID Table Primary key
   *
   * @var Integer
   */
  protected $id;

  /**
   * Title
   *
   * @var String
   */
  protected $title;

  /**
   * Cover photo
   *
   * @var Integer
   */
  protected $cover;

  /**
   * Article inventary code 
   *
   * @var String
   */
  protected $invCode;

  /**
   * Article type
   *
   * @var ArticleTypeDTO
   */
  protected $articleType;

  /**
   * Article format
   *
   * @var ArticleFormatDTO
   */
  protected $articleFormat;

  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }

  public function setTitle($title) {
    $this->title = $title;
  }
  public function getTitle() {
    return $this->title;
  }

  public function setCover($cover) {
    $this->cover = $cover;
  }
  public function getCover() {
    return $this->cover;
  }

  public function setInvCode($invCode) {
    $this->invCode = $invCode;
  }
  public function getInvCode() {
    return $this->invCode;
  }

  public function setArticleType($articleType) {
    $this->articleType = $articleType;
  }
  public function getArticleType() {
    return $this->articleType;
  }

  public function setArticleFormat($articleFormat) {
    $this->articleFormat = $articleFormat;
  }
  public function getArticleFormat() {
    return $this->articleFormat;
  }
}
