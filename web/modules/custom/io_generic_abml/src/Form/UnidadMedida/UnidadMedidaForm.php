<?php
namespace Drupal\io_generic_abml\Form\UnidadMedida;

use Drupal;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Messenger\MessengerInterface;

use Drupal\io_generic_abml\DAOs\UnidadMedidaDAO;
use Drupal\io_generic_abml\DTOs\UnidadMedidaDTO;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UnidadMedidaForm extends FormBase implements FormInterface {
/**
   * ROUTE_TO_RETURN is the route to return at the end of the differntes form flows.
   */
  const ROUTE_TO_RETURN = 'io_generic_abml.unidad_medida.list';
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
    return 'unidad_medida_add';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $unidad_medida_id = NULL) {
    $unidadMedida = new UnidadMedidaDTO();
    if ($unidad_medida_id) {
      if ($unidad_medida_id == 'invalid') {
        $this->messenger->addError(t('Invalid record'));
        return new RedirectResponse(Drupal::url());
      }
      $unidadMedida = UnidadMedidaDAO::load($unidad_medida_id);
      $form['id'] = [
        '#type' => 'hidden',
        '#value' => $unidadMedida->getId(),
      ];
    }
    $form['#attributes']['novalidate'] = '';

     // Preffix and suffix to wrapp the form
    $form['#prefix'] = '<div id="modal_form">';
    $form['#suffix'] = '</div>';
    // The status messages that will contain any form errors.
    $form['status_messages'] = [
      '#type' => 'status_messages',
      '#weight' => -10,
    ];
    // Subtitle
    $form['subtitle'] = [
      '#type'   => 'item',
      '#title'  => t('Desde esta ventana podrá dar de alta nuevas unidades de medidas.'),
    ];

    $form['unidad_medida'] = [
      '#type'           => 'textfield',
      '#title'          => t('Unidad de medida'),
      '#required'       => TRUE,
      '#default_value'  => ($unidadMedida) ? $unidadMedida->getUnidadMedida() : '',
      '#description'    => t('Nombre de la unidad de medida'),
      '#attributes' => [
        'autocomplete' => 'off',
      ],
    ];

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['guardar'] = [
      '#type'   => 'submit',
      '#value'  => 'Guardar',
      '#attributes' => [
        'class' => [
          'use-ajax',
        ],
      ],
      '#ajax' => [
        'callback' => [$this, 'submitModalFormAjax'],
        'event' => 'click',
      ],
    ];

    $form['actions']['cancel'] = [
      '#type'       => 'link',
      '#title'      => 'Cancelar',
      '#attributes' => ['class' => ['button']],
      '#url'        => Url::fromRoute(self::ROUTE_TO_RETURN),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // $unidad_medida = $form_state->getValue('unidad_medida');
    // if (empty($unidad_medida)) {
    //   $form_state->setErrorByName('unidad_medida', t('Debe ingresar un nombre para el unidad_medida de equipo'));
    // }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * Ajax from submit.
   *
   * @param array $form
   * @param FormStateInterface $form_state
   * @return void
   */
  public function submitModalFormAjax(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    if ($form_state->hasAnyErrors()) {
      $response->addCommand(new ReplaceCommand('#modal_form', $form));
    }
    else {
      $this->commonSubmit($form, $form_state);
      $form_state->setRedirect(self::ROUTE_TO_RETURN);
      $response->addCommand(new RedirectCommand(Url::fromRoute(self::ROUTE_TO_RETURN)->toString()));
    }
    return $response;
  }
  /**
   * Submit used by both submits (Ajax and not ajax).
   *
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function commonSubmit(array &$form, FormStateInterface $form_state) {
    // Get current user operator
    $user = \Drupal::currentUser();
    $id = $form_state->getValue('id');

    $fields = [
      'unidad_medida' => $form_state->getValue('unidad_medida'),
      'usuario_alta' => $user->id(),
    ];

    if (!empty($id) && UnidadMedidaDAO::exists($id)) {
      // Set Updated audit fields
      $fields['usuario_mod'] = $user->id();
      $fields['fecha_mod'] = date("Y-m-d h:m:s");
      UnidadMedidaDAO::update($id, $fields);
      $message = 'La Unidad de medida [' . $form_state->getValue('unidad_medida') . '] ha sido actualizada correctamente.';
    } else {
      $new_record_id = UnidadMedidaDAO::add($fields);
      $message = 'La Unidad de medida [' . $form_state->getValue('unidad_medida') . '] ah sido creada satisfactoriamente.';
    }
    $this->messenger->addStatus($message);
  }
}
