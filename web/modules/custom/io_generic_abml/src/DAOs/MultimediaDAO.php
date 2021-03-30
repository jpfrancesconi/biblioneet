<?php

namespace Drupal\io_generic_abml\DAOs;

use Drupal\Component\Utility\Html;

use Drupal\io_generic_abml\DAOs\GenericDAO;

use Drupal\io_generic_abml\DTOs\MultimediaDTO;
use Drupal\io_generic_abml\DTOs\UserDTO;


/**
 * DAO class for multimedia entity.
 */
class MultimediaDAO extends GenericDAO {
  /**
   * @var const TABLE_NAME
   *  Name of the db table related with the entity
   */
  private const TABLE_NAME = 'bn_multimedia';
  /**
   * @var const TABLE_ALIAS
   *  Table alias used by the differents querys
   */
  private const TABLE_ALIAS = 'mm';

  /**
   * To insert a new record into DB.
   *
   * @param array $fields
   *   An array conating the magazine data in key value pair.
   */
  public static function add(array $fields) {
    return \Drupal::database()->insert(self::TABLE_NAME)->fields($fields)->execute();
  }

  /**
   * To load a bn_multimedia record.
   *
   * @param int $id
   *   The article ID.
   */
  public static function loadByArticleId($articleId) {
    $query = \Drupal::database()->select(self::TABLE_NAME, self::TABLE_ALIAS)
      ->fields(self::TABLE_ALIAS, ['id', 'description', 'createdon', 'updatedon']);
    // Get crator username
    $query = parent::addAuditFields($query, self::TABLE_ALIAS);

    $result = $query->condition('article_id', $articleId, '=')->execute()->fetchObject();

    $multimediaDTO = self::getMultimediaDTOFromRecord($result);

    return $multimediaDTO;
  }

  /** Utis methods *********************************************************************************/
  /**
   * Create a multimediaDTO from stdClass from DB Record
   *
   * @param stdClass $row
   *   stdClass DB record
   * @return MultimediaDTO $multimediaDTO
   *   DTO object
   */
  private static function getMultimediaDTOFromRecord($row) {
    $multimediaDTO = new MultimediaDTO();
    $createdBy = new UserDTO();
    $updatedBy = new UserDTO();

    // set simple fields
    $multimediaDTO->setIdMultimedia($row->id);
    $multimediaDTO->setDescription($row->description);

    // set audit fields
    $createdBy->setUid($row->createdby_uid);
    $createdBy->setUsername($row->createdby);
    $multimediaDTO->setCreatedBy($createdBy);

    $multimediaDTO->setCreatedOn($row->createdon);

    $updatedBy->setUid($row->updatedby_uid);
    $updatedBy->setUsername($row->updatedby);
    $multimediaDTO->setUpdatedBy($updatedBy);

    $multimediaDTO->setUpdatedOn($row->updatedon);

    return $multimediaDTO;
  }
}
