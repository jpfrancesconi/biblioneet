<?php

namespace Drupal\io_generic_abml\Form\FallaCausa;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

use Drupal\io_generic_abml\DAOs\FallaCausaDAO;
use Drupal\io_generic_abml\Form\GenericTableForm;

/**
 * Entity list in tableselect format.
 */
class FallaCausaTableForm extends GenericTableForm {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'falla_causa_table_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    global $base_url;

    $form = parent::buildForm($form, $form_state);

    // Table header.
    $header = [
      ['data' => t('CAUSA'), 'field' => 'fc.causa'],
      ['data' => t('ACTIVO'), 'field' => 'fc.activo'],
      ['data' => t('CREADOR'), 'field' => 'fc.usuario_alta'],
      ['data' => t('FECHA ALTA'), 'field' => 'fc.fecha_alta'],
      ['data' => t('MODIFICO'), 'field' => 'fc.usuario_mod'],
      ['data' => t('FECHA MOD.'), 'field' => 'fc.fecha_mod'],
      'actions' => 'OPERACIONES',
    ];

    // Get EquipoTipos by search property
    $search_key = $this->searchKey;
    $counter = [];
    $results = [];
    if (!empty($this->searchKey)) {
      $fullResults = FallaCausaDAO::getAll($header, $search_key);
    } else {
      $fullResults = FallaCausaDAO::getAll($header);
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
       * @var \Drupal\io_generic_abml\DTOs\FallaCausaDTO
       *  $fallaCausaDTO
       */
      $fallaCausaDTO = $row;
      $fallaCausaId = $fallaCausaDTO->getId();
      $ajax_link_attributes = [
        'attributes' => [
          'class' => 'use-ajax',
          'data-dialog-type' => 'modal',
          'data-dialog-options' => ['width' => 700, 'height' => 400],
        ],
      ];

      $ajax_link_attributes['attributes']['title'] = t('Editar');
      $editUrl = Url::fromRoute('io_generic_abml.falla_causa.edit.getmodal', ['falla_causa_id' => $fallaCausaId, 'js' => 'ajax'], $ajax_link_attributes);
      $quickEditLink = \Drupal::service('link_generator')->generate(t('<i class="far fa-edit"></i>'), $editUrl);

      // prepare delete link
      $ajax_link_attributes['attributes']['title'] = t('Eliminar');
      $deletetUrl = Url::fromRoute('io_generic_abml.falla_causa.delete.getmodal', ['falla_causa_id' => $fallaCausaId, 'js' => 'ajax'], $ajax_link_attributes);
      $deleteLink = \Drupal::service('link_generator')->generate(t('<i class="far fa-trash-alt"></i>'), $deletetUrl);

      $operationLinks = t('@editLink @deleteLink', ['@editLink' => $quickEditLink, '@deleteLink' => $deleteLink]);

      $form['table'][$fallaCausaId]['falla_causa'] = [
        '#plain_text' => $fallaCausaDTO->getCausaFalla(),
      ];
      $form['table'][$fallaCausaId]['activo'] = [
        '#plain_text' => $fallaCausaDTO->getActivoString(),
      ];
      $form['table'][$fallaCausaId]['usuario_alta'] = [
        '#plain_text' => $fallaCausaDTO->getUsuarioAlta()->getUsername(),
      ];
      $form['table'][$fallaCausaId]['fecha_alta'] = [
        '#plain_text' => $fallaCausaDTO->getFechaAlta(),
      ];
      $form['table'][$fallaCausaId]['usuario_mod'] = [
        '#plain_text' => $fallaCausaDTO->getUsuarioMod()->getUsername(),
      ];
      $form['table'][$fallaCausaId]['fecha_mod'] = [
        '#plain_text' => $fallaCausaDTO->getFechaMod(),
      ];
      $form['table'][$fallaCausaId]['actions'] = [
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
