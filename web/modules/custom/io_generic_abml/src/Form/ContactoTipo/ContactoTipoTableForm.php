<?php

namespace Drupal\io_generic_abml\Form\ContactoTipo;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

use Drupal\io_generic_abml\DAOs\ContactoTipoDAO;
use Drupal\io_generic_abml\Form\GenericTableForm;

/**
 * Entity list in tableselect format.
 */
class ContactoTipoTableForm extends GenericTableForm {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'contacto_tipo_table_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    global $base_url;

    $form = parent::buildForm($form, $form_state);

    // Table header.
    $header = [
      ['data' => t('TIPO'), 'field' => 'tc.tipo'],
      ['data' => t('ACTIVO'), 'field' => 'tc.activo'],
      ['data' => t('CREADOR'), 'field' => 'tc.usuario_alta'],
      ['data' => t('FECHA ALTA'), 'field' => 'tc.fecha_alta'],
      ['data' => t('MODIFICO'), 'field' => 'tc.usuario_mod'],
      ['data' => t('FECHA MOD.'), 'field' => 'tc.fecha_mod'],
      'actions' => 'OPERACIONES',
    ];

    // Get EquipoTipos by search property
    $search_key = $this->searchKey;
    $counter = [];
    $results = [];
    if (!empty($this->searchKey)) {
      $fullResults = ContactoTipoDAO::getAll($header, $search_key);
    } else {
      $fullResults = ContactoTipoDAO::getAll($header);
    }
    // Get the counter data to be shown in the counter summary section.
    $counter = $fullResults['counter'];
    // Get the DTOs object to be shown in the results table.
    $results = $fullResults['resultsDTO'];
    // Add counter summary section
    $form['results_counter'] = [
      '#markup' => '<p>Mostrando registros '. $counter['start'] .' - ' . $counter['end'] . ' de ' . $counter['total'] . '</p>',
    ];

    $form['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#empty' => t('La lista está vacía'),
      '#attributes' => [
        'id' => 'table',
        'class' => ['table-sm']
      ],
    ];

    // Iterate results to build a table results to be rendered
    foreach ($results as $row) {
      /**
       * @var \Drupal\io_generic_abml\DTOs\ContactoTipoDTO
       *  $contactoTipoDTO
       */
      $contactoTipoDTO = $row;
      $contactoTipoId = $contactoTipoDTO->getId();
      $ajax_link_attributes = [
        'attributes' => [
          'class' => 'use-ajax',
          'data-dialog-type' => 'modal',
          'data-dialog-options' => ['width' => 700, 'height' => 400],
        ],
      ];

      $ajax_link_attributes['attributes']['title'] = t('Editar');
      $editUrl = Url::fromRoute('io_generic_abml.contacto_tipo.edit.getmodal', ['contacto_tipo_id' => $contactoTipoId, 'js' => 'ajax'], $ajax_link_attributes);
      $quickEditLink = \Drupal::service('link_generator')->generate(t('<i class="far fa-edit"></i>'), $editUrl);

      // prepare delete link
      $ajax_link_attributes['attributes']['title'] = t('Eliminar');
      $deletetUrl = Url::fromRoute('io_generic_abml.contacto_tipo.delete.getmodal', ['contacto_tipo_id' => $contactoTipoId, 'js' => 'ajax'], $ajax_link_attributes);
      $deleteLink = \Drupal::service('link_generator')->generate(t('<i class="far fa-trash-alt"></i>'), $deletetUrl);

      $operationLinks = t('@editLink @deleteLink', ['@editLink' => $quickEditLink, '@deleteLink' => $deleteLink]);

      $form['table'][$contactoTipoId]['contacto_tipo'] = [
        '#plain_text' => $contactoTipoDTO->getTipoContacto(),
      ];
      $form['table'][$contactoTipoId]['activo'] = [
        '#plain_text' => $contactoTipoDTO->getActivoString(),
      ];
      $form['table'][$contactoTipoId]['usuario_alta'] = [
        '#plain_text' => $contactoTipoDTO->getUsuarioAlta()->getUsername(),
      ];
      $form['table'][$contactoTipoId]['fecha_alta'] = [
        '#plain_text' => $contactoTipoDTO->getFechaAlta(),
      ];
      $form['table'][$contactoTipoId]['usuario_mod'] = [
        '#plain_text' => $contactoTipoDTO->getUsuarioMod()->getUsername(),
      ];
      $form['table'][$contactoTipoId]['fecha_mod'] = [
        '#plain_text' => $contactoTipoDTO->getFechaMod(),
      ];
      $form['table'][$contactoTipoId]['actions'] = [
        '#markup' => $operationLinks,
      ];
    }

    // attach library to open modals
    $form['#attached'] = ['library' => ['core/drupal.dialog.ajax']];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {}

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

}
