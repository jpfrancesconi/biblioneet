<?php

namespace Drupal\io_generic_abml\Form\RegimenTipo;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

use Drupal\io_generic_abml\DAOs\RegimenTipoDAO;
use Drupal\io_generic_abml\Form\GenericTableForm;

/**
 * Entity list in tableselect format.
 */
class RegimenTipoTableForm extends GenericTableForm {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'regimen_tipo_table_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    global $base_url;

    $form = parent::buildForm($form, $form_state);

    // Table header.
    $header = [
      ['data' => t('TIPO DE REGIMEN'), 'field' => 'rt.tipo_regimen'],
      ['data' => t('ACTIVO'), 'field' => 'rt.activo'],
      ['data' => t('CREADOR'), 'field' => 'rt.usuario_alta'],
      ['data' => t('FECHA ALTA'), 'field' => 'rt.fecha_alta'],
      ['data' => t('MODIFICO'), 'field' => 'rt.usuario_mod'],
      ['data' => t('FECHA MOD.'), 'field' => 'rt.fecha_mod'],
      'actions' => 'OPERACIONES',
    ];

     // Get EquipoTipos by search property
    $search_key = $this->searchKey;
    $counter = [];
    $results = [];
    if (!empty($this->searchKey)) {
      $fullResults = RegimenTipoDAO::getAll($header, $search_key);
    } else {
      $fullResults = RegimenTipoDAO::getAll($header);
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
       * @var \Drupal\io_generic_abml\DTOs\RegimenTipoDTO
       *  $regimenTipoDTO
       */
      $regimenTipoDTO = $row;
      $regimenTipoId = $regimenTipoDTO->getId();
      $ajax_link_attributes = [
        'attributes' => [
          'class' => 'use-ajax',
          'data-dialog-type' => 'modal',
          'data-dialog-options' => ['width' => 700, 'height' => 400],
        ],
      ];
      $ajax_link_attributes['attributes']['title'] = t('Editar');
      $editUrl = Url::fromRoute('io_generic_abml.regimen_tipo.edit.getmodal', ['regimen_tipo_id' => $regimenTipoId, 'js' => 'ajax'], $ajax_link_attributes);
      $quickEditLink = \Drupal::service('link_generator')->generate(t('<i class="far fa-edit"></i>'), $editUrl);

      // prepare delete link
      $ajax_link_attributes['attributes']['title'] = t('Eliminar');
      $deletetUrl = Url::fromRoute('io_generic_abml.regimen_tipo.delete.getmodal', ['regimen_tipo_id' => $regimenTipoId, 'js' => 'ajax'], $ajax_link_attributes);
      $deleteLink = \Drupal::service('link_generator')->generate(t('<i class="far fa-trash-alt"></i>'), $deletetUrl);

      $operationLinks = t('@editLink @deleteLink', ['@editLink' => $quickEditLink, '@deleteLink' => $deleteLink]);

      $form['table'][$regimenTipoId]['tipo_regimen'] = [
        '#plain_text' => $regimenTipoDTO->getTipoRegimen(),
      ];
      $form['table'][$regimenTipoId]['activo'] = [
        '#plain_text' => $regimenTipoDTO->getActivoString(),
      ];
      $form['table'][$regimenTipoId]['usuario_alta'] = [
        '#plain_text' => $regimenTipoDTO->getUsuarioAlta()->getUsername(),
      ];
      $form['table'][$regimenTipoId]['fecha_alta'] = [
        '#plain_text' => $regimenTipoDTO->getFechaAlta(),
      ];
      $form['table'][$regimenTipoId]['usuario_mod'] = [
        '#plain_text' => $regimenTipoDTO->getUsuarioMod()->getUsername(),
      ];
      $form['table'][$regimenTipoId]['fecha_mod'] = [
        '#plain_text' => $regimenTipoDTO->getFechaMod(),
      ];
      $form['table'][$regimenTipoId]['actions'] = [
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
