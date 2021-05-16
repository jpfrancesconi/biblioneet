<?php

namespace Drupal\io_generic_abml\Form\Instances;

use Drupal;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Drupal\io_generic_abml\DAOs\InstanceDAO;
use Drupal\io_generic_abml\DAOs\ItemDAO;

/**
 * Instance delete form.
 */
class InstanceDeleteForm extends ConfirmFormBase {

  protected $id;

  protected $itemId;

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
    return 'instance_delete';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Está seguro que desea eliminar el Registro?', ['%id' => $this->id]);
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
    return new Url('io_generic_abml.items.instances.list', ['id' => $this->itemId]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('io_generic_abml.items.instances.list', ['id' => $this->itemId]);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
    if (!InstanceDAO::exists($id)) {
      $this->messenger->addError(t('Invalid record'));
      $url = Url::fromRoute('io_generic_abml.items.instances.list');
      return new RedirectResponse($url->toString());
    }
    $this->id = $id;
    $instanceDTO = InstanceDAO::load($id);
    $this->itemId = $instanceDTO->getItem()->getId();
    $itemDTO = ItemDAO::load($this->itemId);
    $f = parent::buildForm($form, $form_state);

    $f['subtitle'] = [
      '#type' => 'markup',
      '#markup' => '<div><h3>Esta por eliminar una existencia del Item: '. $itemDTO->getTitle() .'</h3></div>',
    ];

    return $f;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get author by ID
    $instanceDTO = InstanceDAO::load($this->id);
    InstanceDAO::delete($this->id);
    
    $instanceData = $instanceDTO->getInvCode(). ' - '. $instanceDTO->getSignature();
    $this->messenger->addStatus(t('La existencia %instanceData ha sido eliminada.', ['%instanceData' => $instanceData]));
    $form_state->setRedirect('io_generic_abml.items.instances.list', ['id' => $this->itemId]);
  }

}
