<?php

namespace Drupal\io_generic_abml\Controller;

use Drupal;
use Drupal\Core\Url;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;

use Drupal\io_generic_abml\Form\TrabajoTipo\TrabajoTipoTableForm;
use Drupal\io_generic_abml\DAOs\TrabajoTipoDAO;


use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Tipo de trabajo Controller class.
 *
 * This controller has everithing related to ABML from io_trabajo_tipo table.
 */
class TrabajoTipoController extends GenericABMLController {
  public function listAll() {
    //prepare a render array
    $content = [];

    // Add Title
    $content['title'] = [
      '#markup' => '<h3>Listado de Tipos de trabajo</h3>',
    ];

    // add trabajo_tipo search form
    $content['search_form'] =
    $this->formBuilder->getForm('Drupal\io_generic_abml\Form\TrabajoTipo\TrabajoTipoSearchForm');

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
    $addUrl = Url::fromRoute('io_generic_abml.trabajo_tipo.add.getmodal', ['js' => 'ajax'], $ajax_link_attributes);
    $content['add_new_link'] = [
        '#type' => 'link',
        '#title' => 'Nuevo Tipo de trabajo',
        '#url' => $addUrl,
    ];

    $entity_table_form_instance = new TrabajoTipoTableForm($this->db, $search_key);
    $content['table'] = $this->formBuilder->getForm($entity_table_form_instance);
    $content['pager'] = [
      '#type' => 'pager',
    ];


    // attach library to open modals
    $content['#attached'] = ['library' => ['core/drupal.dialog.ajax']];

    return $content;
  }

  /**
   * Callback for openieng the Tipo de trabajo delete modal form.
   *
   * @param ine $trabajo_tipo_id
   *  Tipo de trabajo ID.
   */
  public function openDeleteModalForm($trabajo_tipo_id = NULL, $js) {
    $deleteForm = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\TrabajoTipo\TrabajoTipoDeleteForm', $trabajo_tipo_id);
    // Check if we have to use modal or not
    if($js == 'ajax') {
        $response = new AjaxResponse();
        $trabajo_tipo = TrabajoTipoDAO::load($trabajo_tipo_id);
        $response->addCommand(new OpenModalDialogCommand('Eliminar Tipo de trabajo: '.$trabajo_tipo->getTipoTrabajo() , $deleteForm, ['width' => '800px']));
        return $response;
    } else {
        return $deleteForm;
    }
  }

  /**
   * Callback for opening the Tipo de trabajo add modal form.
   */
  public function getAddModalForm($js) {
      $form = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\TrabajoTipo\TrabajoTipoForm');
      // Check if we have to use modal or not
      if($js == 'ajax') {
          $response = new AjaxResponse();

          $response->addCommand(new OpenModalDialogCommand('Agregar Tipo de trabajo', $form, ['width' => '800px']));
          return $response;
      } else {
          return $form;
      }
  }

  /**
   * Callback for opening the Tipo de trabajo edit form in modal.
   */
  public function getEditModalForm($trabajo_tipo_id = NULL, $js) {
      $actividadClasificacion = TrabajoTipoDAO::load($trabajo_tipo_id);
      $form = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\TrabajoTipo\TrabajoTipoForm', $trabajo_tipo_id);
      // Check if we have to use modal or not
      if($js == 'ajax') {
          $response = new AjaxResponse();
          $response->addCommand(new OpenModalDialogCommand(t('Editar Tipo de trabajo: @name',
          ['@name' => $actividadClasificacion->getTipoTrabajo()]), $form, ['width' => '800px']));
          return $response;
      } else {
          return $form;
      }
  }
}
