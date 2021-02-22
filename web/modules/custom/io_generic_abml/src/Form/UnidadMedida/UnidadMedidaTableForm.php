<?php

namespace Drupal\io_generic_abml\Form\UnidadMedida;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

use Drupal\io_generic_abml\Form\GenericTableForm;
use Drupal\io_generic_abml\DAOs\UnidadMedidaDAO;

/**
 * Entity list in tableselect format.
 */
class UnidadMedidaTableForm extends GenericTableForm {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'unidad_medida_table_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    global $base_url;

    $form = parent::buildForm($form, $form_state);

    // Table header.
    $header = [
      ['data' => t('UNIDAD DE MEDIDA'), 'field' => 'um.unidad_medida'],
      ['data' => t('CREADOR'), 'field' => 'um.usuario_alta'],
      ['data' => t('FECHA ALTA'), 'field' => 'um.fecha_alta'],
      ['data' => t('MODIFICO'), 'field' => 'um.usuario_mod'],
      ['data' => t('FECHA MOD.'), 'field' => 'um.fecha_mod'],
      'actions' => 'OPERACIONES',
    ];
    // Get EquipoTipos by search property
    $search_key = $this->searchKey;
    $counter = [];
    $results = [];
    if (!empty($this->searchKey)) {
      $fullResults = UnidadMedidaDAO::getAll($header, $search_key);
    } else {
      $fullResults = UnidadMedidaDAO::getAll($header);
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
       * @var \Drupal\io_generic_abml\DTOs\UnidadMedidaDTO
       *  $unidadMedidaDTO
       */
      $unidadMedidaDTO = $row;
      $unidadMedidaId = $unidadMedidaDTO->getId();
      $ajax_link_attributes = [
        'attributes' => [
          'class' => 'use-ajax',
          'data-dialog-type' => 'modal',
          'data-dialog-options' => ['width' => 700, 'height' => 400],
        ],
      ];

      $ajax_link_attributes['attributes']['title'] = t('Editar');
      $editUrl = Url::fromRoute('io_generic_abml.unidad_medida.edit.getmodal', ['unidad_medida_id' => $unidadMedidaId, 'js' => 'ajax'], $ajax_link_attributes);
      $quickEditLink = \Drupal::service('link_generator')->generate(t('<i class="far fa-edit"></i>'), $editUrl);

      // prepare delete link
      $ajax_link_attributes['attributes']['title'] = t('Eliminar');
      $deletetUrl = Url::fromRoute('io_generic_abml.unidad_medida.delete.getmodal', ['unidad_medida_id' => $unidadMedidaId, 'js' => 'ajax'], $ajax_link_attributes);
      $deleteLink = \Drupal::service('link_generator')->generate(t('<i class="far fa-trash-alt"></i>'), $deletetUrl);

      $operationLinks = t('@editLink @deleteLink', ['@editLink' => $quickEditLink, '@deleteLink' => $deleteLink]);

      $form['table'][$unidadMedidaId]['prioridad'] = [
        '#plain_text' => $unidadMedidaDTO->getUnidadMedida(),
      ];
      $form['table'][$unidadMedidaId]['usuario_alta'] = [
        '#plain_text' => $unidadMedidaDTO->getUsuarioAlta()->getUsername(),
      ];
      $form['table'][$unidadMedidaId]['fecha_alta'] = [
        '#plain_text' => $unidadMedidaDTO->getFechaAlta(),
      ];
      $form['table'][$unidadMedidaId]['usuario_mod'] = [
        '#plain_text' => $unidadMedidaDTO->getUsuarioMod()->getUsername(),
      ];
      $form['table'][$unidadMedidaId]['fecha_mod'] = [
        '#plain_text' => $unidadMedidaDTO->getFechaMod(),
      ];
      $form['table'][$unidadMedidaId]['actions'] = [
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
