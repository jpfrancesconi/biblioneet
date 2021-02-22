<?php

namespace Drupal\io_generic_abml\Form\Autor;

use Drupal;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\file\Entity\File;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Drupal\io_generic_abml\DAOs\AuthorDAO;
use Drupal\io_generic_abml\DAOs\UtilsDAO;

/**
 * Author Form.
 */
class AuthorForm extends FormBase implements FormInterface {
  /**
   * The Messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * AuthorForm constructor.
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
    return 'author_add';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $author = NULL) {
    if ($author) {
      if ($author == 'invalid') {
        $this->messenger->addError(t('Invalid author record'));
        return new RedirectResponse(Drupal::url('io_generic_abml.author.list'));
      }
      $authorDTO = AuthorDAO::load($author);
      $form['aid'] = [
        '#type' => 'hidden',
        '#value' => $authorDTO->getId(),
      ];
    } else {
      $authorDTO = null;
    }
    $form['#attributes']['novalidate'] = '';

    // Subtitle
    $form['subtitle'] = [
      '#type'   => 'item',
      '#title'  => t('Desde esta ventana podrá dar de alta nuevos autores para que sean luego asociados a libros u otros materiales.'),
    ];

    $form['first_name'] = [
      '#type'           => 'textfield',
      '#title'          => t('Nombre del Autor'),
      '#required'       => TRUE,
      '#default_value'  => ($authorDTO) ? $authorDTO->getFirstName() : '',
      '#description'    => t('Nombre del autor'),
    ];

    $form['last_name'] = [
      '#type'           => 'textfield',
      '#title'          => t('Apellido del Autor'),
      '#required'       => TRUE,
      '#default_value'  => ($authorDTO) ? $authorDTO->getLastName() : '',
      '#description'    => t('Apellido del autor'),
    ];

    $form['nationality'] = [
      '#type'           => 'select',
      '#title'          => t('Nacionalidad del Autor'),
      '#options'        => $this->getCountries(),
      '#required'       => FALSE,
      '#description'    => t('Nacionalidad del autor'),
      '#default_value'  => ($authorDTO) ? $authorDTO->getNationality()->getId() : null,
    ];
    //$form['nationality']['#options'][0] = t('- SELECCIONE UN PAIS -');

    $form['description'] = [
      '#type'           => 'textarea',
      '#title'          => t('Reseña del autor'),
      '#default_value'  => ($authorDTO) ? $authorDTO->getDescription() : '',
      '#required'       => FALSE,
    ];

    $form['upload']['picture'] = [
      '#type'               => 'managed_file',
      '#upload_location'    => 'public://authors_images/',
      '#multiple'           => FALSE,
      '#upload_validators'  => [
        'file_validate_extensions'  => ['png gif jpg jpeg jfif'],
        'file_validate_size'        => [25600000],
        //'file_validate_image_resolution' => array('800x600', '400x300'),.
      ],
      '#title'              => t('Foto o Imagen del autor'),
      '#default_value'      => ($authorDTO) ? [$authorDTO->getPicture()] : '',
    ];

    $form['status'] = [
      '#type'           => 'checkbox',
      '#title'          => t('Activo?'),
      '#description'    => t('Permite determinar si aparecera en las busquedas dentro del sistema'),
      '#default_value'  => ($authorDTO) ? $authorDTO->getStatus() : 1,
    ];

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type'   => 'submit',
      '#value'  => 'Guardar',
    ];

    $form['actions']['cancel'] = [
      '#type'       => 'link',
      '#title'      => 'Cancelar',
      '#attributes' => ['class' => ['button', 'button--primary']],
      '#url'        => Url::fromRoute('io_generic_abml.author.list'),
    ];

    return $form;
  }


  /**
   * {@inheritdoc}
   */
  public function getCountries() {
    // Get all countries from DAO
    $results = UtilsDAO::getAllCountries();
    // Prepar results array
    $countriesList = ['0' => '- SELECCIONE UNA OPCIÓN -'];
    foreach ($results as $key => $value) {
      $countriesList[$value->id] = $value->en_short_name;
    }
    // return results
    return $countriesList;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    $id = $form_state->getValue('aid');
    //$this->messenger->addError('Hello world');

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get current user operator
    $user = \Drupal::currentUser();

    $id = $form_state->getValue('aid');
    $file_usage = Drupal::service('file.usage');
    $picture_fid = NULL;
    $image = $form_state->getValue('picture');
    if (!empty($image)) {
      $picture_fid = $image[0];
    }
    $fields = [
      'first_name' => $form_state->getValue('first_name'),
      'last_name' => $form_state->getValue('last_name'),
      'description' => $form_state->getValue('description'),
      'nationality' => ($form_state->getValue('nationality') != 0) ? $form_state->getValue('nationality') : null,
      'status' => $form_state->getValue('status'),
      'picture' => $picture_fid,
    ];
    if (!empty($id) && AuthorDAO::exists($id)) {
      $author = AuthorDAO::load($id);
      if ($picture_fid) {
        if ($picture_fid !== $author->picture) {
          file_delete($author->picture);
          $file = File::load($picture_fid);
          $file->setPermanent();
          $file->save();
          $file_usage->add($file, 'author', 'file', $id);
        }
      } else {
        file_delete($author->picture);
      }
      // Set Updated auditory fields
      $fields['updatedby'] = $user->id();
      $fields['updatedon'] = date("Y-m-d h:m:s");

      AuthorDAO::update($id, $fields);
      $message = 'El autor ha sido actualizado correctamente.';
    } else {
      // Set Creation auditory fields
      $fields['createdby'] = $user->id();
      $fields['createdon'] = date("Y-m-d h:m:s");

      $new_author_id = AuthorDAO::add($fields);
      if ($picture_fid) {
        $file = File::load($picture_fid);
        $file->setPermanent();
        $file->save();
        $file_usage->add($file, 'author', 'file', $new_author_id);
      }
      // $this->dispatchEmployeeWelcomeMailEvent($new_employee_id);
      $message = 'El autor ah sido creado satisfactoriamente.';
    }
    $this->messenger->addStatus($message);
    $form_state->setRedirect('io_generic_abml.author.list');
  }
}
