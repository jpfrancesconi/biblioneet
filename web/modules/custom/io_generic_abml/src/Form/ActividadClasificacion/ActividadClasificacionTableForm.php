<?php

namespace Drupal\io_generic_abml\Form\ActividadClasificacion;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

use Drupal\io_generic_abml\DAOs\ActividadClasificacionDAO;
use Drupal\io_generic_abml\Form\GenericTableForm;

/**
 * Entity list in tableselect format.
 */
class ActividadClasificacionTableForm extends GenericTableForm {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'actividad_clasificacion_table_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    global $base_url;

    $form = parent::buildForm($form, $form_state);

    // Table header.
    $header = [
      ['data' => t('TIPO'), 'field' => 'ac.tipo'],
      ['data' => t('ACTIVO'), 'field' => 'ac.activo'],
      ['data' => t('CREADOR'), 'field' => 'ac.usuario_alta'],
      ['data' => t('FECHA ALTA'), 'field' => 'ac.fecha_alta'],
      ['data' => t('MODIFICO'), 'field' => 'ac.usuario_mod'],
      ['data' => t('FECHA MOD.'), 'field' => 'ac.fecha_mod'],
      'actions' => 'OPERACIONES',
    ];

    // Get EquipoTipos by search property
    $search_key = $this->searchKey;
    $counter = [];
    $results = [];
    if (!empty($this->searchKey)) {
      $fullResults = ActividadClasificacionDAO::getAll($header, $search_key);
    } else {
      $fullResults = ActividadClasificacionDAO::getAll($header);
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
       * @var \Drupal\io_generic_abml\DTOs\ActividadClasificacionDTO
       *  $actividadClasificacionDTO
       */
      $actividadClasificacionDTO = $row;
      $actividadClasificacionId = $actividadClasificacionDTO->getId();
      $ajax_link_attributes = [
        'attributes' => [
          'class' => 'use-ajax',
          'data-dialog-type' => 'modal',
          'data-dialog-options' => ['width' => 700, 'height' => 400],
        ],
      ];

      $ajax_link_attributes['attributes']['title'] = t('Editar');
      $editUrl = Url::fromRoute('io_generic_abml.actividad_clasificacion.edit.getmodal', ['actividad_clasificacion_id' => $actividadClasificacionId, 'js' => 'ajax'], $ajax_link_attributes);
      $quickEditLink = \Drupal::service('link_generator')->generate(t('<i class="far fa-edit"></i>'), $editUrl);

      // prepare delete link
      $ajax_link_attributes['attributes']['title'] = t('Eliminar');
      $deletetUrl = Url::fromRoute('io_generic_abml.actividad_clasificacion.delete.getmodal', ['actividad_clasificacion_id' => $actividadClasificacionId, 'js' => 'ajax'], $ajax_link_attributes);
      $deleteLink = \Drupal::service('link_generator')->generate(t('<i class="far fa-trash-alt"></i>'), $deletetUrl);

      $operationLinks = t('@editLink @deleteLink', ['@editLink' => $quickEditLink, '@deleteLink' => $deleteLink]);

      $form['table'][$actividadClasificacionId]['actividad_clasificacion'] = [
        '#plain_text' => $actividadClasificacionDTO->getTipo(),
      ];
      $form['table'][$actividadClasificacionId]['activo'] = [
        '#plain_text' => $actividadClasificacionDTO->getActivoString(),
      ];
      $form['table'][$actividadClasificacionId]['usuario_alta'] = [
        '#plain_text' => $actividadClasificacionDTO->getUsuarioAlta()->getUsername(),
      ];
      $form['table'][$actividadClasificacionId]['fecha_alta'] = [
        '#plain_text' => $actividadClasificacionDTO->getFechaAlta(),
      ];
      $form['table'][$actividadClasificacionId]['usuario_mod'] = [
        '#plain_text' => $actividadClasificacionDTO->getUsuarioMod()->getUsername(),
      ];
      $form['table'][$actividadClasificacionId]['fecha_mod'] = [
        '#plain_text' => $actividadClasificacionDTO->getFechaMod(),
      ];
      $form['table'][$actividadClasificacionId]['actions'] = [
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
