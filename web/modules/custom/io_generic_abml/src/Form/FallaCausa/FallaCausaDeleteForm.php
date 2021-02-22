<?php
namespace Drupal\io_generic_abml\Form\FallaCausa;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

use Drupal\io_generic_abml\DAOs\FallaCausaDAO;

use Symfony\Component\HttpFoundation\RedirectResponse;

class FallaCausaDeleteForm extends ConfirmFormBase {
  protected $id;
  protected $fallaCausa;
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'falla_causa_delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Esta seguro de que desea eliminar la Tipo de trabajo %id?', ['%id' => $this->id]);
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
    return new Url('io_generic_abml.falla_causa.list');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('io_generic_abml.falla_causa.list');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    $this->fallaCausa = FallaCausaDAO::load($this->id);

    return $this->t('Esta seguro de que desea eliminar la causa de falla %name?
                     <br>Esta accion no se puede deshacer', ['%name' => $this->fallaCausa->getCausaFalla()]);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
    if (!FallaCausaDAO::exists($id)) {
      //drupal_set_message(t('Invalid employee record'), 'error');
      \Drupal::messenger()->addError('Registro invalido');
      return new RedirectResponse(Url::fromRoute('io_generic_abml.falla_causa.list')->toString());
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
    FallaCausaDAO::delete($this->id);
    //drupal_set_message(t('Employee %id has been deleted.', ['%id' => $this->id]));
    \Drupal::messenger()->addStatus(t('Tipo de trabajo %name ha sido eliminada.', ['%name' => $this->fallaCausa->getCausaFalla()]));
    $form_state->setRedirect('io_generic_abml.falla_causa.list');
  }
}
