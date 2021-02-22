<?php

namespace Drupal\io_generic_abml\Controller;

use Drupal;
use Drupal\Core\Url;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;

use Drupal\io_generic_abml\Form\ActividadClasificacion\ActividadClasificacionTableForm;
use Drupal\io_generic_abml\DAOs\ActividadClasificacionDAO;


use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Clasificacion de actividad Controller class.
 *
 * This controller has everithing related to ABML from io_actividad_clasificacion table.
 */
class ActividadClasificacionController extends GenericABMLController {
  public function listAll() {
    //prepare a render array
    $content = [];

    // Add Title
    $content['title'] = [
      '#markup' => '<h3>Listado de Clasificacion de actividades</h3>',
    ];

    // add actividad_clasificacion search form
    $content['search_form'] =
    $this->formBuilder->getForm('Drupal\io_generic_abml\Form\ActividadClasificacion\ActividadClasificacionSearchForm');

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
    $addUrl = Url::fromRoute('io_generic_abml.actividad_clasificacion.add.getmodal', ['js' => 'ajax'], $ajax_link_attributes);
    $content['add_new_link'] = [
        '#type' => 'link',
        '#title' => 'Nueva clasificacion de actividad',
        '#url' => $addUrl,
    ];

    $entity_table_form_instance = new ActividadClasificacionTableForm($this->db, $search_key);
    $content['table'] = $this->formBuilder->getForm($entity_table_form_instance);
    $content['pager'] = [
      '#type' => 'pager',
    ];


    // attach library to open modals
    $content['#attached'] = ['library' => ['core/drupal.dialog.ajax']];

    return $content;
  }

  /**
   * Callback for openieng the Clasificacion de actividad delete modal form.
   *
   * @param ine $actividad_clasificacion_id
   *  Clasificacion de actividad ID.
   */
  public function openDeleteModalForm($actividad_clasificacion_id = NULL, $js) {
    $deleteForm = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\ActividadClasificacion\ActividadClasificacionDeleteForm', $actividad_clasificacion_id);
    // Check if we have to use modal or not
    if($js == 'ajax') {
        $response = new AjaxResponse();
        $actividad_clasificacion = ActividadClasificacionDAO::load($actividad_clasificacion_id);
        $response->addCommand(new OpenModalDialogCommand('Eliminar Prioridad: '.$actividad_clasificacion->getTipo() , $deleteForm, ['width' => '800px']));
        return $response;
    } else {
        return $deleteForm;
    }
  }

  /**
   * Callback for opening the Clasificacion de actividad add modal form.
   */
  public function getAddModalForm($js) {
      $form = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\ActividadClasificacion\ActividadClasificacionForm');
      // Check if we have to use modal or not
      if($js == 'ajax') {
          $response = new AjaxResponse();

          $response->addCommand(new OpenModalDialogCommand('Agregar clasificacion de actividad', $form, ['width' => '800px']));
          return $response;
      } else {
          return $form;
      }
  }

  /**
   * Callback for opening the Clasificacion de actividad edit form in modal.
   */
  public function getEditModalForm($actividad_clasificacion_id = NULL, $js) {
      $actividadClasificacion = ActividadClasificacionDAO::load($actividad_clasificacion_id);
      $form = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\ActividadClasificacion\ActividadClasificacionForm', $actividad_clasificacion_id);
      // Check if we have to use modal or not
      if($js == 'ajax') {
          $response = new AjaxResponse();
          $response->addCommand(new OpenModalDialogCommand(t('Editar tipo de clasificación de actividad: @name',
          ['@name' => $actividadClasificacion->getTipo()]), $form, ['width' => '800px']));
          return $response;
      } else {
          return $form;
      }
  }
}
