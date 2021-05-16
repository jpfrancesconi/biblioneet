<?php

namespace Drupal\io_generic_abml\Form\Instances;

use Drupal;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Ajax\ReplaceCommand;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Drupal\io_generic_abml\DAOs\InstanceDAO;

use Drupal\io_generic_abml\DTOs\InstanceDTO;

/**
 * Entity Form.
 */
class InstanceForm extends FormBase implements FormInterface {
  /**
   * ROUTE_TO_RETURN is the route to return at the end of the differntes form flows.
   */
  const ROUTE_TO_RETURN = 'io_generic_abml.items.instances.list';
  /**
   * The Messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * EntityForm constructor.
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
    return 'instance_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
    // Set default values to variables
    $isEdit = false;
    $instanceDTO = null;
    // Check if we come from edit or new
    if(isset($_GET['idInstance']))
      $idInstance = $_GET['idInstance'];
    if (isset($idInstance) && $idInstance !== 0) {
      $instanceDTO = InstanceDAO::load($idInstance);
      $isEdit = true;
      $form['id_instance'] = [
        '#type' => 'hidden',
        '#value' => $idInstance,
      ];
    }
    $form['id_item'] = [
      '#type' => 'hidden',
      '#value' => $id,
    ];

    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Desde este formulario se podran crear nuevas existencias o editar las ya registrados'),
    ];

    // 1- Area de titulo y mencion de responsabilidad
    $form['container'] = [
      '#type' => 'details',
      '#title' => $this->t('Datos de la existencia a agregar.'),
      '#open' => TRUE,
    ];
    // Field: bn_instance.inv_code
    $form['container']['inv_code'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Inventario'),
      '#default_value' => ($instanceDTO) ? $instanceDTO->getInvCode() : '',
      '#required' => FALSE,
      '#attributes' => [
        'placeholder' => 'Nro. de inventario',
      ],
    ];
    // Field: bn_instance_status_id.
    $instancesStatusOptions = InstanceDAO::getInstanceStatusSelectFormat(true, 'Seleccione un estado'); //[0=> 'LIBRO', 1=>'REVISTA'];
    $form['container']['instance_status_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Estado'),
      '#default_value' => ($instanceDTO) ? $instanceDTO->getInstanceStatus()->getId() : '',
      '#options' => $instancesStatusOptions,
      '#required' => TRUE,
      '#attributes' => [
        'placeholder' => 'Estado',
      ],
    ];

    // Field: bn_instance.signature
    $form['container']['signature'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Signatura'),
      '#default_value' => ($instanceDTO) ? $instanceDTO->getSignature() : '',
      '#required' => FALSE,
      '#attributes' => [
        'placeholder' => 'Signatura',
      ],
    ];

    $form['container']['actions'] = [
      '#type' => 'actions',
    ];
    $form['container']['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('GUARDAR EXISTENCIA'),
      '#attributes' => [
        'class' => ['btn btn-success btn-sm'],
      ],
    ];
    // If all required fields are not completed we can't submit the form yet.
    //$form['actions']['submit']['#disabled'] = TRUE;

    if ($isEdit) {
      $form['container']['actions']['submit']['#value'] = $this->t('GUARDAR CAMBIOS');

      $form['actions']['cancel'] = [
        '#type' => 'link',
        '#title' => 'VOLVER',
        '#attributes' => ['class' => ['btn', 'btn-danger', 'btn-sm']],
        '#url' => Url::fromRoute(self::ROUTE_TO_RETURN, ['id' => $id]),
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $trigger = (string) $form_state->getTriggeringElement()['#value'];
    $formValues = $form_state->getValues();

    $status_selected_option = $form_state->getUserInput()['instance_status_id'];
    if ($status_selected_option === "0") {
      $form_state->setErrorByName('instance_status_id', $this->t('Debe seleccionar un estado.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $formValues = $form_state->getValues();

    // We have to create a new instance
    $user = \Drupal::currentUser();
    $itemId = intval($form_state->getUserInput()['id_item']['id']);
    $fields = [
      'inv_code' => trim(strtoupper($form_state->getUserInput()['inv_code'])),
      'signature' => trim(strtoupper($form_state->getUserInput()['signature'])),
      'instance_status_id' => $form_state->getUserInput()['instance_status_id'],
      'item_id' => $itemId,
      'createdby' => $user->id(),
      'createdon' => date("Y-m-d h:m:s"),
    ];
    InstanceDAO::add($fields);
    $this->messenger()->addStatus($this->t('La existencia fue creada satisfactoriamente.'));
    $form_state->setRedirect(self::ROUTE_TO_RETURN, ['id' => $itemId]);
  }

}
