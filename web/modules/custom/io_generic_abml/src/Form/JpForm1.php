<?php
namespace Drupal\io_generic_abml\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Html;

class JpForm1 extends FormBase {
  public function getFormId(){
    return 'jpform1_form';
  }

  /**
   * Build the simple form.
   *
   * @param array $form
   *   Default form array structure.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object containing current form state.
   *
   * @return array
   *   The render array defining the elements of the form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Generate a unique wrapper HTML ID.
    $ajax_wrapper_id = 'pepe';//Html::getUniqueId('text-container');
    if($form_state->has('text'))
      $text = $form_state->get('text');
    else
      $text = 'No text yet.';

    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t($text),
      '#prefix' => '<div id="' . $ajax_wrapper_id . '">',
      '#suffix' => '</div>',
    ];

    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#description' => $this->t('Title must be at least 5 characters in length.'),
      '#required' => TRUE,
      '#ajax' => [
        // The Ajax callback method that is responsible for responding to the
        // Ajax HTTP request.
        'callback' => '::promptCallback',
        // The ID of the DOM element whose content will be replaced with
        // whatever is returned from the above callback.
        'wrapper' => $ajax_wrapper_id,
        'method' => 'replace',
        'event' => 'keyup',
      ],
      '#attributes' => [
        'autocomplete'=> 'off',
      ],
    ];

    // Group submit handlers in an actions element with a key of "actions" so
    // that it gets styled correctly, and so that other modules may add actions
    // to the form. This is not required, but is convention.
    $form['actions'] = [
      '#type' => 'actions',
    ];

    // Add a submit button that handles the submission of the form.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];
    return $form;
  }

  /**
   * Implements form validation.
   *
   * @param array $form
   *   The render array of the currently built form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object describing the current state of the form.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $form_state->set('text', $form_state->getValue('title'));

  }

  /**
   * Implements a form submit handler.
   *
   * @param array $form
   *   The render array of the currently built form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object describing the current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRebuild(TRUE);
    $title = $form_state->getValue('title');
    $this->messenger()->addStatus($this->t('You specified a title of %title.', ['%title' => $title]));
  }

  /**
   * Ajax callback for "Submit" button.
   *
   * This callback is called regardless of what happens in validation and
   * submission processing. It needs to return the content that will be used to
   * replace the DOM element identified by the '#ajax' properties 'wrapper' key.
   *
   * @return array
   *   Renderable array (the box element)
   */
  public function promptCallback(array &$form, FormStateInterface $form_state) {
    return $form['description'];
  }
}
