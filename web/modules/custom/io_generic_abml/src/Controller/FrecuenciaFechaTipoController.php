<?php

namespace Drupal\io_generic_abml\Controller;

use Drupal\Core\Url;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;

use Drupal\io_generic_abml\Form\FrecuenciaFechaTipo\FrecuenciaFechaTipoTableForm;
use Drupal\io_generic_abml\DAOs\FrecuenciaFechaTipoDAO;

/**
 * Tipo de frecuencia de fecha Controller class.
 *
 * This controller has everithing related to ABML from io_frecuencia_fecha_tipo table.
 */
class FrecuenciaFechaTipoController extends GenericABMLController {
  public function listAll() {
    //prepare a render array
    $content = [];

    // Add Title
    $content['title'] = [
      '#markup' => '<h3>Listado de Tipos de Frecuencias de fecha</h3>',
    ];

    // add frecuencia_fecha_tipo search form
    $content['search_form'] =
    $this->formBuilder->getForm('Drupal\io_generic_abml\Form\FrecuenciaFechaTipo\FrecuenciaFechaTipoSearchForm');

    // get current search parameter on the request
    $search_key = $this->request->getCurrentRequest()->get('search');

    // Add new record link
    $ajax_link_attributes = [
        'attributes' => [
          'class' => ['use-ajax', 'btn', 'btn-primary'],
          'data-dialog-type' => 'modal',
          'data-dialog-options' => ['width' => 700, 'height' => 400],
        ],
      ];
    $addUrl = Url::fromRoute('io_generic_abml.frecuencia_fecha_tipo.add.getmodal', ['js' => 'ajax'], $ajax_link_attributes);
    $content['add_new_link'] = [
        '#type' => 'link',
        '#title' => 'Nuevo Tipo de frecuencia de fecha',
        '#url' => $addUrl,
    ];

    $entity_table_form_instance = new FrecuenciaFechaTipoTableForm($this->db, $search_key);
    $content['table'] = $this->formBuilder->getForm($entity_table_form_instance);
    $content['pager'] = [
      '#type' => 'pager',
    ];


    // attach library to open modals
    $content['#attached'] = ['library' => ['core/drupal.dialog.ajax']];

    return $content;
  }

  /**
   * Callback for openieng the Tipo de frecuencia de fecha delete modal form.
   *
   * @param ine $frecuencia_fecha_tipo_id
   *  Tipo de frecuencia de fecha ID.
   */
  public function openDeleteModalForm($frecuencia_fecha_tipo_id = NULL, $js) {
    $deleteForm = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\FrecuenciaFechaTipo\FrecuenciaFechaTipoDeleteForm', $frecuencia_fecha_tipo_id);
    // Check if we have to use modal or not
    if($js == 'ajax') {
        $response = new AjaxResponse();
        $frecuencia_fecha_tipo = FrecuenciaFechaTipoDAO::load($frecuencia_fecha_tipo_id);
        $response->addCommand(new OpenModalDialogCommand('Eliminar Tipo de frecuencia de fecha: '.$frecuencia_fecha_tipo->getFrecuencia() , $deleteForm, ['width' => '800px']));
        return $response;
    } else {
        return $deleteForm;
    }
  }

  /**
   * Callback for opening the Tipo de frecuencia de fecha add modal form.
   */
  public function getAddModalForm($js) {
      $form = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\FrecuenciaFechaTipo\FrecuenciaFechaTipoForm');
      // Check if we have to use modal or not
      if($js == 'ajax') {
          $response = new AjaxResponse();

          $response->addCommand(new OpenModalDialogCommand('Agregar Tipo de frecuencia de fecha', $form, ['width' => '800px']));
          return $response;
      } else {
          return $form;
      }
  }

  /**
   * Callback for opening the Tipo de frecuencia de fecha edit form in modal.
   */
  public function getEditModalForm($frecuencia_fecha_tipo_id = NULL, $js) {
      $actividadClasificacion = FrecuenciaFechaTipoDAO::load($frecuencia_fecha_tipo_id);
      $form = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\FrecuenciaFechaTipo\FrecuenciaFechaTipoForm', $frecuencia_fecha_tipo_id);
      // Check if we have to use modal or not
      if($js == 'ajax') {
          $response = new AjaxResponse();
          $response->addCommand(new OpenModalDialogCommand(t('Editar Tipo de frecuencia de fecha: @name',
          ['@name' => $actividadClasificacion->getFrecuencia()]), $form, ['width' => '800px']));
          return $response;
      } else {
          return $form;
      }
  }
}
