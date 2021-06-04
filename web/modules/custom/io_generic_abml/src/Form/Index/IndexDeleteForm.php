<?php

namespace Drupal\io_generic_abml\Form\Index;

use Drupal;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Drupal\io_generic_abml\DAOs\IndexDAO;

/**
 * Index Delete Form.
 */
class IndexDeleteForm extends ConfirmFormBase {
    
  protected $idIndex;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'index_delete';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Esta seguro de que desea eliminar el indice %id?', ['%id' => $this->idIndex]);
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
    return new Url('io_generic_abml.items.indexes.list', ['id' => $idItem]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('io_generic_abml.items.indexes.list', ['id' => $idItem]);
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->t('Esta accion no se puede deshacer');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $idIndex = NULL) {
    if (!IndexDAO::exists($idIndex)) {
      //drupal_set_message(t('Invalid employee record'), 'error');
      \Drupal::messenger()->addError('Registro invalido');
      //return new RedirectResponse(Url::fromRoute('localizaio_equipos_locs.localizaciones.listcion')->toString());
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
    $itemDTO = IndexDAO::load($this->idIndex);
    $idItem = $itemDTO->getId();

    IndexDAO::delete($this->idIndex);
    //drupal_set_message(t('Employee %id has been deleted.', ['%id' => $this->id]));
    \Drupal::messenger()->addStatus(t('El indice %id ha sido eliminado.', ['%id' => $this->id]));
    $form_state->setRedirect('io_generic_abml.items.indexes.list', ['id' => $idItem]);
  }
}