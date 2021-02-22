<?php
namespace Drupal\io_generic_abml\Form\FallaTipo;

use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

class FallaTipoSearchForm implements FormInterface {

  /**
   * { @inheritDoc }
   */
  public function getFormId() {
    return 'falla_tipo_search_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form_state->setAlwaysProcess(FALSE);

    $form['#method'] = 'GET';
    $form['#token'] = FALSE;

    $form['search'] = [
      '#type' => 'search',
      '#default_value' => $_GET['search'] ?? '',
      '#attributes' => [
        'placeholder' => t('BUSCAR POR FALLA'),
      ],
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
      '#url' => Url::fromRoute('io_generic_abml.falla_tipo.list'),
    ];
    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  public function submitForm(array &$form, FormStateInterface $form_state) {

  }
}
