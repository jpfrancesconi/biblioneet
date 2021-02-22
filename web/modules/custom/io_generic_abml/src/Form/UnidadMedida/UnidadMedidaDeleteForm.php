<?php
namespace Drupal\io_generic_abml\Form\UnidadMedida;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

use Drupal\io_generic_abml\DAOs\UnidadMedidaDAO;
use Drupal\io_generic_abml\DTOs\UnidadMedidaDTO;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UnidadMedidaDeleteForm extends ConfirmFormBase {
  protected $id;
  protected $unidadMedida;
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'unidad_medida_delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Esta seguro de que desea eliminar la UnidadMedida %id?', ['%id' => $this->id]);
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
    return new Url('io_generic_abml.unidad_medida.list');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('io_generic_abml.unidad_medida.list');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    $this->unidadMedida = UnidadMedidaDAO::load($this->id);
    return $this->t('Esta seguro de que desea eliminar la unidad de medida %name?<br>Esta accion no se puede deshacer', ['%name' => $this->unidadMedida->getUnidadMedida()]);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
    if (!UnidadMedidaDAO::exists($id)) {
      //drupal_set_message(t('Invalid employee record'), 'error');
      \Drupal::messenger()->addError('Registro invalido');
      return new RedirectResponse(Url::fromRoute('io_generic_abml.unidad_medida.list')->toString());
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
    UnidadMedidaDAO::delete($this->id);

    \Drupal::messenger()->addStatus(t('Unidad de medida %name ha sido eliminada.', ['%name' => $this->unidadMedida->getUnidadMedida()]));
    $form_state->setRedirect('io_generic_abml.unidad_medida.list');
  }
}
