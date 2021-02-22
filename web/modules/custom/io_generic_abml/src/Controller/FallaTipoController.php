<?php

namespace Drupal\io_generic_abml\Controller;

use Drupal\Core\Url;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\io_generic_abml\DAOs\EquipoTiposDAO;
use Drupal\io_generic_abml\Form\FallaTipo\FallaTipoTableForm;
use Drupal\io_generic_abml\DAOs\FallaTipoDAO;

/**
 * Tipo de falla Controller class.
 *
 * This controller has everithing related to ABML from io_falla_tipo table.
 */
class FallaTipoController extends GenericABMLController {
  public function listAll($equipo_tipo_id) {
    //prepare a render array
    $content = [];

    // Add Title
    $content['title'] = [
      '#markup' => '<h3>Listado de Tipos de falla</h3>',
    ];

    // add falla_tipo search form
    $content['search_form'] =
    $this->formBuilder->getForm('Drupal\io_generic_abml\Form\FallaTipo\FallaTipoSearchForm');

    // get current search parameter on the request
    $search_key = $this->request->getCurrentRequest()->get('search');
    // load the Tipo de equipo related with the Tipo de Falla
    $equipoTipo = EquipoTiposDAO::load($equipo_tipo_id);

    // Add new record link
    $ajax_link_attributes = [
        'attributes' => [
          'class' => ['use-ajax', 'btn', 'btn-primary'],
          'data-dialog-type' => 'modal',
          'data-dialog-options' => ['width' => 700, 'height' => 400],
        ],
      ];
    $addUrl = Url::fromRoute('io_generic_abml.falla_tipo.add.getmodal', ['equipo_tipo_id' => $equipo_tipo_id, 'js' => 'ajax'], $ajax_link_attributes);
    $content['add_new_link'] = [
        '#type' => 'link',
        '#title' => 'Nuevo Tipo de falla',
        '#url' => $addUrl,
    ];

    $content['equipo_tipo'] = [
      '#markup' => '<span><p><strong>Tipo de equipo: </strong>' . $equipoTipo->getTipo() . '</p></span>'
    ];

    $entity_table_form_instance = new FallaTipoTableForm($this->db, $search_key);
    $content['table'] = $this->formBuilder->getForm($entity_table_form_instance, $equipo_tipo_id);
    $content['pager'] = [
      '#type' => 'pager',
    ];

    // attach library to open modals
    $content['#attached'] = ['library' => ['core/drupal.dialog.ajax']];

    $returnUrl = Url::fromRoute('io_generic_abml.equipo_tipo.list');
    $content['return_link'] = [
        '#type' => 'link',
        '#title' => 'Volver',
        '#url' => $returnUrl,
    ];
    return $content;
  }

  /**
   * Callback for openieng the Tipo de falla delete modal form.
   *
   * @param ine $falla_tipo_id
   *  Tipo de falla ID.
   */
  public function openDeleteModalForm($falla_tipo_id = NULL, $js) {
    $deleteForm = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\FallaTipo\FallaTipoDeleteForm', $falla_tipo_id);
    // Check if we have to use modal or not
    if($js == 'ajax') {
        $response = new AjaxResponse();
        $falla_tipo = FallaTipoDAO::load($falla_tipo_id);
        $response->addCommand(new OpenModalDialogCommand('Eliminar Tipo de falla: '.$falla_tipo->getFalla() , $deleteForm, ['width' => '800px']));
        return $response;
    } else {
        return $deleteForm;
    }
  }

  /**
   * Callback for opening the Tipo de falla add modal form.
   */
  public function getAddModalForm($equipo_tipo_id, $js) {
      $form = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\FallaTipo\FallaTipoForm', $equipo_tipo_id);
      // Check if we have to use modal or not
      if($js == 'ajax') {
          $response = new AjaxResponse();

          $response->addCommand(new OpenModalDialogCommand('Agregar Tipo de falla', $form, ['width' => '800px']));
          return $response;
      } else {
          return $form;
      }
  }

  /**
   * Callback for opening the Tipo de falla edit form in modal.
   */
  public function getEditModalForm($equipo_tipo_id, $falla_tipo_id = NULL, $js) {
      $actividadClasificacion = FallaTipoDAO::load($falla_tipo_id);
      $form = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\FallaTipo\FallaTipoForm', $equipo_tipo_id, $falla_tipo_id);
      // Check if we have to use modal or not
      if($js == 'ajax') {
          $response = new AjaxResponse();
          $response->addCommand(new OpenModalDialogCommand(t('Editar Tipo de falla: @name',
          ['@name' => $actividadClasificacion->getFalla()]), $form, ['width' => '800px']));
          return $response;
      } else {
          return $form;
      }
  }
}
