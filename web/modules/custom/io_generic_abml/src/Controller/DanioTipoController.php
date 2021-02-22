<?php

namespace Drupal\io_generic_abml\Controller;

use Drupal;
use Drupal\Core\Url;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;

use Drupal\io_generic_abml\Form\DanioTipo\DanioTipoTableForm;
use Drupal\io_generic_abml\DAOs\DanioTipoDAO;


use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Tipo de daño Controller class.
 *
 * This controller has everithing related to ABML from io_danio_tipo table.
 */
class DanioTipoController extends GenericABMLController {
  public function listAll() {
    //prepare a render array
    $content = [];

    // Add Title
    $content['title'] = [
      '#markup' => '<h3>Listado de Tipos de daño</h3>',
    ];

    // add danio_tipo search form
    $content['search_form'] =
    $this->formBuilder->getForm('Drupal\io_generic_abml\Form\DanioTipo\DanioTipoSearchForm');

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
    $addUrl = Url::fromRoute('io_generic_abml.danio_tipo.add.getmodal', ['js' => 'ajax'], $ajax_link_attributes);
    $content['add_new_link'] = [
        '#type' => 'link',
        '#title' => 'Nuevo Tipo de daño',
        '#url' => $addUrl,
    ];

    $entity_table_form_instance = new DanioTipoTableForm($this->db, $search_key);
    $content['table'] = $this->formBuilder->getForm($entity_table_form_instance);
    $content['pager'] = [
      '#type' => 'pager',
    ];


    // attach library to open modals
    $content['#attached'] = ['library' => ['core/drupal.dialog.ajax']];

    return $content;
  }

  /**
   * Callback for openieng the Tipo de daño delete modal form.
   *
   * @param ine $danio_tipo_id
   *  Tipo de daño ID.
   */
  public function openDeleteModalForm($danio_tipo_id = NULL, $js) {
    $deleteForm = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\DanioTipo\DanioTipoDeleteForm', $danio_tipo_id);
    // Check if we have to use modal or not
    if($js == 'ajax') {
        $response = new AjaxResponse();
        $danio_tipo = DanioTipoDAO::load($danio_tipo_id);
        $response->addCommand(new OpenModalDialogCommand('Eliminar Tipo de daño: '.$danio_tipo->getDanio() , $deleteForm, ['width' => '800px']));
        return $response;
    } else {
        return $deleteForm;
    }
  }

  /**
   * Callback for opening the Tipo de daño add modal form.
   */
  public function getAddModalForm($js) {
      $form = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\DanioTipo\DanioTipoForm');
      // Check if we have to use modal or not
      if($js == 'ajax') {
          $response = new AjaxResponse();

          $response->addCommand(new OpenModalDialogCommand('Agregar Tipo de daño', $form, ['width' => '800px']));
          return $response;
      } else {
          return $form;
      }
  }

  /**
   * Callback for opening the Tipo de daño edit form in modal.
   */
  public function getEditModalForm($danio_tipo_id = NULL, $js) {
      $actividadClasificacion = DanioTipoDAO::load($danio_tipo_id);
      $form = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\DanioTipo\DanioTipoForm', $danio_tipo_id);
      // Check if we have to use modal or not
      if($js == 'ajax') {
          $response = new AjaxResponse();
          $response->addCommand(new OpenModalDialogCommand(t('Editar Tipo de daño: @name',
          ['@name' => $actividadClasificacion->getDanio()]), $form, ['width' => '800px']));
          return $response;
      } else {
          return $form;
      }
  }
}
