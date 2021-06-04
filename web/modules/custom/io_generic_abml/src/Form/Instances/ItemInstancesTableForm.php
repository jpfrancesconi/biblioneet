<?php

namespace Drupal\io_generic_abml\Form\Instances;

use Drupal;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Url;

use Drupal\io_generic_abml\Form\GenericTableForm;

use Drupal\io_generic_abml\DAOs\ItemDAO;
use Drupal\io_generic_abml\DAOs\InstanceDAO;

use Drupal\io_generic_abml\DTOs\InstanceDTO;

/**
 * Entity list in tableselect format.
 */
class ItemInstancesTableForm implements FormInterface {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'item_instances_table_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = null) {
    global $base_url;

    //$form = parent::buildForm($form, $form_state);
    if(isset($id)) {
      $itemDTO = ItemDAO::load($id);
      $form['title'] = [
        '#type' => 'markup',
        '#markup' => '<h4>Gestionando existencias del item: '. $itemDTO->getTitle() .' </h4>',
      ];
    }

    // Table header.
    $header = [
      ['data' => t('CÓDIGO'), 'field' => 'ins.id'],
      ['data' => t('INVENTARIO'), 'field' => 'ins.inv_code'],
      ['data' => t('ESTADO'), 'field' => 'ins.instance_status_id'],
      ['data' => t('SIGNATURA'), 'field' => 'ins.signature'],
      ['data' => t('CREADOR'), 'field' => 'ins.createdby'],
      ['data' => t('CREADO'), 'field' => 'ins.createdon'],
      ['data' => t('MODIFICÓ'), 'field' => 'ins.updatedby'],
      ['data' => t('MODIFICADO'), 'field' => 'ins.updatedon'],
      'actions' => 'OPERACIONES',
    ];

    $fullResults = InstanceDAO::getInstancesFromItem($header, $id);

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
        'class' => ['table-sm', 'io-table-sm']
      ],
    ];

    // Iterate results to build a table results to be rendered
    foreach ($results as $row) {
      /** @var \Drupal\io_generic_abml\DTOs\instanceDTO $instanceDTO */
      $instanceDTO = $row;

      $ajax_link_attributes = [
        'attributes' => [
          'class' => 'use-ajax',
          'data-dialog-type' => 'modal',
          'data-dialog-options' => ['width' => 700, 'height' => 400],
        ],
      ];
      $ajax_link_attributes['attributes']['title'] = t('Editar');
      $editUrl = Url::fromRoute('io_generic_abml.items.instances.edit', ['id' => $instanceDTO->getItem()->getId(), 'idInstance' => $instanceDTO->getId(), 'js' => 'no']);
      $quickEditLink = \Drupal::service('link_generator')->generate(t('<i class="fas fa-edit"></i>'), $editUrl);

      // prepare delete link
      $ajax_link_attributes['attributes']['title'] = t('Eliminar');
      $deletetUrl = Url::fromRoute('io_generic_abml.items.instances.delete', ['id' => $instanceDTO->getId(), 'js' => 'no']);
      $deleteLink = \Drupal::service('link_generator')->generate(t('<i class="far fa-trash-alt"></i>'), $deletetUrl);

      // prepare Qr generator link
      $qrGenerateUrl = Url::fromRoute('io_generic_abml.items.instances.edit', ['id' => $instanceDTO->getId()], ['attributes' => ['title' => 'Generar QR']]);
      $qrGenerateLink = \Drupal::service('link_generator')->generate(t('<i class="fa fa-qrcode" aria-hidden="true"></i>'), $qrGenerateUrl);

      $operationLinks = t('@linkEdit @linkDelete @linkQrGenerate', array('@linkEdit' => $quickEditLink, '@linkDelete' => $deleteLink, '@linkQrGenerate' => $qrGenerateLink));

      $form['table'][$instanceDTO->getId()]['codigo'] = [
        '#plain_text' => $instanceDTO->getId(),
      ];
      $form['table'][$instanceDTO->getId()]['inventario'] = [
        '#plain_text' => $instanceDTO->getInvCode(),
      ];
      $form['table'][$instanceDTO->getId()]['instance_status'] = [
        '#plain_text' => $instanceDTO->getInstanceStatus()->getStatusName(),
      ];
      $form['table'][$instanceDTO->getId()]['signature'] = [
        '#plain_text' => $instanceDTO->getSignature(),
      ];
      $form['table'][$instanceDTO->getId()]['createdby'] = [
        '#plain_text' => $instanceDTO->getCreatedBy()->getUsername(),
      ];
      $form['table'][$instanceDTO->getId()]['createdon'] = [
        '#plain_text' => $instanceDTO->getCreatedOn(),
      ];
      $form['table'][$instanceDTO->getId()]['updatedby'] = [
        '#plain_text' => $instanceDTO->getUpdatedBy()->getUsername(),
      ];
      $form['table'][$instanceDTO->getId()]['updstedon'] = [
        '#plain_text' => $instanceDTO->getUpdatedOn(),
      ];
      $form['table'][$instanceDTO->getId()]['actions'] = [
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
