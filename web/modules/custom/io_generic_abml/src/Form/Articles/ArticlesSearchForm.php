<?php

namespace Drupal\io_generic_abml\Form\Articles;

use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Articles search form.
 */
class ArticlesSearchForm implements FormInterface {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'articles_search_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $article = NULL) {
    $form_state->setAlwaysProcess(FALSE);

    $form['#method'] = 'GET';
    $form['#token'] = FALSE;
    // The status messages that will contain any form errors.
    $form['search_article_type'] = [
      '#type' => 'select',
      '#title' => 'FILTROS',
      '#options' => [
        0 => 'TODOS',
        1 => 'TÍTULO',
        2 => 'AUTOR',
        3 => 'MATERIA',
        4 => 'ISBN',
      ],
      '#default_value' => $_GET['search_article_type'] ?? '0',
    ];
    $form['search_article'] = [
      '#type' => 'search',
      '#attributes' => [
        'placeholder' => 'Ingrese alguna palabra',
      ],
      '#default_value' => $_GET['search_article'] ?? '',
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
          'button', 'button--primary',
        ],
      ],
    ];
    $form['actions']['cancel'] = [
      '#type' => 'link',
      '#title' => 'Limpiar',
      '#attributes' => ['class' => ['button', 'form-actions']],
      '#url' => Url::fromRoute('io_generic_abml.articles.list'),
    ];
    $form['actions']['new_article'] = [
      '#type' => 'link',
      '#title' => 'Nuevo Artículo',
      '#attributes' => ['class' => ['btn', 'btn-primary']],
      '#url' => Url::fromRoute('io_generic_abml.articles.add'),
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
