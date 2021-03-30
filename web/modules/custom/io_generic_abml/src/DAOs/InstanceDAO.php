<?php

namespace Drupal\io_generic_abml\DAOs;

use Drupal\Component\Utility\Html;

use Drupal\io_generic_abml\DAOs\GenericDAO;

use Drupal\io_generic_abml\DTOs\BookDTO;
use Drupal\io_generic_abml\DTOs\ArticleDTO;
use Drupal\io_generic_abml\DTOs\ArticleTypeDTO;
use Drupal\io_generic_abml\DTOs\ArticleFormatDTO;
use Drupal\io_generic_abml\DTOs\EditorialDTO;
use Drupal\io_generic_abml\DTOs\UserDTO;


/**
 * DAO class for instance entity.
 */
class InstanceDAO extends GenericDAO {
  /**
   * @var const TABLE_NAME
   *  Name of the db table related with the entity
   */
  private const TABLE_NAME = 'bn_instance';
  /**
   * @var const TABLE_ALIAS
   *  Table alias used by the differents querys
   */
  private const TABLE_ALIAS = 'ins';

  /**
   * To insert a new record into DB.
   *
   * @param array $fields
   *   An array conating the author data in key value pair.
   */
  public static function add(array $fields) {
    return \Drupal::database()->insert(self::TABLE_NAME)->fields($fields)->execute();
  }

}
