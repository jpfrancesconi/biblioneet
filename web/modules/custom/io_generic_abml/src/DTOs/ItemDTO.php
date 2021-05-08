<?php

namespace Drupal\io_generic_abml\DTOs;

class ItemDTO extends GenericDTO {

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
   * Item type
   *
   * @var ItemTypeDTO
   */
  protected $itemType;

  /**
   * Item format
   *
   * @var ArticleFormatDTO
   */
  //protected $itemFormat;

  /**
   * Item Parallel Title
   *
   * @var String
   */
  protected $parallelTitle;

  /**
   * Item Edition
   *
   * @var String
   */
  protected $edition;

  /**
   * Item Publication place
   *
   * @var String
   */
  protected $publicationPlace;

  /**
   * Item Editorial DTO
   *
   * @var EditorialDTO
   */
  protected $editorial;

  /**
   * Item Publiation Year
   *
   * @var String
   */
  protected $publicationYear;

  /**
   * Item Extension
   *
   * @var String
   */
  protected $extension;

  /**
   * Item Dimensions
   *
   * @var String
   */
  protected $dimensions;

  /**
   * Item Other Physical Details
   *
   * @var String
   */
  protected $othersPhysicalDetails;

  /**
   * Item Complements
   *
   * @var String
   */
  protected $complements;

  /**
   * Item Serie Title
   *
   * @var String
   */
  protected $serieTitle;

  /**
   * Item Serie Number
   *
   * @var String
   */
  protected $serieNumber;

  /**
   * Item Notes
   *
   * @var String
   */
  protected $notes;

  /**
   * Item isbn
   *
   * @var String
   */
  protected $isbn;

  /**
   * Item issn
   *
   * @var String
   */
  protected $issn;

  /**
   * Item Acquisition Condition
   *
   * @var AcquisitionConditionDTO
   */
  protected $acquisitionCondition;

  /**
   * Item Acquisition Condition Notes
   *
   * @var String
   */
  protected $acquisitionConditionNotes;

  /** Getters and Setters ****************************************************/

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

  public function setItemType($itemType) {
    $this->itemType = $itemType;
  }
  public function getItemType() {
    return $this->itemType;
  }

  public function setParallelTitle($parallelTitle) {
    $this->parallelTitle = $parallelTitle;
  }
  public function getParallelTitle() {
    return $this->parallelTitle;
  }

  public function setEdition($edition) {
    $this->edition = $edition;
  }
  public function getEdition() {
    return $this->edition;
  }

  public function setPublicationPlace($publicationPlace) {
    $this->publicationPlace = $publicationPlace;
  }
  public function getPublicationPlace() {
    return $this->publicationPlace;
  }

  public function setEditorial($editorial) {
    $this->editorial = $editorial;
  }
  public function getEditorial() {
    return $this->editorial;
  }

  public function setPublicationYear($publicationYear) {
    $this->publicationYear = $publicationYear;
  }
  public function getPublicationYear() {
    return $this->publicationYear;
  }

  public function setExtension($extension) {
    $this->extension = $extension;
  }
  public function getExtension() {
    return $this->extension;
  }

  public function setDimensions($dimensions) {
    $this->dimensions = $dimensions;
  }
  public function getDimensions() {
    return $this->dimensions;
  }

  public function setOthersPhysicalDetails($othersPhysicalDetails) {
    $this->othersPhysicalDetails = $othersPhysicalDetails;
  }
  public function getOthersPhysicalDetails() {
    return $this->othersPhysicalDetails;
  }

  public function setComplements($complements) {
    $this->complements = $complements;
  }
  public function getComplements() {
    return $this->complements;
  }

  public function setSerieTitle($serieTitle) {
    $this->serieTitle = $serieTitle;
  }
  public function getSerieTitle() {
    return $this->serieTitle;
  }

  public function setSerieNumber($serieNumber) {
    $this->serieNumber = $serieNumber;
  }
  public function getSerieNumber() {
    return $this->serieNumber;
  }

  public function setNotes($notes) {
    $this->notes = $notes;
  }
  public function getNotes() {
    return $this->notes;
  }

  public function setIsbn($isbn) {
    $this->isbn = $isbn;
  }
  public function getIsbn() {
    return $this->isbn;
  }

  public function setIssn($issn) {
    $this->issn = $issn;
  }
  public function getIssn() {
    return $this->issn;
  }

  public function setAcquisitionCondition($acquisitionCondition) {
    $this->acquisitionCondition = $acquisitionCondition;
  }
  public function getAcquisitionCondition() {
    return $this->acquisitionCondition;
  }

  public function setAcquisitionConditionNotes($acquisitionConditionNotes) {
    $this->acquisitionConditionNotes = $acquisitionConditionNotes;
  }
  public function getAcquisitionConditionNotes() {
    return $this->acquisitionConditionNotes;
  }

}
