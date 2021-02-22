<?php

namespace Drupal\io_generic_abml\Form\EquipoTipos;

use Drupal\io_generic_abml\Form\GenericTableForm;

use Drupal\io_generic_abml\DAOs\EquipoTiposDAO;

use Drupal;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;


/**
 * Entity list in tableselect format.
 */
class EquipoTiposTableForm extends GenericTableForm {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'equipo_tipos_table_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    global $base_url;

    $form = parent::buildForm($form, $form_state);

    // Table header.
    $header = [
      ['data' => t('TIPO'), 'field' => 'et.tipo'],
      ['data' => t('ACTIVO'), 'field' => 'et.activo'],
      ['data' => t('CREADOR'), 'field' => 'et.usuario_alta'],
      ['data' => t('CREADO'), 'field' => 'et.fecha_alta'],
      ['data' => t('MODIFICO'), 'field' => 'et.usuario_mod'],
      ['data' => t('MODIFICADO'), 'field' => 'et.fecha_mod'],
      'actions' => 'OPERACIONES',
    ];

    // Get EquipoTipos by search property
    $search_key = $this->searchKey;
    $counter = [];
    $results = [];
    if (!empty($this->searchKey)) {
      $fullResults = EquipoTiposDAO::getAll($header, $search_key);
    } else {
      $fullResults = EquipoTiposDAO::getAll($header);
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
      /** @var \Drupal\io_generic_abml\DTOs\EquipoTipoDTO $equipoTipoDTO */
      $equipoTipoDTO = $row;

      $ajax_link_attributes = [
        'attributes' => [
          'class' => 'use-ajax',
          'data-dialog-type' => 'modal',
          'data-dialog-options' => ['width' => 700, 'height' => 400],
        ],
      ];
      $ajax_link_attributes['attributes']['title'] = t('Editar');
      $editUrl = Url::fromRoute('io_generic_abml.equipo_tipo.edit.getmodal', ['equipo_tipo_id' => $equipoTipoDTO->getId(), 'js' => 'ajax'], $ajax_link_attributes);
      $quickEditLink = \Drupal::service('link_generator')->generate(t('<i class="fas fa-edit"></i>'), $editUrl);

      // prepare delete link
      $ajax_link_attributes['attributes']['title'] = t('Eliminar');
      $deletetUrl = Url::fromRoute('io_generic_abml.equipo_tipo.delete.getmodal', ['equipo_tipo_id' => $equipoTipoDTO->getId(), 'js' => 'ajax'], $ajax_link_attributes);
      $deleteLink = \Drupal::service('link_generator')->generate(t('<i class="far fa-trash-alt"></i>'), $deletetUrl);

      // prepare tipos de falla link
      $tiposFallaUrl = Url::fromRoute('io_generic_abml.falla_tipo.list', ['equipo_tipo_id' => $equipoTipoDTO->getId()], ['attributes' => ['title' => 'Tipos de falla']]);
      $tiposFallaLink = \Drupal::service('link_generator')->generate(t('<i class="fas fa-tools"></i>'), $tiposFallaUrl);

      $operationLinks = t('@linkEdit @linkDelete @linkTiposFalla', array('@linkEdit' => $quickEditLink, '@linkDelete' => $deleteLink, '@linkTiposFalla' => $tiposFallaLink));

      $form['table'][$equipoTipoDTO->getId()]['tipo'] = [
        '#plain_text' => $equipoTipoDTO->getTipo(),
      ];
      $form['table'][$equipoTipoDTO->getId()]['activo'] = [
        '#plain_text' => $equipoTipoDTO->getActivoString(),
      ];
      $form['table'][$equipoTipoDTO->getId()]['usuario_alta'] = [
        '#plain_text' => $equipoTipoDTO->getUsuarioAlta()->getUsername(),
      ];
      $form['table'][$equipoTipoDTO->getId()]['fecha_alta'] = [
        '#plain_text' => $equipoTipoDTO->getFechaAlta(),
      ];
      $form['table'][$equipoTipoDTO->getId()]['usuario_mod'] = [
        '#plain_text' => ($equipoTipoDTO->getUsuarioMod() ? $equipoTipoDTO->getUsuarioMod()->getUsername() : NULL),
      ];
      $form['table'][$equipoTipoDTO->getId()]['fecha_mod'] = [
        '#plain_text' => $equipoTipoDTO->getFechaMod(),
      ];
      $form['table'][$equipoTipoDTO->getId()]['actions'] = [
        '#markup' =>$operationLinks,
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
