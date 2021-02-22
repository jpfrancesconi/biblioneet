<?php

namespace Drupal\io_generic_abml\Form;

use Drupal;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Database\Connection;
use Drupal\Component\Utility\Html;
use Drupal\file\Entity\File;

/**
 * Generic Table Form Class.
 */
abstract class GenericTableForm implements FormInterface {
  /**
   * Databse Connection.
   *
   * @var \Drupal\Core\Database\Connection
   */

  protected $db;

  /**
   * Search String.
   *
   * @var string
   */

  protected $searchKey;



  /**
   * Constructs the EntityTableForm.
   *
   * @param \Drupal\Core\Database\Connection $con
   *   The database connection.
   * @param string $search_key
   *   The search string.
   */
  public function __construct(Connection $con, $search_key = '') {
    $this->db = $con;
    $this->searchKey = $search_key;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
        $container->get('database')
      );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'generic_table_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {}

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

}
