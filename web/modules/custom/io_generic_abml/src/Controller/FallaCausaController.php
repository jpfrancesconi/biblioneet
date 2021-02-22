<?php

namespace Drupal\io_generic_abml\Controller;

use Drupal\Core\Url;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;

use Drupal\io_generic_abml\Form\FallaCausa\FallaCausaTableForm;
use Drupal\io_generic_abml\DAOs\FallaCausaDAO;

/**
 * Causa de falla Controller class.
 *
 * This controller has everithing related to ABML from io_falla_causa table.
 */
class FallaCausaController extends GenericABMLController {
  public function listAll() {
    //prepare a render array
    $content = [];

    // Add Title
    $content['title'] = [
      '#markup' => '<h3>Listado de Causas de falla</h3>',
    ];

    // add falla_causa search form
    $content['search_form'] =
    $this->formBuilder->getForm('Drupal\io_generic_abml\Form\FallaCausa\FallaCausaSearchForm');

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
    $addUrl = Url::fromRoute('io_generic_abml.falla_causa.add.getmodal', ['js' => 'ajax'], $ajax_link_attributes);
    $content['add_new_link'] = [
        '#type' => 'link',
        '#title' => 'Nueva Causa de falla',
        '#url' => $addUrl,
    ];

    $entity_table_form_instance = new FallaCausaTableForm($this->db, $search_key);
    $content['table'] = $this->formBuilder->getForm($entity_table_form_instance);
    $content['pager'] = [
      '#type' => 'pager',
    ];


    // attach library to open modals
    $content['#attached'] = ['library' => ['core/drupal.dialog.ajax']];

    return $content;
  }

  /**
   * Callback for openieng the Causa de falla delete modal form.
   *
   * @param ine $falla_causa_id
   *  Causa de falla ID.
   */
  public function openDeleteModalForm($falla_causa_id = NULL, $js) {
    $deleteForm = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\FallaCausa\FallaCausaDeleteForm', $falla_causa_id);
    // Check if we have to use modal or not
    if($js == 'ajax') {
        $response = new AjaxResponse();
        $falla_causa = FallaCausaDAO::load($falla_causa_id);
        $response->addCommand(new OpenModalDialogCommand('Eliminar Causa de falla: '.$falla_causa->getCausaFalla() , $deleteForm, ['width' => '800px']));
        return $response;
    } else {
        return $deleteForm;
    }
  }

  /**
   * Callback for opening the Causa de falla add modal form.
   */
  public function getAddModalForm($js) {
      $form = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\FallaCausa\FallaCausaForm');
      // Check if we have to use modal or not
      if($js == 'ajax') {
          $response = new AjaxResponse();

          $response->addCommand(new OpenModalDialogCommand('Agregar Causa de falla', $form, ['width' => '800px']));
          return $response;
      } else {
          return $form;
      }
  }

  /**
   * Callback for opening the Causa de falla edit form in modal.
   */
  public function getEditModalForm($falla_causa_id = NULL, $js) {
      $actividadClasificacion = FallaCausaDAO::load($falla_causa_id);
      $form = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\FallaCausa\FallaCausaForm', $falla_causa_id);
      // Check if we have to use modal or not
      if($js == 'ajax') {
          $response = new AjaxResponse();
          $response->addCommand(new OpenModalDialogCommand(t('Editar Causa de falla: @name',
          ['@name' => $actividadClasificacion->getCausaFalla()]), $form, ['width' => '800px']));
          return $response;
      } else {
          return $form;
      }
  }
}
