<?php

namespace Drupal\io_generic_abml\Form\FrecuenciaFechaTipo;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

use Drupal\io_generic_abml\DAOs\FrecuenciaFechaTipoDAO;
use Drupal\io_generic_abml\Form\GenericTableForm;

/**
 * Entity list in tableselect format.
 */
class FrecuenciaFechaTipoTableForm extends GenericTableForm {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'frecuencia_fecha_tipo_table_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    global $base_url;

    $form = parent::buildForm($form, $form_state);

    // Table header.
    $header = [
      ['data' => t('FRECUENCIA'), 'field' => 'fft.frecuencia'],
      ['data' => t('FUNC. CALCULO'), 'field' => 'fft.funcion_calculo'],
      ['data' => t('1er PARAM'), 'field' => 'fft.param_1'],
      ['data' => t('2do PARAM'), 'field' => 'fft.param_2'],
      ['data' => t('3er PARAM'), 'field' => 'fft.param_3'],
      ['data' => t('ACTIVO'), 'field' => 'fft.activo'],
      ['data' => t('CREADOR'), 'field' => 'fft.usuario_alta'],
      ['data' => t('FECHA ALTA'), 'field' => 'fft.fecha_alta'],
      ['data' => t('MODIFICO'), 'field' => 'fft.usuario_mod'],
      ['data' => t('FECHA MOD.'), 'field' => 'fft.fecha_mod'],
      'actions' => 'OPERACIONES',
    ];

    // Get EquipoTipos by search property
    $search_key = $this->searchKey;
    $counter = [];
    $results = [];
    if (!empty($this->searchKey)) {
      $fullResults = FrecuenciaFechaTipoDAO::getAll($header, $search_key);
    } else {
      $fullResults = FrecuenciaFechaTipoDAO::getAll($header);
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
       * @var \Drupal\io_generic_abml\DTOs\FrecuenciaFechaTipoDTO
       *  $frecuenciaFechaTipoDTO
       */
      $frecuenciaFechaTipoDTO = $row;
      $frecuenciaFechaTipoId = $frecuenciaFechaTipoDTO->getId();
      $ajax_link_attributes = [
        'attributes' => [
          'class' => 'use-ajax',
          'data-dialog-type' => 'modal',
          'data-dialog-options' => ['width' => 700, 'height' => 400],
        ],
      ];

      $ajax_link_attributes['attributes']['title'] = t('Editar');
      $editUrl = Url::fromRoute('io_generic_abml.frecuencia_fecha_tipo.edit.getmodal', ['frecuencia_fecha_tipo_id' => $frecuenciaFechaTipoId, 'js' => 'ajax'], $ajax_link_attributes);
      $quickEditLink = \Drupal::service('link_generator')->generate(t('<i class="far fa-edit"></i>'), $editUrl);

      // prepare delete link
      $ajax_link_attributes['attributes']['title'] = t('Eliminar');
      $deletetUrl = Url::fromRoute('io_generic_abml.frecuencia_fecha_tipo.delete.getmodal', ['frecuencia_fecha_tipo_id' => $frecuenciaFechaTipoId, 'js' => 'ajax'], $ajax_link_attributes);
      $deleteLink = \Drupal::service('link_generator')->generate(t('<i class="far fa-trash-alt"></i>'), $deletetUrl);

      $operationLinks = t('@editLink @deleteLink', ['@editLink' => $quickEditLink, '@deleteLink' => $deleteLink]);

      $form['table'][$frecuenciaFechaTipoId]['frecuencia_fecha_tipo'] = [
        '#plain_text' => $frecuenciaFechaTipoDTO->getFrecuencia(),
      ];
      $form['table'][$frecuenciaFechaTipoId]['funcion_calculo'] = [
        '#plain_text' => $frecuenciaFechaTipoDTO->getFuncionCalculo(),
      ];
      $form['table'][$frecuenciaFechaTipoId]['param_1'] = [
        '#plain_text' => $frecuenciaFechaTipoDTO->getParam_1(),
      ];
      $form['table'][$frecuenciaFechaTipoId]['param_2'] = [
        '#plain_text' => $frecuenciaFechaTipoDTO->getParam_2(),
      ];
      $form['table'][$frecuenciaFechaTipoId]['param_3'] = [
        '#plain_text' => $frecuenciaFechaTipoDTO->getParam_3(),
      ];
      $form['table'][$frecuenciaFechaTipoId]['activo'] = [
        '#plain_text' => $frecuenciaFechaTipoDTO->getActivoString(),
      ];
      $form['table'][$frecuenciaFechaTipoId]['usuario_alta'] = [
        '#plain_text' => $frecuenciaFechaTipoDTO->getUsuarioAlta()->getUsername(),
      ];
      $form['table'][$frecuenciaFechaTipoId]['fecha_alta'] = [
        '#plain_text' => $frecuenciaFechaTipoDTO->getFechaAlta(),
      ];
      $form['table'][$frecuenciaFechaTipoId]['usuario_mod'] = [
        '#plain_text' => $frecuenciaFechaTipoDTO->getUsuarioMod()->getUsername(),
      ];
      $form['table'][$frecuenciaFechaTipoId]['fecha_mod'] = [
        '#plain_text' => $frecuenciaFechaTipoDTO->getFechaMod(),
      ];
      $form['table'][$frecuenciaFechaTipoId]['actions'] = [
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
