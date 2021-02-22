<?php
namespace Drupal\io_generic_abml\Form\Prioridad;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\io_generic_abml\DAOs\PrioridadDAO;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PrioridadDeleteForm extends ConfirmFormBase {
  protected $id;
  protected $prioridad;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'prioridad_delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Esta seguro de que desea eliminar la Prioridad %id?', ['%id' => $this->id]);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return t('Eliminar');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelRoute() {
    return new Url('io_generic_abml.prioridad.list');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('io_generic_abml.prioridad.list');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    $this->prioridad = PrioridadDAO::load($this->id);

    return $this->t('Esta seguro de que desea eliminar la prioridad %name?<br>Esta accion no se puede deshacer', ['%name' => $this->prioridad->getPrioridad()]);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
    if (!PrioridadDAO::exists($id)) {
      //drupal_set_message(t('Invalid employee record'), 'error');
      \Drupal::messenger()->addError('Registro invalido');
      return new RedirectResponse(Url::fromRoute('io_generic_abml.prioridad.list')->toString());
    }
    $this->id = $id;
    $f = parent::buildForm($form, $form_state);
    // Need to check if we can delete this record
    if(true) {

    } else {
        // hide confirm button
        $f['actions']['submit'] = [];
    }

    return $f;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    PrioridadDAO::delete($this->id);

    \Drupal::messenger()->addStatus(t('Prioridad %prioridad ha sido eliminada.', ['%prioridad' => $this->prioridad->getPrioridad()]));
    $form_state->setRedirect('io_generic_abml.prioridad.list');
  }
}
