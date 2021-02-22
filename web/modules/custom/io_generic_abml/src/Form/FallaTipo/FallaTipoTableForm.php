<?php

namespace Drupal\io_generic_abml\Form\FallaTipo;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

use Drupal\io_generic_abml\DAOs\FallaTipoDAO;
use Drupal\io_generic_abml\Form\GenericTableForm;

/**
 * Entity list in tableselect format.
 */
class FallaTipoTableForm extends GenericTableForm {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'falla_tipo_table_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $equipo_tipo_id = NULL) {
    global $base_url;

    $form = parent::buildForm($form, $form_state);

    // Table header.
    $header = [
      ['data' => t('FALLA'), 'field' => 'ft.falla'],
      ['data' => t('ACTIVO'), 'field' => 'ft.activo'],
      ['data' => t('CREADOR'), 'field' => 'ft.usuario_alta'],
      ['data' => t('FECHA ALTA'), 'field' => 'ft.fecha_alta'],
      ['data' => t('MODIFICO'), 'field' => 'ft.usuario_mod'],
      ['data' => t('FECHA MOD.'), 'field' => 'ft.fecha_mod'],
      'actions' => 'OPERACIONES',
    ];

    // Get EquipoTipos by search property
    $search_key = $this->searchKey;
    $counter = [];
    $results = [];
    if (!empty($this->searchKey)) {
      $fullResults = FallaTipoDAO::getAll($header, $equipo_tipo_id, $search_key);
    } else {
      $fullResults = FallaTipoDAO::getAll($header, $equipo_tipo_id);
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
       * @var \Drupal\io_generic_abml\DTOs\FallaTipoDTO
       *  $fallaTipoDTO
       */
      $fallaTipoDTO = $row;
      $fallaTipoId = $fallaTipoDTO->getId();
      $ajax_link_attributes = [
        'attributes' => [
          'class' => 'use-ajax',
          'data-dialog-type' => 'modal',
          'data-dialog-options' => ['width' => 700, 'height' => 400],
        ],
      ];

      $ajax_link_attributes['attributes']['title'] = t('Editar');
      $editUrl = Url::fromRoute('io_generic_abml.falla_tipo.edit.getmodal', ['equipo_tipo_id' => $equipo_tipo_id, 'falla_tipo_id' => $fallaTipoId, 'js' => 'ajax'], $ajax_link_attributes);
      $quickEditLink = \Drupal::service('link_generator')->generate(t('<i class="far fa-edit"></i>'), $editUrl);

      // prepare delete link
      $ajax_link_attributes['attributes']['title'] = t('Eliminar');
      $deletetUrl = Url::fromRoute('io_generic_abml.falla_tipo.delete.getmodal', ['falla_tipo_id' => $fallaTipoId, 'js' => 'ajax'], $ajax_link_attributes);
      $deleteLink = \Drupal::service('link_generator')->generate(t('<i class="far fa-trash-alt"></i>'), $deletetUrl);

      $operationLinks = t('@editLink @deleteLink', ['@editLink' => $quickEditLink, '@deleteLink' => $deleteLink]);

      $form['table'][$fallaTipoId]['falla_tipo'] = [
        '#plain_text' => $fallaTipoDTO->getFalla(),
      ];
      $form['table'][$fallaTipoId]['activo'] = [
        '#plain_text' => $fallaTipoDTO->getActivoString(),
      ];
      $form['table'][$fallaTipoId]['usuario_alta'] = [
        '#plain_text' => $fallaTipoDTO->getUsuarioAlta()->getUsername(),
      ];
      $form['table'][$fallaTipoId]['fecha_alta'] = [
        '#plain_text' => $fallaTipoDTO->getFechaAlta(),
      ];
      $form['table'][$fallaTipoId]['usuario_mod'] = [
        '#plain_text' => $fallaTipoDTO->getUsuarioMod()->getUsername(),
      ];
      $form['table'][$fallaTipoId]['fecha_mod'] = [
        '#plain_text' => $fallaTipoDTO->getFechaMod(),
      ];
      $form['table'][$fallaTipoId]['actions'] = [
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
