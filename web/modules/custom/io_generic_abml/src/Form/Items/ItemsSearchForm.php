<?php

namespace Drupal\io_generic_abml\Form\Items;

use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Items search form.
 */
class ItemsSearchForm implements FormInterface {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'items_search_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $item = NULL) {
    $form_state->setAlwaysProcess(FALSE);

    $form['#method'] = 'GET';
    $form['#token'] = FALSE;
    // The status messages that will contain any form errors.
    $form['search_item_type'] = [
      '#type' => 'select',
      //'#title' => 'FILTROS',
      '#prefix' => '<div class="input-group">',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#options' => [
        0 => 'TODOS',
        1 => 'TÍTULO',
        2 => 'AUTOR',
        3 => 'MATERIA',
        4 => 'ISBN',
      ],
      '#default_value' => $_GET['search_item_type'] ?? '0',
    ];
    $form['search_item'] = [
      '#type' => 'search',
      '#attributes' => [
        'placeholder' => 'Ingrese alguna palabra',
        'class' => ['form-control'],
      ],
      '#suffix' => '</div>',
      '#default_value' => $_GET['search_item'] ?? '',
    ];

    $form['actions']['#prefix'] =
      '<div class="form-actions js-form-wrapper form-wrapper">';
    $form['actions']['#suffix'] = '</div>';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => 'Buscar',
      '#attributes' => [
        'class' => [
          'form-actions',
          'btn', 'btn-success',
        ],
      ],
    ];
    // $form['actions']['cancel'] = [
    //   '#type' => 'link',
    //   '#title' => 'Limpiar',
    //   '#attributes' => ['class' => ['button', 'form-actions']],
    //   '#url' => Url::fromRoute('io_generic_abml.items.list'),
    // ];
    $form['actions']['new_item'] = [
      '#type' => 'link',
      '#title' => 'Nuevo Item',
      '#attributes' => ['class' => ['btn', 'btn-primary']],
      '#url' => Url::fromRoute('io_generic_abml.items.add'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }
}
