<?php

namespace Drupal\io_generic_abml\Form\DanioTipo;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

use Drupal\io_generic_abml\DAOs\DanioTipoDAO;
use Drupal\io_generic_abml\Form\GenericTableForm;

/**
 * Entity list in tableselect format.
 */
class DanioTipoTableForm extends GenericTableForm {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'danio_tipo_table_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    global $base_url;

    $form = parent::buildForm($form, $form_state);

    // Table header.
    $header = [
      ['data' => t('DAÑO'), 'field' => 'dt.danio'],
      ['data' => t('ACTIVO'), 'field' => 'dt.activo'],
      ['data' => t('CREADOR'), 'field' => 'dt.usuario_alta'],
      ['data' => t('FECHA ALTA'), 'field' => 'dt.fecha_alta'],
      ['data' => t('MODIFICO'), 'field' => 'dt.usuario_mod'],
      ['data' => t('FECHA MOD.'), 'field' => 'dt.fecha_mod'],
      'actions' => 'OPERACIONES',
    ];

    // Get EquipoTipos by search property
    $search_key = $this->searchKey;
    $counter = [];
    $results = [];
    if (!empty($this->searchKey)) {
      $fullResults = DanioTipoDAO::getAll($header, $search_key);
    } else {
      $fullResults = DanioTipoDAO::getAll($header);
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
       * @var \Drupal\io_generic_abml\DTOs\DanioTipoDTO
       *  $danioTipoDTO
       */
      $danioTipoDTO = $row;
      $danioTipoId = $danioTipoDTO->getId();
      $ajax_link_attributes = [
        'attributes' => [
          'class' => 'use-ajax',
          'data-dialog-type' => 'modal',
          'data-dialog-options' => ['width' => 700, 'height' => 400],
        ],
      ];

      $ajax_link_attributes['attributes']['title'] = t('Editar');
      $editUrl = Url::fromRoute('io_generic_abml.danio_tipo.edit.getmodal', ['danio_tipo_id' => $danioTipoId, 'js' => 'ajax'], $ajax_link_attributes);
      $quickEditLink = \Drupal::service('link_generator')->generate(t('<i class="far fa-edit"></i>'), $editUrl);

      // prepare delete link
      $ajax_link_attributes['attributes']['title'] = t('Eliminar');
      $deletetUrl = Url::fromRoute('io_generic_abml.danio_tipo.delete.getmodal', ['danio_tipo_id' => $danioTipoId, 'js' => 'ajax'], $ajax_link_attributes);
      $deleteLink = \Drupal::service('link_generator')->generate(t('<i class="far fa-trash-alt"></i>'), $deletetUrl);

      $operationLinks = t('@editLink @deleteLink', ['@editLink' => $quickEditLink, '@deleteLink' => $deleteLink]);

      $form['table'][$danioTipoId]['danio_tipo'] = [
        '#plain_text' => $danioTipoDTO->getDanio(),
      ];
      $form['table'][$danioTipoId]['activo'] = [
        '#plain_text' => $danioTipoDTO->getActivoString(),
      ];
      $form['table'][$danioTipoId]['usuario_alta'] = [
        '#plain_text' => $danioTipoDTO->getUsuarioAlta()->getUsername(),
      ];
      $form['table'][$danioTipoId]['fecha_alta'] = [
        '#plain_text' => $danioTipoDTO->getFechaAlta(),
      ];
      $form['table'][$danioTipoId]['usuario_mod'] = [
        '#plain_text' => $danioTipoDTO->getUsuarioMod()->getUsername(),
      ];
      $form['table'][$danioTipoId]['fecha_mod'] = [
        '#plain_text' => $danioTipoDTO->getFechaMod(),
      ];
      $form['table'][$danioTipoId]['actions'] = [
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
