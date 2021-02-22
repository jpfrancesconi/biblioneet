<?php
namespace Drupal\io_generic_abml\Form\DanioTipo;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

use Drupal\io_generic_abml\DAOs\DanioTipoDAO;

use Symfony\Component\HttpFoundation\RedirectResponse;

class DanioTipoDeleteForm extends ConfirmFormBase {
  protected $id;
  protected $danioTipo;
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'danio_tipo_delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Esta seguro de que desea eliminar la Tipo de daño %id?', ['%id' => $this->id]);
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
    return new Url('io_generic_abml.danio_tipo.list');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('io_generic_abml.danio_tipo.list');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    $this->danioTipo = DanioTipoDAO::load($this->id);

    return $this->t('Esta seguro de que desea eliminar el tipo de clasificación de actividad %name?
                     <br>Esta accion no se puede deshacer', ['%name' => $this->danioTipo->getDanio()]);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
    if (!DanioTipoDAO::exists($id)) {
      //drupal_set_message(t('Invalid employee record'), 'error');
      \Drupal::messenger()->addError('Registro invalido');
      return new RedirectResponse(Url::fromRoute('io_generic_abml.danio_tipo.list')->toString());
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
    DanioTipoDAO::delete($this->id);
    //drupal_set_message(t('Employee %id has been deleted.', ['%id' => $this->id]));
    \Drupal::messenger()->addStatus(t('Tipo de daño %name ha sido eliminada.', ['%name' => $this->danioTipo->getDanio()]));
    $form_state->setRedirect('io_generic_abml.danio_tipo.list');
  }
}
