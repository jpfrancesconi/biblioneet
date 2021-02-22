<?php

namespace Drupal\io_generic_abml\Form\Autor;

use Drupal;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Drupal\io_generic_abml\DAOs\AuthorDAO;

/**
 * Author delete form.
 */
class AuthorDeleteForm extends ConfirmFormBase {

  protected $id;

  /**
   * The Messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * AuthorForm constructor.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   */
  public function __construct(MessengerInterface $messenger) {
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('messenger')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'author_delete';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Está seguro que desea eliminar al Autor: %id?', ['%id' => $this->id]);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return t('Eliminar definitivamente');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelRoute() {
    return new Url('io_generic_abml.author.list');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('io_generic_abml.author.list');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
    if (!AuthorDAO::exists($id)) {
      $this->messenger->addError(t('Invalid author record'));
      $url = Url::fromRoute('io_generic_abml.author.list');
      return new RedirectResponse($url->toString());
    }
    $this->id = $id;
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get author by ID
    $authorDTO = AuthorDAO::load($this->id);
    AuthorDAO::delete($this->id);
    //drupal_set_message(t('Employee %id has been deleted.', ['%id' => $this->id]));
    $autor_nomape = $authorDTO->getFirstName(). ', '. $authorDTO->getLastName();
    $this->messenger->addStatus(t('Autor %autor ha sido eliminado.', ['%autor' => $autor_nomape]));
    $form_state->setRedirect('io_generic_abml.author.list');
  }

}
