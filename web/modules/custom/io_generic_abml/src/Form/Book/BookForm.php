<?php
namespace Drupal\io_generic_abml\Form\Book;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

use Drupal\io_generic_abml\DAOs\ArticleDAO;

class BookForm extends FormBase {
  public function getFormId() {
    return 'book_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#prefix'] = '<div id="type-form-id">';
    $form['#suffix'] = '</div>';

    $form['isbn'] = [
      '#type' => 'textfield',
      '#title' => $this->t('ISBN'),
    ];
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {

  }
}
