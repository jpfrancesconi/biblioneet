<?php

namespace Drupal\io_generic_abml\Form\Autor;

use Drupal;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Database\Connection;

use Drupal\Component\Utility\Html;

use Drupal\file\Entity\File;

use Drupal\io_generic_abml\Form\GenericTableForm;
use Drupal\io_generic_abml\DAOs\AuthorDAO;

use Drupal\io_generic_abml\DTOs\AuthorDTO;

/**
 * Author list in tableselect format.
 */
class AuthorTableForm extends GenericTableForm {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'author_table_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    global $base_url;

    // Get Authors by search property
    $search_key = $this->searchKey;
    $counter = [];
    $results = [];
    if (!empty($this->searchKey)) {
      $fullResults = AuthorDAO::getAll2($search_key);
    } else {
      $fullResults = AuthorDAO::getAll2();
    }

    // Get the counter data to be shown in the counter summary section.
    $counter = $fullResults['counter'];
    // Get the DTOs object to be shown in the results table.
    $results = $fullResults['resultsDTO'];
    // Add counter summary section
    $form['#results_counter'] = [
      '#markup' => '<p>Mostrando registros ' . $counter['start'] . ' - ' . $counter['end'] . ' de ' . $counter['total'] . ' registros</p>',
    ];

    $rows = [];
    foreach ($results as $row) {
      /** @var \Drupal\io_generic_abml\DTOs\AuthorDTO $authorDTO */
      $authorDTO = $row;

      $ajax_link_attributes = [
        'attributes' => [
          'class' => 'use-ajax',
          'data-dialog-type' => 'modal',
          'data-dialog-options' => ['width' => 700, 'height' => 400],
        ],
      ];
      $view_url = Url::fromRoute('io_generic_abml.author.view',
        ['author' => $authorDTO->getId(), 'js' => 'nojs']);
      $ajax_view_url = Url::fromRoute(
        'io_generic_abml.author.view',
        ['author' => $authorDTO->getId(), 'js' => 'ajax'], $ajax_link_attributes);
      $ajax_view_link = Link::fromTextAndUrl(t($authorDTO->getFirstName()), $ajax_view_url);

      $view_link = Link::fromTextAndUrl(t('View'), Url::fromRoute(
        'io_generic_abml.author.view',
        ['author' => $authorDTO->getId(), 'js' => 'nojs']));
      /*$mail_url = Url::fromRoute('employee.sendmail', ['employee' => $row->id],
        $ajax_link_attributes);*/
      $drop_button = [
        '#type' => 'dropbutton',
        '#links' => [
          'edit' => [
            'title' => t('Editar'),
            'url' => Url::fromRoute('io_generic_abml.author.edit', ['author' => $authorDTO->getId()]),
          ],
          'delete' => [
            'title' => t('Eliminar'),
            'url' => Url::fromRoute('io_generic_abml.author.delete', ['id' => $authorDTO->getId()]),
          ],
          /*'quick_edit' => [
            'title' => t('Quick Edit'),
            'url' => Url::fromRoute('employee.quickedit', ['employee' => $row->id],
              $ajax_link_attributes),
          ],*/
        ],
      ];
      $profile_pic = FALSE;
      if($authorDTO->getPicture())
        $profile_pic = File::load($authorDTO->getPicture());

      if ($profile_pic) {
        $style = Drupal::entityTypeManager()->getStorage('image_style')->load('mobile_1x_560px_');
        $profile_pic_url = $style->buildUrl($profile_pic->getFileUri());
      }
      else {
        $module_handler = Drupal::service('module_handler');
        $path = $module_handler->getModule('io_generic_abml')->getPath();
        $profile_pic_url = $base_url . '/' . $path . '/assets/profile_placeholder.png';
      }
      $rows[$authorDTO->getId()] = [
        'id' => sprintf("%04s", $authorDTO->getId()),
        'picture' => [
          'data' => [
            '#type' => 'html_tag',
            '#tag' => 'img',
            '#attributes' => ['src' => $profile_pic_url],
          ],
        ],
        //[$ajax_view_link],
        'first_name' => $authorDTO->getFirstName(),
        'last_name' => $authorDTO->getLastName(),
        'nationality' =>  ($authorDTO->getNationality() !== null) ? $authorDTO->getNationality()->getEnShortName() : null,
        'status' => ($authorDTO->getStatus()) ? 'Activo' : 'Inactivo',
        'status_id' => $authorDTO->getStatus(),
        [$view_link],
        'description' => $authorDTO->getDescription(),
        //'actions' => [
        //  'data' => $drop_button,
        //],
      ];
    }

    $form['#theme'] = 'authors_list_page';
    $form['#results'] = $rows;

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
    /*$selected_ids = array_filter($form_state->getValue('table'));
    $selected_ids = array_map(function ($val) {
      $record = EmployeeStorage::load($val);
      return $record->name;
    }, $selected_ids);
    if (!array_filter($selected_ids)) {
      drupal_set_message(t('No employee record to selected'), 'error');
      $form_state->setRedirect('employee.list');
      return;
    }
    else {
      $request = Drupal::request();
      $session = $request->getSession();
      $session->set('employee', [
        'selected_items' => $selected_ids,
      ]);
      $form_state->setRedirect('employee.action', ['action' => $form_state->getValue('action')]);
      return;
    }*/
  }

}
