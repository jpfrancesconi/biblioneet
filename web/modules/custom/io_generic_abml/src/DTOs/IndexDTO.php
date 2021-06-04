<?php

namespace Drupal\io_generic_abml\DTOs;

class IndexDTO extends GenericDTO {
    /**
     * ID de la localizacion (Clave Primaria)
     *
     * @var Integer
     */
    protected $id;

    /**
     * Text content
     *
     * @var String
     */
    protected $content;

    /**
     * Number
     *
     * @var String
     */
    protected $number;

    /**
     * Nombre del index padre
     *
     * @var IndexDTO
     */
    protected $indexPadre;

    /**
     * Item al que pertenece el indice
     *
     * @var ItemDTO
     */
    protected $item;

    /**
     * peso de la localizacion (determina la ubicacion entre hermanos)
     *
     * @var Integer
     */
    protected $peso;

    public function setId($id) {
        $this->id = $id;
    }
    public function getId() {
        return $this->id;
    }

    public function setContent($content) {
        $this->content = $content;
    }
    public function getContent() {
        return $this->content;
    }

    public function setNumber($number) {
        $this->number = $number;
    }
    public function getNumber() {
        return $this->number;
    }

    public function setIndexPadre($indexPadre) {
        $this->indexPadre = $indexPadre;
    }
    public function getIndexPadre() {
        return $this->indexPadre;
    }

    public function setItem($item) {
        $this->item = $item;
    }
    public function getItem() {
        return $this->item;
    }

    public function setPeso($peso) {
        $this->peso = $peso;
    }
    public function getPeso() {
        return $this->peso;
    }    
}