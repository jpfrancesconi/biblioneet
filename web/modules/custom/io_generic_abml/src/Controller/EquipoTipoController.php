<?php

namespace Drupal\io_generic_abml\Controller;

use Drupal\io_generic_abml\Form\EquipoTipos\EquipoTiposTableForm;

use Drupal\io_generic_abml\DAOs\EquipoTiposDAO;

use Drupal;
use Drupal\Core\Url;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Tipo de Equipo Controller class.
 *
 * This controller has everithing related to ABML from io_equipo_tipo table.
 */
class EquipoTipoController extends GenericABMLController {

    /**
     * Lists all the entity records.
     */
    public function listAll() {
        // prepare render array
        $content = [];

        // Add Title
        $content['title'] = [
            '#markup' => '<h3>Listado de Tipos de Equipos</h3>',
        ];

        // add search form
        $content['search_form'] = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\EquipoTipos\EquipoTiposSearchForm');

        // get current search parameter on the request
        $search_key = $this->request->getCurrentRequest()->get('search');

        // Add nre record link
        $ajax_link_attributes = [
            'attributes' => [
              'class' => ['use-ajax', 'btn', 'btn-primary'],
              'data-dialog-type' => 'modal',
              'data-dialog-options' => ['width' => 700, 'height' => 400],
            ],
          ];
        $addUrl = Url::fromRoute('io_generic_abml.equipo_tipo.add.getmodal', ['js' => 'ajax'], $ajax_link_attributes);
        $content['add_new_link'] = [
            '#type' => 'link',
            '#title' => 'Nuevo Tipo de Equipo',
            '#url' => $addUrl,
        ];

        $entity_table_form_instance = new EquipoTiposTableForm($this->db, $search_key);
        $content['table'] = $this->formBuilder->getForm($entity_table_form_instance);
        $content['pager'] = [
        '#type' => 'pager',
        ];

        // attach library to open modals
        $content['#attached'] = ['library' => ['core/drupal.dialog.ajax']];

        return $content;
    }

    /**
     * Callback for opening the Tipo de Equipo delete form in modal.
     */
    public function getDeleteModalForm($equipo_tipo_id, $js) {
        $deleteForm = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\EquipoTipos\EquipoTipoDeleteForm', $equipo_tipo_id);
        // Check if we have to use modal or not
        if($js == 'ajax') {
            $response = new AjaxResponse();
            $equipoTipo = EquipoTiposDAO::load($equipo_tipo_id);
            $response->addCommand(new OpenModalDialogCommand('Eliminar Tipo de Equipo: '.$equipoTipo->getTipo() , $deleteForm, ['width' => '800px']));
            return $response;
        } else {
            return $deleteForm;
        }
    }

    /**
     * Callback for opening the Tipo de Equipo delete form in modal.
     */
    public function getAddModalForm($js) {
        $form = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\EquipoTipos\EquipoTipoForm');
        // Check if we have to use modal or not
        if($js == 'ajax') {
            $response = new AjaxResponse();
            $response->addCommand(new OpenModalDialogCommand('Agregar Tipo de Equipo', $form, ['width' => '800px']));
            return $response;
        } else {
            return $form;
        }
    }

    /**
     * Callback for opening the Tipo de Equipo delete form in modal.
     */
    public function getEditModalForm($js, $equipo_tipo_id) {
        $equipoTipo = EquipoTiposDAO::load($equipo_tipo_id);
        $form = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\EquipoTipos\EquipoTipoForm', $equipo_tipo_id);
        // Check if we have to use modal or not
        if($js == 'ajax') {
            $response = new AjaxResponse();
            $response->addCommand(new OpenModalDialogCommand(t('Editar tipo de equipo: @name',
            ['@name' => $equipoTipo->getTipo()]), $form, ['width' => '800px']));
            return $response;
        } else {
            return $form;
        }
    }
}
