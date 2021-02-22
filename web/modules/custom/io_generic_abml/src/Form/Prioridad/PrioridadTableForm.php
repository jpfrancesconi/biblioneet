<?php

namespace Drupal\io_generic_abml\Form\Prioridad;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

use Drupal\io_generic_abml\DAOs\PrioridadDAO;
use Drupal\io_generic_abml\Form\GenericTableForm;
/**
 * Entity list in tableselect format.
 */
class PrioridadTableForm extends GenericTableForm {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'prioridad_table_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

    // Table header.
    $header = [
      ['data' => t('PRIORIDAD'), 'field' => 'p.prioridad'],
      ['data' => t('ACTIVO'), 'field' => 'p.activo'],
      ['data' => t('CREADOR'), 'field' => 'p.usuario_alta'],
      ['data' => t('FECHA ALTA'), 'field' => 'p.fecha_alta'],
      ['data' => t('MODIFICO'), 'field' => 'p.usuario_mod'],
      ['data' => t('FECHA MOD.'), 'field' => 'p.fecha_mod'],
      'actions' => 'OPERACIONES',
    ];

    // Get EquipoTipos by search property
    $search_key = $this->searchKey;
    $counter = [];
    $results = [];
    if (!empty($this->searchKey)) {
      $fullResults = PrioridadDAO::getAll($header, $search_key);
    } else {
      $fullResults = PrioridadDAO::getAll($header);
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
       * @var \Drupal\io_generic_abml\DTOs\PrioridadDTO
       *  $prioridadDTO
       */
      $prioridadDTO = $row;
      $prioridadId = $prioridadDTO->getId();
      $ajax_link_attributes = [
        'attributes' => [
          'class' => 'use-ajax',
          'data-dialog-type' => 'modal',
          'data-dialog-options' => ['width' => 700, 'height' => 400],
        ],
      ];

      $ajax_link_attributes['attributes']['title'] = t('Editar');
      $editUrl = Url::fromRoute('io_generic_abml.prioridad.edit.getmodal', ['prioridad_id' => $prioridadId, 'js' => 'ajax'], $ajax_link_attributes);
      $quickEditLink = \Drupal::service('link_generator')->generate(t('<i class="far fa-edit"></i>'), $editUrl);

      // prepare delete link
      $ajax_link_attributes['attributes']['title'] = t('Eliminar');
      $deletetUrl = Url::fromRoute('io_generic_abml.prioridad.delete.getmodal', ['prioridad_id' => $prioridadId, 'js' => 'ajax'], $ajax_link_attributes);
      $deleteLink = \Drupal::service('link_generator')->generate(t('<i class="far fa-trash-alt"></i>'), $deletetUrl);

      $operationLinks = t('@editLink @deleteLink', ['@editLink' => $quickEditLink, '@deleteLink' => $deleteLink]);

      $form['table'][$prioridadId]['prioridad'] = [
        '#plain_text' => $prioridadDTO->getPrioridad(),
      ];
      $form['table'][$prioridadId]['activo'] = [
        '#plain_text' => $prioridadDTO->getActivoString(),
      ];
      $form['table'][$prioridadId]['usuario_alta'] = [
        '#plain_text' => $prioridadDTO->getUsuarioAlta()->getUsername(),
      ];
      $form['table'][$prioridadId]['fecha_alta'] = [
        '#plain_text' => $prioridadDTO->getFechaAlta(),
      ];
      $form['table'][$prioridadId]['usuario_mod'] = [
        '#plain_text' => $prioridadDTO->getUsuarioMod()->getUsername(),
      ];
      $form['table'][$prioridadId]['fecha_mod'] = [
        '#plain_text' => $prioridadDTO->getFechaMod(),
      ];
      $form['table'][$prioridadId]['actions'] = [
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
