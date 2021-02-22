<?php

namespace Drupal\io_generic_abml\Controller;

use Drupal;
use Drupal\Core\Url;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;

use Drupal\io_generic_abml\Form\ContactoTipo\ContactoTipoTableForm;
use Drupal\io_generic_abml\DAOs\ContactoTipoDAO;


use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Tipo de contacto Controller class.
 *
 * This controller has everithing related to ABML from io_contacto_tipo table.
 */
class ContactoTipoController extends GenericABMLController {
  public function listAll() {
    //prepare a render array
    $content = [];

    // Add Title
    $content['title'] = [
      '#markup' => '<h3>Listado de Tipos de contacto</h3>',
    ];

    // add contacto_tipo search form
    $content['search_form'] =
    $this->formBuilder->getForm('Drupal\io_generic_abml\Form\ContactoTipo\ContactoTipoSearchForm');

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
    $addUrl = Url::fromRoute('io_generic_abml.contacto_tipo.add.getmodal', ['js' => 'ajax'], $ajax_link_attributes);
    $content['add_new_link'] = [
        '#type' => 'link',
        '#title' => 'Nuevo Tipo de contacto',
        '#url' => $addUrl,
    ];

    $entity_table_form_instance = new ContactoTipoTableForm($this->db, $search_key);
    $content['table'] = $this->formBuilder->getForm($entity_table_form_instance);
    $content['pager'] = [
      '#type' => 'pager',
    ];


    // attach library to open modals
    $content['#attached'] = ['library' => ['core/drupal.dialog.ajax']];

    return $content;
  }

  /**
   * Callback for openieng the Tipo de contacto delete modal form.
   *
   * @param ine $contacto_tipo_id
   *  Tipo de contacto ID.
   */
  public function openDeleteModalForm($contacto_tipo_id = NULL, $js) {
    $deleteForm = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\ContactoTipo\ContactoTipoDeleteForm', $contacto_tipo_id);
    // Check if we have to use modal or not
    if($js == 'ajax') {
        $response = new AjaxResponse();
        $contacto_tipo = ContactoTipoDAO::load($contacto_tipo_id);
        $response->addCommand(new OpenModalDialogCommand('Eliminar Tipo de contacto: '.$contacto_tipo->getTipoContacto() , $deleteForm, ['width' => '800px']));
        return $response;
    } else {
        return $deleteForm;
    }
  }

  /**
   * Callback for opening the Tipo de contacto add modal form.
   */
  public function getAddModalForm($js) {
      $form = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\ContactoTipo\ContactoTipoForm');
      // Check if we have to use modal or not
      if($js == 'ajax') {
          $response = new AjaxResponse();

          $response->addCommand(new OpenModalDialogCommand('Agregar Tipo de contacto', $form, ['width' => '800px']));
          return $response;
      } else {
          return $form;
      }
  }

  /**
   * Callback for opening the Tipo de contacto edit form in modal.
   */
  public function getEditModalForm($contacto_tipo_id = NULL, $js) {
      $actividadClasificacion = ContactoTipoDAO::load($contacto_tipo_id);
      $form = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\ContactoTipo\ContactoTipoForm', $contacto_tipo_id);
      // Check if we have to use modal or not
      if($js == 'ajax') {
          $response = new AjaxResponse();
          $response->addCommand(new OpenModalDialogCommand(t('Editar Tipo de contacto: @name',
          ['@name' => $actividadClasificacion->getTipoContacto()]), $form, ['width' => '800px']));
          return $response;
      } else {
          return $form;
      }
  }
}
