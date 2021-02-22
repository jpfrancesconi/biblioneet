<?php
namespace Drupal\io_generic_abml\Form\ContactoTipo;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

use Drupal\io_generic_abml\DAOs\ContactoTipoDAO;

use Symfony\Component\HttpFoundation\RedirectResponse;

class ContactoTipoDeleteForm extends ConfirmFormBase {
  protected $id;
  protected $contactoTipo;
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'contacto_tipo_delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Esta seguro de que desea eliminar la Tipo de contacto %id?', ['%id' => $this->id]);
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
    return new Url('io_generic_abml.contacto_tipo.list');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('io_generic_abml.contacto_tipo.list');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    $this->contactoTipo = ContactoTipoDAO::load($this->id);

    return $this->t('Esta seguro de que desea eliminar el tipo de contacto %name?
                     <br>Esta accion no se puede deshacer', ['%name' => $this->contactoTipo->getTipoContacto()]);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
    if (!ContactoTipoDAO::exists($id)) {
      //drupal_set_message(t('Invalid employee record'), 'error');
      \Drupal::messenger()->addError('Registro invalido');
      return new RedirectResponse(Url::fromRoute('io_generic_abml.contacto_tipo.list')->toString());
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
    ContactoTipoDAO::delete($this->id);
    //drupal_set_message(t('Employee %id has been deleted.', ['%id' => $this->id]));
    \Drupal::messenger()->addStatus(t('Tipo de contacto %name ha sido eliminada.', ['%name' => $this->contactoTipo->getTipoContacto()]));
    $form_state->setRedirect('io_generic_abml.contacto_tipo.list');
  }
}
