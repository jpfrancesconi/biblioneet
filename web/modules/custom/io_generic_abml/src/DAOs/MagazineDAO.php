<?php

namespace Drupal\io_generic_abml\DAOs;

use Drupal\Component\Utility\Html;

use Drupal\io_generic_abml\DAOs\GenericDAO;

use Drupal\io_generic_abml\DTOs\MagazineDTO;
use Drupal\io_generic_abml\DTOs\UserDTO;


/**
 * DAO class for magazine entity.
 */
class MagazineDAO extends GenericDAO {
  /**
   * @var const TABLE_NAME
   *  Name of the db table related with the entity
   */
  private const TABLE_NAME = 'bn_magazine';
  /**
   * @var const TABLE_ALIAS
   *  Table alias used by the differents querys
   */
  private const TABLE_ALIAS = 'mg';

  /**
   * To load a bn_book record.
   *
   * @param int $id
   *   The article ID.
   */
  public static function loadByArticleId($articleId) {
    $query = \Drupal::database()->select(self::TABLE_NAME, self::TABLE_ALIAS)
      ->fields(self::TABLE_ALIAS, ['id', 'numero', 'createdon', 'updatedon']);
    // Get crator username
    $query = parent::addAuditFields($query, self::TABLE_ALIAS);

    $result = $query->condition('article_id', $articleId, '=')->execute()->fetchObject();

    $bookDTO = self::getMagazineDTOFromRecord($result);

    return $bookDTO;
  }

  /** Utis methods *********************************************************************************/
  /**
   * Create a magazineDTO from stdClass from DB Record
   *
   * @param stdClass $row
   *   stdClass DB record
   * @return MagazineDTO $magazineDTO
   *   DTO object
   */
  private static function getMagazineDTOFromRecord($row) {
    $magazineDTO = new MagazineDTO();
    $createdBy = new UserDTO();
    $updatedBy = new UserDTO();

    // set simple fields
    $magazineDTO->setIdMagazine($row->id);
    $magazineDTO->setNumero($row->numero);

    // set audit fields
    $createdBy->setUid($row->createdby_uid);
    $createdBy->setUsername($row->createdby);
    $magazineDTO->setCreatedBy($createdBy);

    $magazineDTO->setCreatedOn($row->createdon);

    $updatedBy->setUid($row->updatedby_uid);
    $updatedBy->setUsername($row->updatedby);
    $magazineDTO->setUpdatedBy($updatedBy);

    $magazineDTO->setUpdatedOn($row->updatedon);

    return $magazineDTO;
  }

}
