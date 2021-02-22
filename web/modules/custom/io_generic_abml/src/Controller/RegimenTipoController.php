<?php

namespace Drupal\io_generic_abml\Controller;

use Drupal;
use Drupal\Core\Url;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;

use Drupal\io_generic_abml\Form\RegimenTipo\RegimenTipoTableForm;
use Drupal\io_generic_abml\DAOs\RegimenTipoDAO;


use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Tipo de regimen Controller class.
 *
 * This controller has everithing related to ABML from io_regimen_tipo table.
 */
class RegimenTipoController extends GenericABMLController {
  public function listAll() {
    //prepare a render array
    $content = [];

    // Add Title
    $content['title'] = [
      '#markup' => '<h3>Listado de Tipos de regimen</h3>',
    ];

    // add regimen_tipo search form
    $content['search_form'] =
    $this->formBuilder->getForm('Drupal\io_generic_abml\Form\RegimenTipo\RegimenTipoSearchForm');

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
    $addUrl = Url::fromRoute('io_generic_abml.regimen_tipo.add.getmodal', ['js' => 'ajax'], $ajax_link_attributes);
    $content['add_new_link'] = [
        '#type' => 'link',
        '#title' => 'Nueva tipo de regimen',
        '#url' => $addUrl,
    ];

    $entity_table_form_instance = new RegimenTipoTableForm($this->db, $search_key);
    $content['table'] = $this->formBuilder->getForm($entity_table_form_instance);
    $content['pager'] = [
      '#type' => 'pager',
    ];


    // attach library to open modals
    $content['#attached'] = ['library' => ['core/drupal.dialog.ajax']];

    return $content;
  }

  /**
   * Callback for openieng the Tipo de regimen delete modal form.
   *
   * @param ine $regimen_tipo_id
   *  Tipo de regimen ID.
   */
  public function openDeleteModalForm($regimen_tipo_id = NULL, $js) {
    $deleteForm = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\RegimenTipo\RegimenTipoDeleteForm', $regimen_tipo_id);
    // Check if we have to use modal or not
    if($js == 'ajax') {
        $response = new AjaxResponse();
        $regimen_tipo = RegimenTipoDAO::load($regimen_tipo_id);
        $response->addCommand(new OpenModalDialogCommand('Eliminar Tipo de regimen: '.$regimen_tipo->getTipoRegimen() , $deleteForm, ['width' => '800px']));
        return $response;
    } else {
        return $deleteForm;
    }
  }

  /**
   * Callback for opening the Tipo de regimen add modal form.
   */
  public function getAddModalForm($js) {
      $form = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\RegimenTipo\RegimenTipoForm');
      // Check if we have to use modal or not
      if($js == 'ajax') {
          $response = new AjaxResponse();

          $response->addCommand(new OpenModalDialogCommand('Agregar tipo de regimen', $form, ['width' => '800px']));
          return $response;
      } else {
          return $form;
      }
  }

  /**
   * Callback for opening the Clasificacion de actividad edit form in modal.
   */
  public function getEditModalForm($regimen_tipo_id = NULL, $js) {
    $regimenTipo = RegimenTipoDAO::load($regimen_tipo_id);
    $form = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\RegimenTipo\RegimenTipoForm', $regimen_tipo_id);
    // Check if we have to use modal or not
    if($js == 'ajax') {
        $response = new AjaxResponse();
        $response->addCommand(new OpenModalDialogCommand(t('Editar tipo de regimen: @name',
        ['@name' => $regimenTipo->getTipoRegimen()]), $form, ['width' => '800px']));
        return $response;
    } else {
        return $form;
    }
  }
}
