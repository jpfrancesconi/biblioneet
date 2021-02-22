<?php

namespace Drupal\io_generic_abml\Controller;

use Drupal;
use Drupal\Core\Url;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;

use Drupal\io_generic_abml\Form\EquipoClasificacion\EquipoClasificacionTableForm;
use Drupal\io_generic_abml\DAOs\EquipoClasificacionDAO;


use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Clasificacion de equipo Controller class.
 *
 * This controller has everithing related to ABML from io_equipo_clasificacion table.
 */
class EquipoClasificacionController extends GenericABMLController {
  public function listAll() {
    //prepare a render array
    $content = [];

    // Add Title
    $content['title'] = [
      '#markup' => '<h3>Listado de Clasificacion de equipos</h3>',
    ];

    // add equipo_clasificacion search form
    $content['search_form'] =
    $this->formBuilder->getForm('Drupal\io_generic_abml\Form\EquipoClasificacion\EquipoClasificacionSearchForm');

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
    $addUrl = Url::fromRoute('io_generic_abml.equipo_clasificacion.add.getmodal', ['js' => 'ajax'], $ajax_link_attributes);
    $content['add_new_link'] = [
        '#type' => 'link',
        '#title' => 'Nueva clasificacion de equipo',
        '#url' => $addUrl,
    ];

    $entity_table_form_instance = new EquipoClasificacionTableForm($this->db, $search_key);
    $content['table'] = $this->formBuilder->getForm($entity_table_form_instance);
    $content['pager'] = [
      '#type' => 'pager',
    ];


    // attach library to open modals
    $content['#attached'] = ['library' => ['core/drupal.dialog.ajax']];

    return $content;
  }

  /**
   * Callback for openieng the Clasificacion de equipo delete modal form.
   *
   * @param ine $equipo_clasificacion_id
   *  Clasificacion de equipo ID.
   */
  public function openDeleteModalForm($equipo_clasificacion_id = NULL, $js) {
    $deleteForm = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\EquipoClasificacion\EquipoClasificacionDeleteForm', $equipo_clasificacion_id);
    // Check if we have to use modal or not
    if($js == 'ajax') {
        $response = new AjaxResponse();
        $equipo_clasificacion = EquipoClasificacionDAO::load($equipo_clasificacion_id);
        $response->addCommand(new OpenModalDialogCommand('Eliminar Prioridad: '.$equipo_clasificacion->getClasificacion() , $deleteForm, ['width' => '800px']));
        return $response;
    } else {
        return $deleteForm;
    }
  }

  /**
   * Callback for opening the Clasificacion de equipo add modal form.
   */
  public function getAddModalForm($js) {
      $form = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\EquipoClasificacion\EquipoClasificacionForm');
      // Check if we have to use modal or not
      if($js == 'ajax') {
          $response = new AjaxResponse();

          $response->addCommand(new OpenModalDialogCommand('Agregar clasificacion de equipo', $form, ['width' => '800px']));
          return $response;
      } else {
          return $form;
      }
  }

  /**
   * Callback for opening the Clasificacion de equipo edit form in modal.
   */
  public function getEditModalForm($equipo_clasificacion_id = NULL, $js) {
      $equipoClasificacion = EquipoClasificacionDAO::load($equipo_clasificacion_id);
      $form = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\EquipoClasificacion\EquipoClasificacionForm', $equipo_clasificacion_id);
      // Check if we have to use modal or not
      if($js == 'ajax') {
          $response = new AjaxResponse();
          $response->addCommand(new OpenModalDialogCommand(t('Editar tipo de clasificación de equipo: @name',
          ['@name' => $equipoClasificacion->getClasificacion()]), $form, ['width' => '800px']));
          return $response;
      } else {
          return $form;
      }
  }
}
