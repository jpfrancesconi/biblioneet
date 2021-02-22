<?php
namespace Drupal\io_generic_abml\DTOs;

class FrecuenciaFechaTipoDTO extends GenericDTO {
  /**
   * Id del Tipo de Frecuencia Fecha (Clave Primaria)
   *
   */
  protected $id;
  /**
   * Nombre del Tipo de Frecuencia Fecha
   *
   */
  protected $frecuencia;
  /**
   * Funcion con la que se calculara la siguiente fecha
   *
   */
  protected $funcionCalculo;
  /**
   * Parametro 1 de la funcion con la que se calculara la siguiente fecha
   *
   */
  protected $param_1;
  /**
   * Parametro 2 de la funcion con la que se calculara la siguiente fecha
   *
   */
  protected $param_2;
  /**
   * Parametro 3 de la funcion con la que se calculara la siguiente fecha
   *
   */
  protected $param_3;
  /**
   * Estado de Tipo de frecuencia fecha
   *
   */
  protected $activo;


  /**
   * Get id del Tipo de Frecuencia Fecha (Clave Primaria)
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set id del Tipo de Frecuencia Fecha (Clave Primaria)
   *
   */
  public function setId($id) {
    $this->id = $id;
  }

  /**
   * Get nombre del Tipo de Frecuencia Fecha
   */
  public function getFrecuencia() {
    return $this->frecuencia;
  }

  /**
   * Set nombre del Tipo de Frecuencia Fecha
   */
  public function setFrecuencia($tipo_trabajo) {
    $this->frecuencia = $tipo_trabajo;
  }

  /**
   * Get estado de Tipo de frecuencia fecha
   */
  public function getActivo() {
    return $this->activo;
  }

  /**
   * Set estado de Tipo de frecuencia fecha
   */
  public function setActivo($activo) {
    $this->activo = $activo;
  }

  /**
   * Get estado to show to the user
   */
  public function getActivoString()
  {
    if ($this->activo) {
      return t('SI');
    }
    return t('NO');
  }

  /**
   * Get funcion con la que se calculara la siguiente fecha
   */
  public function getFuncionCalculo() {
    return $this->funcionCalculo;
  }

  /**
   * Set funcion con la que se calculara la siguiente fecha
   */
  public function setFuncionCalculo($funcion_calculo) {
    $this->funcionCalculo = $funcion_calculo;
  }

  /**
   * Get parametro 1 de la funcion con la que se calculara la siguiente fecha
   */
  public function getParam_1() {
    return $this->param_1;
  }

  /**
   * Set parametro 1 de la funcion con la que se calculara la siguiente fecha
   */
  public function setParam_1($param_1) {
    $this->param_1 = $param_1;
  }

  /**
   * Get parametro 2 de la funcion con la que se calculara la siguiente fecha
   */
  public function getParam_2() {
    return $this->param_2;
  }

  /**
   * Set parametro 2 de la funcion con la que se calculara la siguiente fecha
   *
   */
  public function setParam_2($param_2) {
    $this->param_2 = $param_2;
  }

  /**
   * Get parametro 3 de la funcion con la que se calculara la siguiente fecha
   */
  public function getParam_3() {
    return $this->param_3;
  }

  /**
   * Set parametro 3 de la funcion con la que se calculara la siguiente fecha
   */
  public function setParam_3($param_3) {
    $this->param_3 = $param_3;
  }
}
