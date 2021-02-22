<?php

namespace Drupal\io_generic_abml\Form\TrabajoTipo;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

use Drupal\io_generic_abml\DAOs\TrabajoTipoDAO;
use Drupal\io_generic_abml\Form\GenericTableForm;

/**
 * Entity list in tableselect format.
 */
class TrabajoTipoTableForm extends GenericTableForm {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'trabajo_tipo_table_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    global $base_url;

    $form = parent::buildForm($form, $form_state);

    // Table header.
    $header = [
      ['data' => t('TIPO'), 'field' => 'tt.tipo'],
      ['data' => t('ACTIVO'), 'field' => 'tt.activo'],
      ['data' => t('CREADOR'), 'field' => 'tt.usuario_alta'],
      ['data' => t('FECHA ALTA'), 'field' => 'tt.fecha_alta'],
      ['data' => t('MODIFICO'), 'field' => 'tt.usuario_mod'],
      ['data' => t('FECHA MOD.'), 'field' => 'tt.fecha_mod'],
      'actions' => 'OPERACIONES',
    ];

    // Get EquipoTipos by search property
    $search_key = $this->searchKey;
    $counter = [];
    $results = [];
    if (!empty($this->searchKey)) {
      $fullResults = TrabajoTipoDAO::getAll($header, $search_key);
    } else {
      $fullResults = TrabajoTipoDAO::getAll($header);
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
       * @var \Drupal\io_generic_abml\DTOs\TrabajoTipoDTO
       *  $trabajoTipoDTO
       */
      $trabajoTipoDTO = $row;
      $trabajoTipoId = $trabajoTipoDTO->getId();
      $ajax_link_attributes = [
        'attributes' => [
          'class' => 'use-ajax',
          'data-dialog-type' => 'modal',
          'data-dialog-options' => ['width' => 700, 'height' => 400],
        ],
      ];

      $ajax_link_attributes['attributes']['title'] = t('Editar');
      $editUrl = Url::fromRoute('io_generic_abml.trabajo_tipo.edit.getmodal', ['trabajo_tipo_id' => $trabajoTipoId, 'js' => 'ajax'], $ajax_link_attributes);
      $quickEditLink = \Drupal::service('link_generator')->generate(t('<i class="far fa-edit"></i>'), $editUrl);

      // prepare delete link
      $ajax_link_attributes['attributes']['title'] = t('Eliminar');
      $deletetUrl = Url::fromRoute('io_generic_abml.trabajo_tipo.delete.getmodal', ['trabajo_tipo_id' => $trabajoTipoId, 'js' => 'ajax'], $ajax_link_attributes);
      $deleteLink = \Drupal::service('link_generator')->generate(t('<i class="far fa-trash-alt"></i>'), $deletetUrl);

      $operationLinks = t('@editLink @deleteLink', ['@editLink' => $quickEditLink, '@deleteLink' => $deleteLink]);

      $form['table'][$trabajoTipoId]['trabajo_tipo'] = [
        '#plain_text' => $trabajoTipoDTO->getTipoTrabajo(),
      ];
      $form['table'][$trabajoTipoId]['activo'] = [
        '#plain_text' => $trabajoTipoDTO->getActivoString(),
      ];
      $form['table'][$trabajoTipoId]['usuario_alta'] = [
        '#plain_text' => $trabajoTipoDTO->getUsuarioAlta()->getUsername(),
      ];
      $form['table'][$trabajoTipoId]['fecha_alta'] = [
        '#plain_text' => $trabajoTipoDTO->getFechaAlta(),
      ];
      $form['table'][$trabajoTipoId]['usuario_mod'] = [
        '#plain_text' => $trabajoTipoDTO->getUsuarioMod()->getUsername(),
      ];
      $form['table'][$trabajoTipoId]['fecha_mod'] = [
        '#plain_text' => $trabajoTipoDTO->getFechaMod(),
      ];
      $form['table'][$trabajoTipoId]['actions'] = [
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
