<?php

namespace Drupal\io_generic_abml\Form\EquipoTipos;

use Drupal;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Drupal\io_generic_abml\DAOs\EquipoTiposDAO;

/**
 * Equipo Tipo Delete Form.
 */
class EquipoTipoDeleteForm extends ConfirmFormBase {

  protected $id;
  protected $equipoTipo;
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'equipo_tipo_delete';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Esta seguro de que desea eliminar el Tipo de Equipo %id?', ['%id' => $this->id]);
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
    return new Url('io_generic_abml.equipo_tipo.list');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('io_generic_abml.equipo_tipo.list');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    $this->equipoTipo = EquipoTiposDAO::load($this->id);

    return $this->t('Esta seguro de que desea eliminar el Tipo de Equipo %tipo?<br>Esta accion no se puede deshacer', ['%tipo' => $this->equipoTipo->getTipo()]);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
    if (!EquipoTiposDAO::exists($id)) {
      \Drupal::messenger()->addError('Registro invalido');
      return new RedirectResponse(Url::fromRoute('io_generic_abml.equipo_tipo.list')->toString());
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
    EquipoTiposDAO::delete($this->id);
    //drupal_set_message(t('Employee %id has been deleted.', ['%id' => $this->id]));
    \Drupal::messenger()->addStatus(t('Tipo de Equipo %tipo ha sido eliminado.', ['%tipo' => $this->equipoTipo->getTipo()]));
    $form_state->setRedirect('io_generic_abml.equipo_tipo.list');
  }
}
