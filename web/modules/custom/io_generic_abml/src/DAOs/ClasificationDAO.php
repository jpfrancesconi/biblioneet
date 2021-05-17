<?php

namespace Drupal\io_generic_abml\DAOs;

use Drupal;
use Drupal\Component\Utility\Html;
use Drupal\file\Entity\File;

use Drupal\io_generic_abml\DAOs\GenericDAO;

use Drupal\io_generic_abml\DTOs\ClasificationDTO;
use Drupal\io_generic_abml\DTOs\UserDTO;


/**
 * DAO class for item entity.
 */
class ClasificationDAO extends GenericDAO {
  /**
   * @var const TABLE_NAME
   *  Name of the db table related with the entity
   */
  private const TABLE_NAME = 'bn_clasification';
  /**
   * @var const TABLE_ALIAS
   *  Table alias used by the differents querys
   */
  private const TABLE_ALIAS = 'c';

  /**
   * To load an Item record.
   *
   * @param int $id
   *   The item ID.
   * @return ItemDTO $objectDTO
   *   The searched object DTO.
   */
  public static function load($id) {
    $query = \Drupal::database()->select(self::TABLE_NAME, self::TABLE_ALIAS)
      ->fields(self::TABLE_ALIAS, [
        'id',
        'code',
        'materia',
        'status',
        'createdon',
        'updatedon'
      ]);

    $query = parent::addAuditFields($query, self::TABLE_ALIAS);

    $result = $query->condition(self::TABLE_ALIAS . '.id', $id, '=')->execute()->fetchObject();
    $objectDTO = self::getClasificationDTOFromRecord($result);

    return $objectDTO;
  }

  /**
   * Get the list of item clasification in the select format
   */
  public static function getClasificationsSelectFormat($status = 1, $opcion_vacia) {
    $query = \Drupal::database()->select('bn_clasification', 'c')
      ->fields('c', ['id', 'code', 'materia', 'status']);
    if(isset($status))
      $query->condition('c.status', $status, '=');
    // Query execution.
    $result = $query->execute()->fetchAll();

    $select_options = [];
    foreach ($result as $key => $row) {
      $select_options[$row->id] = $row->code .' - '. $row->materia;
    }
    return $select_options;
  }

  /** Utis methods *********************************************************************************/
  /**
   * Create a ClasificationDTO from stdClass from DB Record
   *
   * @param stdClass $row
   *   stdClass DB record
   * @return ClasificationDTO $clasificationDTO
   *   DTO object
   */
  private static function getClasificationDTOFromRecord($row) {
    $clasificationDTO = new ClasificationDTO();
    $createdBy = new UserDTO();
    $updatedBy = new UserDTO();

    // set simple fields
    $clasificationDTO->setId($row->id);
    $clasificationDTO->setCode($row->code);
    $clasificationDTO->setMateria($row->materia);
    $clasificationDTO->setStatus($row->status);

    // set audit fields
    $createdBy->setUid($row->createdby_uid);
    $createdBy->setUsername($row->createdby);
    $clasificationDTO->setCreatedBy($createdBy);

    $clasificationDTO->setCreatedOn($row->createdon);

    $updatedBy->setUid($row->updatedby_uid);
    $updatedBy->setUsername($row->updatedby);
    $clasificationDTO->setUpdatedBy($updatedBy);

    $clasificationDTO->setUpdatedOn($row->updatedon);

    return $clasificationDTO;
  }
}