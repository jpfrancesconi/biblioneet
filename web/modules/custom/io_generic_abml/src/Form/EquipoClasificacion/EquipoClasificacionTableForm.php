<?php

namespace Drupal\io_generic_abml\Form\EquipoClasificacion;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

use Drupal\io_generic_abml\DAOs\EquipoClasificacionDAO;
use Drupal\io_generic_abml\Form\GenericTableForm;

/**
 * Entity list in tableselect format.
 */
class EquipoClasificacionTableForm extends GenericTableForm {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'equipo_clasificacion_table_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    global $base_url;

    $form = parent::buildForm($form, $form_state);

    // Table header.
    $header = [
      ['data' => t('CLASIFICACIÓN'), 'field' => 'ec.clasificacion'],
      ['data' => t('ACTIVO'), 'field' => 'ec.activo'],
      ['data' => t('CREADOR'), 'field' => 'ec.usuario_alta'],
      ['data' => t('FECHA ALTA'), 'field' => 'ec.fecha_alta'],
      ['data' => t('MODIFICO'), 'field' => 'ec.usuario_mod'],
      ['data' => t('FECHA MOD.'), 'field' => 'ec.fecha_mod'],
      'actions' => 'OPERACIONES',
    ];

    // Get EquipoTipos by search property
    $search_key = $this->searchKey;
    $counter = [];
    $results = [];
    if (!empty($this->searchKey)) {
      $fullResults = EquipoClasificacionDAO::getAll($header, $search_key);
    } else {
      $fullResults = EquipoClasificacionDAO::getAll($header);
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
       * @var \Drupal\io_generic_abml\DTOs\EquipoClasificacionDTO
       *  $equipoClasificacionDTO
       */
      $equipoClasificacionDTO = $row;
      $equipoClasificacionId = $equipoClasificacionDTO->getId();
      $ajax_link_attributes = [
        'attributes' => [
          'class' => 'use-ajax',
          'data-dialog-type' => 'modal',
          'data-dialog-options' => ['width' => 700, 'height' => 400],
        ],
      ];

      $ajax_link_attributes['attributes']['title'] = t('Editar');
      $editUrl = Url::fromRoute('io_generic_abml.equipo_clasificacion.edit.getmodal', ['equipo_clasificacion_id' => $equipoClasificacionId, 'js' => 'ajax'], $ajax_link_attributes);
      $quickEditLink = \Drupal::service('link_generator')->generate(t('<i class="far fa-edit"></i>'), $editUrl);

      // prepare delete link
      $ajax_link_attributes['attributes']['title'] = t('Eliminar');
      $deletetUrl = Url::fromRoute('io_generic_abml.equipo_clasificacion.delete.getmodal', ['equipo_clasificacion_id' => $equipoClasificacionId, 'js' => 'ajax'], $ajax_link_attributes);
      $deleteLink = \Drupal::service('link_generator')->generate(t('<i class="far fa-trash-alt"></i>'), $deletetUrl);

      $operationLinks = t('@editLink @deleteLink', ['@editLink' => $quickEditLink, '@deleteLink' => $deleteLink]);

      $form['table'][$equipoClasificacionId]['equipo_clasificacion'] = [
        '#plain_text' => $equipoClasificacionDTO->getClasificacion(),
      ];
      $form['table'][$equipoClasificacionId]['activo'] = [
        '#plain_text' => $equipoClasificacionDTO->getActivoString(),
      ];
      $form['table'][$equipoClasificacionId]['usuario_alta'] = [
        '#plain_text' => $equipoClasificacionDTO->getUsuarioAlta()->getUsername(),
      ];
      $form['table'][$equipoClasificacionId]['fecha_alta'] = [
        '#plain_text' => $equipoClasificacionDTO->getFechaAlta(),
      ];
      $form['table'][$equipoClasificacionId]['usuario_mod'] = [
        '#plain_text' => $equipoClasificacionDTO->getUsuarioMod()->getUsername(),
      ];
      $form['table'][$equipoClasificacionId]['fecha_mod'] = [
        '#plain_text' => $equipoClasificacionDTO->getFechaMod(),
      ];
      $form['table'][$equipoClasificacionId]['actions'] = [
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
