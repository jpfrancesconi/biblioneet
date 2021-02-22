<?php

namespace Drupal\io_generic_abml\Form;

use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Entitys search form.
 */
class EntitySearchForm implements FormInterface {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'entity_search_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $entity = NULL) {
    $form_state->setAlwaysProcess(FALSE);

    $form['#method'] = 'GET';
    $form['#token'] = FALSE;
    // The status messages that will contain any form errors.
    $form['search'] = [
      '#type' => 'search',
      '#default_value' => $_GET['search'] ?? '',
    ];

    $form['actions']['#prefix'] =
      '<div class="form-actions js-form-wrapper form-wrapper">';
    $form['actions']['#suffix'] = '</div>';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => 'Buscar',
      '#attributes' => [
        'class' => ['form-actions',
          'button', 'button--primary',
        ],
      ],
    ];
    $form['actions']['cancel'] = [
      '#type' => 'link',
      '#title' => 'Limpiar',
      '#attributes' => ['class' => ['button', 'form-actions']],
      '#url' => Url::fromRoute('io_generic_abml.entity.list'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {}

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {}

}
