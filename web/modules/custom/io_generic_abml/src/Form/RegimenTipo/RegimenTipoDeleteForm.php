<?php
namespace Drupal\io_generic_abml\Form\RegimenTipo;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

use Drupal\io_generic_abml\DAOs\RegimenTipoDAO;

use Symfony\Component\HttpFoundation\RedirectResponse;

class RegimenTipoDeleteForm extends ConfirmFormBase {
  protected $id;
  protected $regimenTipo;
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'regimen_tipo_delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Esta seguro de que desea eliminar el tipo de regimen %id?', ['%id' => $this->id]);
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
    return new Url('io_generic_abml.regimen_tipo.list');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('io_generic_abml.regimen_tipo.list');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    $this->regimenTipo = RegimenTipoDAO::load($this->id);

    return $this->t('Esta seguro de que desea eliminar el tipo de regimen %name?<br>Esta accion no se puede deshacer', ['%name' => $this->regimenTipo->getTipoRegimen()]);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
    if (!RegimenTipoDAO::exists($id)) {
      //drupal_set_message(t('Invalid employee record'), 'error');
      \Drupal::messenger()->addError('Registro invalido');
      return new RedirectResponse(Url::fromRoute('io_generic_abml.regimen_tipo.list')->toString());
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
    RegimenTipoDAO::delete($this->id);
    \Drupal::messenger()->addStatus(t('Tipo de regimen [%name] ha sido eliminada.', ['%name' => $this->regimenTipo->getTipoRegimen()]));
    $form_state->setRedirect('io_generic_abml.regimen_tipo.list');
  }
}
