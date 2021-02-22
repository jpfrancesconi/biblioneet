<?php

namespace Drupal\io_generic_abml\Controller;

use Drupal;
use Drupal\Core\Url;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;

use Drupal\io_generic_abml\Form\UnidadMedida\UnidadMedidaTableForm;
use Drupal\io_generic_abml\DAOs\UnidadMedidaDAO;


use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Unidad de medida Controller class.
 *
 * This controller has everithing related to ABML from io_unidad_medida table.
 */
class UnidadMedidaController extends GenericABMLController {
  public function listAll() {
    //prepare a render array
    $content = [];

    // Add Title
    $content['title'] = [
      '#markup' => '<h3>Listado de Unidades de medida</h3>',
    ];

    // add unidad_medida search form
    $content['search_form'] =
    $this->formBuilder->getForm('Drupal\io_generic_abml\Form\UnidadMedida\UnidadMedidaSearchForm');

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
    $addUrl = Url::fromRoute('io_generic_abml.unidad_medida.add.getmodal', ['js' => 'ajax'], $ajax_link_attributes);
    $content['add_new_link'] = [
        '#type' => 'link',
        '#title' => 'Nueva unidad de medida',
        '#url' => $addUrl,
    ];

    $entity_table_form_instance = new UnidadMedidaTableForm($this->db, $search_key);
    $content['table'] = $this->formBuilder->getForm($entity_table_form_instance);
    $content['pager'] = [
      '#type' => 'pager',
    ];


    // attach library to open modals
    $content['#attached'] = ['library' => ['core/drupal.dialog.ajax']];

    return $content;
  }

  /**
   * Callback for openieng the Unidad de medida delete modal form.
   *
   * @param ine $unidad_medida_id
   *  Unidad de medida ID.
   */
  public function openDeleteModalForm($unidad_medida_id = NULL, $js) {
    $deleteForm = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\UnidadMedida\UnidadMedidaDeleteForm', $unidad_medida_id);
    // Check if we have to use modal or not
    if($js == 'ajax') {
        $response = new AjaxResponse();
        $unidad_medida = UnidadMedidaDAO::load($unidad_medida_id);
        $response->addCommand(new OpenModalDialogCommand('Eliminar Prioridad: '.$unidad_medida->getUnidadMedida() , $deleteForm, ['width' => '800px']));
        return $response;
    } else {
        return $deleteForm;
    }
  }

  /**
   * Callback for opening the Unidad de medida add modal form.
   */
  public function getAddModalForm($js) {
      $form = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\UnidadMedida\UnidadMedidaForm');
      // Check if we have to use modal or not
      if($js == 'ajax') {
          $response = new AjaxResponse();

          $response->addCommand(new OpenModalDialogCommand('Agregar unidad de medida', $form, ['width' => '800px']));
          return $response;
      } else {
          return $form;
      }
  }

  /**
   * Callback for opening the Unidad de medida delete form in modal.
   */
  public function getEditModalForm($unidad_medida_id, $js) {
      $unidadMedida = UnidadMedidaDAO::load($unidad_medida_id);
      $form = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\UnidadMedida\UnidadMedidaForm', $unidad_medida_id);
      // Check if we have to use modal or not
      if($js == 'ajax') {
          $response = new AjaxResponse();
          $response->addCommand(new OpenModalDialogCommand(t('Editar tipo de equipo: @name',
          ['@name' => $unidadMedida->getUnidadMedida()]), $form, ['width' => '800px']));
          return $response;
      } else {
          return $form;
      }
  }
}
