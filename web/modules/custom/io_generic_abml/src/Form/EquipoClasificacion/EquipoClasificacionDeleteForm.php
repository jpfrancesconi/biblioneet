<?php
namespace Drupal\io_generic_abml\Form\EquipoClasificacion;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

use Drupal\io_generic_abml\DAOs\EquipoClasificacionDAO;

use Symfony\Component\HttpFoundation\RedirectResponse;

class EquipoClasificacionDeleteForm extends ConfirmFormBase {
  protected $id;
  protected $equipoClasificacion;
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'equipo_clasificacion_delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Esta seguro de que desea eliminar la Clasificacion del Equipo %id?', ['%id' => $this->id]);
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
    return new Url('io_generic_abml.equipo_clasificacion.list');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('io_generic_abml.equipo_clasificacion.list');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    $this->equipoClasificacion = EquipoClasificacionDAO::load($this->id);

    return $this->t('Esta seguro de que desea eliminar el tipo de clasificación de equipo %name?
                     <br>Esta accion no se puede deshacer', ['%name' => $this->equipoClasificacion->getClasificacion()]);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
    if (!EquipoClasificacionDAO::exists($id)) {
      //drupal_set_message(t('Invalid employee record'), 'error');
      \Drupal::messenger()->addError('Registro invalido');
      return new RedirectResponse(Url::fromRoute('io_generic_abml.equipo_clasificacion.list')->toString());
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
    EquipoClasificacionDAO::delete($this->id);
    //drupal_set_message(t('Employee %id has been deleted.', ['%id' => $this->id]));
    \Drupal::messenger()->addStatus(t('Clasificacion de equipo %name ha sido eliminada.', ['%name' => $this->equipoClasificacion->getClasificacion()]));
    $form_state->setRedirect('io_generic_abml.equipo_clasificacion.list');
  }
}
