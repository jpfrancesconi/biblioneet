<?php

namespace Drupal\io_generic_abml\DTOs;

class BookDTO extends GenericDTO {

  /**
   * ID Table Primary key
   *
   * @var Integer
   */
  protected $id;

  /**
   * ISBN book code
   *
   * @var String
   */
  protected $isbn;

  /**
   * Book Editorial DTO
   *
   * @var EditorialDTO
   */
  protected $editorial;

  /**
   * Book title
   *
   * @var String
   */
  protected $titulo;

  /**
   * Book edition year
   *
   * @var Integer
   */
  protected $anioEdicion;

  /**
   * Book pages quantity
   *
   * @var Integer
   */
  protected $cantPaginas;

  /**
   * Book title
   *
   * @var String
   */
  protected $idioma;

  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }

  public function setIsbn($isbn) {
    $this->isbn = $isbn;
  }
  public function getIsbn() {
    return $this->isbn;
  }

  public function setEditorial($editorial) {
    $this->editorial = $editorial;
  }
  public function getEditorial() {
    return $this->editorial;
  }

  public function setTitulo($titulo) {
    $this->titulo = $titulo;
  }
  public function getTitulo() {
    return $this->titulo;
  }

  public function setAnioEdicion($anioEdicion) {
    $this->anioEdicion = $anioEdicion;
  }
  public function getAnioEdicion() {
    return $this->anioEdicion;
  }

  public function setCantPaginas($cantPaginas) {
    $this->cantPaginas = $cantPaginas;
  }
  public function getCantPaginas() {
    return $this->cantPaginas;
  }

  public function setIdioma($idioma) {
    $this->idioma = $idioma;
  }
  public function getIdioma() {
    return $this->idioma;
  }
}
