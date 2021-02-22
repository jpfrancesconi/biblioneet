<?php

namespace Drupal\io_generic_abml\Form;

use Drupal;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Database\Connection;
use Drupal\Component\Utility\Html;
use Drupal\file\Entity\File;

/**
 * Entity list in tableselect format.
 */
class EntityTableForm implements FormInterface {
  /**
   * Databse Connection.
   *
   * @var \Drupal\Core\Database\Connection
   */

  protected $db;

  /**
   * Search String.
   *
   * @var string
   */

  private $searchKey;

  /**
   * Entity name String.
   *
   * @var string
   */

  private $entity;

  /**
   * Constructs the EntityTableForm.
   *
   * @param \Drupal\Core\Database\Connection $con
   *   The database connection.
   * @param string $search_key
   *   The search string.
   */
  public function __construct(Connection $con, $search_key = '', $entity = NULL) {
    $this->db = $con;
    $this->searchKey = $search_key;
    $this->entity = $entity;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
        $container->get('database')
      );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'entity_table_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    global $base_url;

    $query = $this->db->select($this->entity, 'e')
      ->fields('e')
      ->extend('Drupal\Core\Database\Query\TableSortExtender')
      ->extend('Drupal\Core\Database\Query\PagerSelectExtender');
    //$query->leftJoin('bn_countries', 'c', 'c.id = a.nationality');
    //$query->orderByHeader($header);
    $config = Drupal::config('entity.settings');
    $limit = ($config->get('page_limit')) ? $config->get('page_limit') : 10;
    $query->limit($limit);

    $search_key = $this->searchKey;
    if (!empty($this->searchKey)) {
      $query->condition('e.tipo', "%" .
        Html::escape($search_key) . "%", 'LIKE');
    }
    $results = $query->execute();
    $rows = [];
    foreach ($results as $row) {
      $view_url = Url::fromRoute(
        'io_generic_abml.entity.add',
        ['entity' => $row->id, 'js' => 'nojs']
      );
      $drop_button = [
        '#type' => 'dropbutton',
        '#links' => [
          'view' => [
            'title' => t('VER'),
            'url' => $view_url,
          ],
          'edit' => [
            'title' => t('EDITAR'),
            'url' => Url::fromRoute('io_generic_abml.entity.add', ['employee' => $row->id]),
          ],
          'delete' => [
            'title' => t('BORRAR'),
            'url' => Url::fromRoute('io_generic_abml.entity.add', ['id' => $row->id]),
          ],
          /*'quick_edit' => [
            'title' => t('Quick Edit'),
            'url' => Url::fromRoute(
              'employee.quickedit',
              ['employee' => $row->id],
              $ajax_link_attributes
            ),
          ],*/
        ],
      ];
      $rowKeys  = $row;
      foreach ($row as $k => $r) {
        if($k == 'id')
          $rows[$r] = [[sprintf("%04s", $r)],];
        else
          array_push($rows[$row->id], [$k => $r]);
      }
      array_push($rows[$row->id], ['actions' => [
          'data' => $drop_button,
        ],]);
    }

    // Table header.
    $header = [];
    foreach ($rowKeys as $key => $value) {
      array_push($header, ['data' => t($key), 'field' => 'e.'.$key]);
    }
    // set last header OPERACIONES
    array_push($header, 'OPERACIONES');

    /*$form['action'] = [
      '#type' => 'select',
      '#title' => t('Action'),
      '#options' => [
        'delete' => 'Delete Selected',
        'activate' => 'Activate Selected',
        'block' => 'Block Selected',
      ],
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'Apply to selected items',
      '#prefix' => '<div class="form-actions js-form-wrapper form-wrapper">',
      '#suffix' => '</div>',
    ];*/

    $form['table'] = [
      '#type' => 'tableselect',
      '#header' => $header,
      '#empty' => t('La lista está vacía'),
      '#options' => $rows,
      '#attributes' => [
        'id' => 'table',
      ],
    ];

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
