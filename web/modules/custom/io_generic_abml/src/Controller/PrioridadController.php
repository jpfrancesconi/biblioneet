<?php

namespace Drupal\io_generic_abml\Controller;

use Drupal;
use Drupal\Core\Url;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;

use Drupal\io_generic_abml\DAOs\PrioridadDAO;
use Drupal\io_generic_abml\Form\Prioridad\PrioridadTableForm;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Prioridad Controller class.
 *
 * This controller has everithing related to ABML from io_prioridad table.
 */
class PrioridadController extends GenericABMLController {
  public function listAll() {
    //prepare a render array
    $content = [];

    // Add Title
    $content['title'] = [
      '#markup' => '<h3>Listado de Prioridades</h3>',
    ];

    // add prioridad search form
    $content['search_form'] =
    $this->formBuilder->getForm('Drupal\io_generic_abml\Form\Prioridad\PrioridadSearchForm');

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
    $addUrl = Url::fromRoute('io_generic_abml.prioridad.add.getmodal', ['js' => 'ajax'], $ajax_link_attributes);
    $content['add_new_link'] = [
        '#type' => 'link',
        '#title' => 'Nueva prioridad',
        '#url' => $addUrl,
    ];

    $entity_table_form_instance = new PrioridadTableForm($this->db, $search_key);
    $content['table'] = $this->formBuilder->getForm($entity_table_form_instance);
    $content['pager'] = [
      '#type' => 'pager',
    ];


    // attach library to open modals
    $content['#attached'] = ['library' => ['core/drupal.dialog.ajax']];

    return $content;
  }

  /**
   * Callback for openieng the Prioridad delete modal form.
   *
   * @param ine $prioridad_id
   *  Prioridad ID.
   */
  public function openDeleteModalForm($prioridad_id = NULL, $js) {
    $deleteForm = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\Prioridad\PrioridadDeleteForm', $prioridad_id);
    // Check if we have to use modal or not
    if($js == 'ajax') {
        $response = new AjaxResponse();
        $prioridad = PrioridadDAO::load($prioridad_id);
        $response->addCommand(new OpenModalDialogCommand('Eliminar Prioridad: '.$prioridad->getPrioridad() , $deleteForm, ['width' => '800px']));
        return $response;
    } else {
        return $deleteForm;
    }
  }

  /**
   * Callback for opening the Prioridad add modal form.
   */
  public function getAddModalForm($js) {
      $form = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\Prioridad\PrioridadForm');
      // Check if we have to use modal or not
      if($js == 'ajax') {
          $response = new AjaxResponse();
          $response->addCommand(new OpenModalDialogCommand('Agregar Prioridad', $form, ['width' => '800px']));
          return $response;
      } else {
          return $form;
      }
  }

  /**
   * Callback for opening the Tipo de Equipo delete form in modal.
   */
  public function getEditModalForm($prioridad_id, $js) {
      $prioridad = PrioridadDAO::load($prioridad_id);
      $form = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\Prioridad\PrioridadForm', $prioridad_id);
      // Check if we have to use modal or not
      if($js == 'ajax') {
          $response = new AjaxResponse();
          $response->addCommand(new OpenModalDialogCommand(t('Editar prioridad: @name',
          ['@name' => $prioridad->getPrioridad()]), $form, ['width' => '800px']));
          return $response;
      } else {
          return $form;
      }
  }
}
