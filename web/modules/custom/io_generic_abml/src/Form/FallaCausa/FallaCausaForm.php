<?php
namespace Drupal\io_generic_abml\Form\FallaCausa;

use Drupal;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Messenger\MessengerInterface;


use Drupal\io_generic_abml\DAOs\FallaCausaDAO;
use Drupal\io_generic_abml\DTOs\FallaCausaDTO;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class FallaCausaForm extends FormBase implements FormInterface {
  /**
   * ROUTE_TO_RETURN is the route to return at the end of the differntes form flows.
   */
  const ROUTE_TO_RETURN = 'io_generic_abml.falla_causa.list';

  /**
   * Messaenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Class constructor.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messanger
   *  Messenger service.
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
   * { @inheritDoc }
   */
  public function getFormId() {
    return 'falla_causa_form';
  }

  /**
   * { @inheritDoc }
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {

    if (!empty($id)) {
      if ($id == 'invalid') {
        $this->messenger->addError(t('Invalid record'));
        return new RedirectResponse(Drupal::url(self::ROUTE_TO_RETURN));
      }
      $fallaCausa = FallaCausaDAO::load($id);
      $form['id'] = [
        '#type' => 'hidden',
        '#value' => $id
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
      '#title'  => t('Desde esta ventana podrá dar de alta nuevas causas de falla para que sean luego asociados a los causas no rutinarios.'),
    ];

    $form['causa'] = [
      '#type'           => 'textfield',
      '#title'          => t('Causa de falla'),
      '#required'       => TRUE,
      '#default_value'  => ($fallaCausa) ? $fallaCausa->getCausaFalla() : '',
      '#description'    => t('Nombre de la causa de falla'),
      '#attributes' => [
        'autocomplete' => 'off',
      ],
    ];

    $form['activo'] = [
      '#type'           => 'checkbox',
      '#title'          => t('Activo'),
      '#required'       => FALSE,
      '#default_value'  => ($fallaCausa) ? $fallaCausa->getActivo() : 1,
      '#description'    => t('Estado de la causa de falla'),
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

  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

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
      'causa' => trim(strtoupper($form_state->getValue('causa'))),
      'activo' => $form_state->getValue('activo'),
      'usuario_alta' => $user->id(),
    ];

    if (!empty($id) && FallaCausaDAO::exists($id)) {
      // Set Updated audit fields
      $fields['usuario_mod'] = $user->id();
      $fields['fecha_mod'] = date("Y-m-d h:m:s");
      FallaCausaDAO::update($id, $fields);
      $message = 'La causa de falla [' . $form_state->getValue('causa') . '] ha sido actualizada correctamente.';
    } else {
      $new_record_id = FallaCausaDAO::add($fields);
      $message = 'La causa de falla [' . $form_state->getValue('causa') . '] ah sido creada satisfactoriamente.';
    }
    $this->messenger->addStatus($message);
  }

}
