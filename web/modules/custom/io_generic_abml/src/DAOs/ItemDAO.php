<?php

namespace Drupal\io_generic_abml\DAOs;

use Drupal;
use Drupal\Component\Utility\Html;
use Drupal\file\Entity\File;

use Drupal\io_generic_abml\DAOs\GenericDAO;
use Drupal\io_generic_abml\DAOs\InstanceDAO;
use Drupal\io_generic_abml\DAOs\AuthorDAO;

use Drupal\io_generic_abml\DTOs\ItemDTO;
use Drupal\io_generic_abml\DTOs\ItemTypeDTO;
use Drupal\io_generic_abml\DTOs\AuthorDTO;
use Drupal\io_generic_abml\DTOs\EditorialDTO;
use Drupal\io_generic_abml\DTOs\AcquisitionConditionDTO;
use Drupal\io_generic_abml\DTOs\UserDTO;

use \Drupal\Core\Entity\EntityStorageInterface;

/**
 * DAO class for item entity.
 */
class ItemDAO extends GenericDAO {
  /**
   * @var const TABLE_NAME
   *  Name of the db table related with the entity
   */
  private const TABLE_NAME = 'bn_item';
  /**
   * @var const TABLE_ALIAS
   *  Table alias used by the differents querys
   */
  private const TABLE_ALIAS = 'i';

  /**
   * To get multiple bn_item records.
   *
   * @param array $header
   *   The table header used to sort the results
   * @param string $search_key
   *   The search string to filter
   * @param int $limit
   *   The number of records to be fetched.
   */
  public static function getAll($search_item = NULL, $search_item_type = NULL, $limit = NULL) {
    if (!isset($limit))
      $limit = 10;
    $query = \Drupal::database()->select(self::TABLE_NAME, self::TABLE_ALIAS)
      ->fields(self::TABLE_ALIAS, [
        'id',
        'title',
        'cover',
        //'item_type_id',
        'parallel_title',
        'edition',
        'publication_place',
        //'editorial_id',
        'publication_year',
        'extension',
        'dimensions',
        'others_physical_details',
        'complements',
        'serie_title',
        'serie_number',
        'notes',
        'isbn',
        'issn',
        //'acquisition_condition_id',
        'acquisition_condition_notes',
        'createdon',
        'updatedon'
      ]);

    // Add join to bn_item_type table
    $query->join('bn_item_type', 'it', 'it.id = ' . self::TABLE_ALIAS . '.item_type_id');
    $query->fields('it', ['id', 'type', 'status']);

    // Add join to bn_editorial table
    $query->leftjoin('bn_editorial', 'ed', 'ed.id = ' . self::TABLE_ALIAS . '.editorial_id');
    $query->fields('ed', ['id', 'editorial', 'status']);

    // Add join to bn_acquisition_condition table
    $query->join('bn_acquisition_condition', 'aci', 'aci.id = ' . self::TABLE_ALIAS . '.acquisition_condition_id');
    $query->fields('aci', ['id', 'condition', 'status']);

    // Add the audit fields to the query.
    $query =  parent::addAuditFields($query, self::TABLE_ALIAS);

    // Now we have to check if user has selected any filter: $search_item_type
    if (isset($search_item_type)) {
      switch ($search_item_type) {
        case '0':
          # TODOS
          // If $search_key is not null means that need to add the where condition.
          if (!is_null($search_item)) {
            // -- Recuperar item por cualquier criterio
            // -- datos del item o autores o materia
            // SELECT it.id, it.title
            // FROM bn_item it
            // WHERE it.title LIKE '%LITERATURA%'
            // OR it.id IN (
            //       SELECT it2.id FROM bn_item it2
            //             JOIN bn_item_author ia2 ON ia2.item_id = it2.id
            //             JOIN bn_author a2 ON a2.id = ia2.author_id
            //             WHERE a2.first_name LIKE '%LITERATURA%' OR a2.last_name LIKE '%LITERATURA%'
            //       )
            // OR it.id IN (
            //       SELECT it3.id FROM bn_item it3
            //             JOIN bn_item_clasification ic3 ON ic3.item_id = it3.id
            //             JOIN bn_clasification c3 ON c3.id = ic3.clasification_id
            //             WHERE c3.code LIKE '%LITERATURA%' OR c3.materia LIKE '%LITERATURA%'
            //       );
                        
            // Add LEFT JOIN to bn_item_author table
            $query->leftjoin('bn_item_author', 'ite_aut', 'ite_aut.item_id = ' . self::TABLE_ALIAS . '.id');
            // Add LEFT JOIN to bn_author table
            $query->leftjoin('bn_author', 'aut', 'aut.id = ite_aut.author_id');
            $group = $query->orConditionGroup()
              ->condition(self::TABLE_ALIAS . '.title', "%" . Html::escape($search_item) . "%", 'LIKE')
              ->condition(self::TABLE_ALIAS . '.parallel_title', "%" . Html::escape($search_item) . "%", 'LIKE')
              ->condition('aut.first_name', "%" . Html::escape($search_item) . "%", 'LIKE')
              ->condition('aut.last_name', "%" . Html::escape($search_item) . "%", 'LIKE')
              ->condition(self::TABLE_ALIAS . '.isbn', "%" . Html::escape($search_item) . "%", 'LIKE')
              ->condition(self::TABLE_ALIAS . '.issn', "%" . Html::escape($search_item) . "%", 'LIKE');
            $query->condition($group);
          }
          break;
        case '1':
          # TITULO
          // If $search_key is not null means that need to add the where condition.
          if (!is_null($search_item)) {
            $group = $query->orConditionGroup()
            ->condition(self::TABLE_ALIAS . '.title', "%" . Html::escape($search_item) . "%", 'LIKE')
            ->condition(self::TABLE_ALIAS . '.parallel_title', "%" . Html::escape($search_item) . "%", 'LIKE');
            $query->condition($group);
          }
          break;

        case '2':
          # AUTOR
          if (!is_null($search_item)) {
            // Add LEFT JOIN to bn_item_author table
            $query->leftjoin('bn_item_author', 'ite_aut', 'ite_aut.item_id = ' . self::TABLE_ALIAS . '.id');
            // Add LEFT JOIN to bn_author table
            $query->leftjoin('bn_author', 'aut', 'aut.id = ite_aut.author_id');
            $group = $query->orConditionGroup()
              ->condition('aut.first_name', "%" . Html::escape($search_item) . "%", 'LIKE')
              ->condition('aut.last_name', "%" . Html::escape($search_item) . "%", 'LIKE');
            $query->condition($group);
          }
          break;

        case '3':
          # MATERIA
          // Relation between bn_item and bn?clasification throught bn_item_clasifiation table
          /**
           * SELECT it.id, it.title, c.id, c.materia 
           *   FROM biblioneet_dev.bn_item it
           *   JOIN biblioneet_dev.bn_item_clasification ic ON ic.item_id = it.id
           *   JOIN biblioneet_dev.bn_clasification c ON ic.clasification_id = c.id
           *   WHERE c.code LIKE '%82%' OR c.materia LIKE '%82%';
           *  */ 
          if (!is_null($search_item)) {
            // Add JOIN to bn_item_clasification table
            $query->join('bn_item_clasification', 'ite_cla', 'ite_cla.item_id = ' . self::TABLE_ALIAS . '.id');
            // Add JOIN to bn_item_clasification table
            $query->join('bn_clasification', 'cla', 'cla.id = ite_cla.clasification_id');
            // Add or conditions
            $group = $query->orConditionGroup()
              ->condition('cla.code', "%" . Html::escape($search_item) . "%", 'LIKE')
              ->condition('cla.materia', "%" . Html::escape($search_item) . "%", 'LIKE');
            $query->condition($group);
          }
          break;

        case '4':
          # ISBN
          // Add condition
          $query->condition(self::TABLE_ALIAS.'.isbn', "%" . Html::escape($search_item) . "%", 'LIKE');
          $query->condition(self::TABLE_ALIAS . '.issn', "%" . Html::escape($search_item) . "%", 'LIKE');
          break;

        default:
          # code...
          $a = 1;
          break;
      }
    }
    //$query->extend('Drupal\Core\Database\Query\TableSortExtender')->orderByHeader($header);
    // Add the range sentence (limit, lenght) to the query using the page number.
    $query = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit($limit);
    // Results array to be returned.
    $results = [];
    // Add to the results array the total rows count.
    $results = parent::addTotalCounterData($query, $results);
    // Query execution.
    $result = $query->execute()->fetchAll();
    // Add to the results the counter summary considering the pagination.
    $results = parent::addSummaryCounterData($query, $results, $limit);
    //Now we have to build the DTO list result.
    $resultsDTO = [];
    // DB results iterations
    foreach ($result as $key => $row) {
      $entityDTO = self::getItemDTOFromRecord($row);
      // Add element to result array
      array_push($resultsDTO, $entityDTO);
    }
    // Add the DTOs to the results array.
    $results['resultsDTO'] = $resultsDTO;
    return $results;
  }

  /**
   * To check if an author is valid.
   *
   * @param int $id
   *   The item ID.
   */
  public static function exists($id) {
    $result = \Drupal::database()->select(self::TABLE_NAME, self::TABLE_ALIAS)
      ->fields(self::TABLE_ALIAS, ['id'])
      ->condition('id', $id, '=')
      ->execute()
      ->fetchField();
    return (bool) $result;
  }

  /**
   * To update an existing record.
   *
   * @param int $id
   *   The item ID.
   * @param array $fields
   *   An array conating the author data in key value pair.
   */
  public static function update($id, array $fieldsItem,
    int $cover_fid = null,
    array $authorsItemsList,
    array $fieldsEditorial,
    array $clasificationItemsList) {
    
      // We open the transaction
    $transaction = \Drupal::database()->startTransaction();

    try {
      $item = ItemDAO::load($id);
      $controller = \Drupal::entityTypeManager()->getStorage('file');
      $file_usage = Drupal::service('file.usage');
      if ($cover_fid) {
        if ($cover_fid !== $item->getCover()) {
          if($item->getCover()) {
            $fileDelete = File::load($item->getCover());
            $controller->delete([$fileDelete]);
          }

          $file = File::load($cover_fid);
          $file->setPermanent();
          $file->save();
          $file_usage->add($file, 'article', 'file', $id);
        }
      } else {
        if($item->getCover()) {
          $fileDelete = File::load($item->getCover());
          $controller->delete([$fileDelete]);
        }
      }

      // Editorial
      // We need to create a new one?
      if($fieldsEditorial['id'] === -1) {
        unset($fieldsEditorial['id']);
        $newEditorialId = EditorialDAO::add($fieldsEditorial);
        // Set new Editorial to item
        $fieldsItem['editorial_id'] = $newEditorialId;
      } else if($fieldsEditorial['id'] === "" || $fieldsEditorial['id'] === "0") {
        // Set new Editorial to item
        $fieldsItem['editorial_id'] = null;
      } else {
        // Set Editorial seleted to item
        $fieldsItem['editorial_id'] = $fieldsEditorial['id'];
      }
      $userId = $fieldsItem['createdby'];

      \Drupal::database()->update(self::TABLE_NAME)->fields($fieldsItem)
        ->condition('id', $id)
        ->execute();

      // Remove items authors relationships
      Self::UnlinkAuthorToItem($id);
      // Link the authors to recently created item
      foreach ($authorsItemsList as $key => $author) {
        // Check if we must create a new author
        if($author->getId() === 0) {
          $authorFields = [
            'first_name' => $author->getFirstName(),
            'last_name' => $author->getLastName(),
            'status' => 1,
            'createdBy' => $userId,
            'createdOn' => date("Y-m-d h:m:s"),
          ];
          $idAuthor = AuthorDAO::add($authorFields);
        } else {
          $idAuthor = $author->getId();
        }

        Self::LinkAuthorToItem($id, $idAuthor, $userId);        
      }

      // Remove items authors relationships
      Self::UnlinkClasificationToItem($id);
      // Link clasifications to recently created item
      if(isset($clasificationItemsList)) {
        foreach ($clasificationItemsList as $key => $clasification) {
          Self::LinkClasificationToItem($id, $clasification->getId(), $userId);
        }
      }
    } catch (Exception $e) {
      $transaction->rollBack();
      watchdog_exception('io_generics_abml.add_article', $e);
    }

    // You can let $transaction go out of scope here and the transaction will
    // automatically be committed if it wasn't already rolled back.
    // However, if you have more work to do, you will want to commit the transaction
    // yourself, like this:
    unset($transaction);
  }

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
        'title',
        'cover',
        //'item_type_id',
        'parallel_title',
        'edition',
        'publication_place',
        //'editorial_id',
        'publication_year',
        'extension',
        'dimensions',
        'others_physical_details',
        'complements',
        'serie_title',
        'serie_number',
        'notes',
        'isbn',
        'issn',
        //'acquisition_condition_id',
        'acquisition_condition_notes',
        'createdon',
        'updatedon'
      ]);

    // Add join to bn_item_type table
    $query->join('bn_item_type', 'it', 'it.id = ' . self::TABLE_ALIAS . '.item_type_id');
    $query->fields('it', ['id', 'type', 'status']);

    // Add join to bn_editorial table
    $query->leftjoin('bn_editorial', 'ed', 'ed.id = ' . self::TABLE_ALIAS . '.editorial_id');
    $query->fields('ed', ['id', 'editorial', 'status']);

    // Add join to bn_acquisition_condition table
    $query->join('bn_acquisition_condition', 'aci', 'aci.id = ' . self::TABLE_ALIAS . '.acquisition_condition_id');
    $query->fields('aci', ['id', 'condition', 'status']);

    // Add join to bn_article_format table
    //$query->leftjoin('bn_article_format', 'af', 'af.id = ' . self::TABLE_ALIAS . '.article_format_id');
    //$query->fields('af', ['id', 'format', 'status']);

    // Get crator username
    $query = parent::addAuditFields($query, self::TABLE_ALIAS);

    $result = $query->condition(self::TABLE_ALIAS . '.id', $id, '=')->execute()->fetchObject();
    $objectDTO = self::getItemDTOFromRecord($result);

    return $objectDTO;
  }

  /**
   * To insert a new record into DB.
   *
   * @param array $fields
   *   An array conating the book data in key value pair.
   */
  public static function add(array $fieldsItem,
    int $cover_fid = null,
    array $authorsItemsList,
    array $fieldsEditorial,
    array $clasificationItemsList) {

    // We open the transaction
    $transaction = \Drupal::database()->startTransaction();

    try {
      // Editorial
      // We need to create a new one?
      if($fieldsEditorial['id'] === -1) {
        unset($fieldsEditorial['id']);
        $newEditorialId = EditorialDAO::add($fieldsEditorial);
        // Set new Editorial to item
        $fieldsItem['editorial_id'] = $newEditorialId;
      } else if($fieldsEditorial['id'] === "" || $fieldsEditorial['id'] === "0") {
        // Set new Editorial to item
        $fieldsItem['editorial_id'] = null;
      } else {
        // Set Editorial seleted to item
        $fieldsItem['editorial_id'] = $fieldsEditorial['id'];
      }

      // Create Item
      $idNewItem = \Drupal::database()->insert(self::TABLE_NAME)->fields($fieldsItem)->execute();

      $userId = $fieldsItem['createdby'];

      //Save cover file
      $file_usage = Drupal::service('file.usage');
      if ($cover_fid) {
        $file = File::load($cover_fid);
        $file->setPermanent();
        $file->save();
        $file_usage->add($file, 'article', 'file', $idNewItem);
      }

      // Link the authors to recently created item
      foreach ($authorsItemsList as $key => $author) {
        // Check if we must create a new author
        if($author->getId() === 0) {
          $authorFields = [
            'first_name' => $author->getFirstName(),
            'last_name' => $author->getLastName(),
            'status' => 1,
            'createdBy' => $userId,
            'createdOn' => date("Y-m-d h:m:s"),
          ];
          $idAuthor = AuthorDAO::add($authorFields);
        } else {
          $idAuthor = $author->getId();
        }

        Self::LinkAuthorToItem($idNewItem, $idAuthor, $userId);        
      }

      // Link clasifications to recently created item
      if(isset($clasificationItemsList)) {
        foreach ($clasificationItemsList as $key => $clasification) {
          Self::LinkClasificationToItem($idNewItem, $clasification->getId(), $userId);
        }
      }

      return $idNewItem;
    } catch (Exception $e) {
      $transaction->rollBack();
      watchdog_exception('io_generics_abml.add_article', $e);
    }

    // You can let $transaction go out of scope here and the transaction will
    // automatically be committed if it wasn't already rolled back.
    // However, if you have more work to do, you will want to commit the transaction
    // yourself, like this:
    unset($transaction);

    //return \Drupal::database()->insert(self::TABLE_NAME)->fields($fieldsBook)->execute();
  }

  /**
   * Get number of instance from a determined item
   * @param int $itemId
   * @param int $lendable
   * @return $count
   */
  public static function getItemAvalability($itemId, $lendable = 1) {
    /*
    SELECT COUNT(*) AS AVAILABLE
    FROM biblioneet_dev.bn_item ite
    JOIN bn_instance ins ON ins.item_id = ite.id
    JOIN bn_instance_status ist ON ins.instance_status_id = ist.id
    WHERE ite.id = 1 and ist.lendable = 1;
    */
    $query = \Drupal::database()->select(self::TABLE_NAME, self::TABLE_ALIAS);
    // Add join to bn_instance table
    $query->join('bn_instance', 'ins', 'ins.item_id = ' . self::TABLE_ALIAS . '.id');
    // Add join to bn_instance_status table
    $query->join('bn_instance_status', 'ist', 'ist.id = ins.instance_status_id');
    $query->condition(self::TABLE_ALIAS . '.id', $itemId, '=');
    $query->condition('ist.lendable', $lendable, '=');
    $result = $query->countQuery()->execute()->fetchField();

    return $result;
  }

  /**
   * Get the list of Items Types in the select format
   */
  public static function getItemsTypesSelectFormat($status = NULL, $opcion_vacia) {
    $select_options = parent::getListSelectFormat('bn_item_type', 'type', $status, $opcion_vacia);

    return $select_options;
  }

  /**
   * Get the list of Acquisition Conditions Types in the select format
   */
  public static function getAcquisitionConditionsTypesSelectFormat($status = NULL, $opcion_vacia) {
    $select_options = parent::getListSelectFormat('bn_acquisition_condition', 'condition', $status, $opcion_vacia);

    return $select_options;
  }

  /**
   * Get the list of Article Formats in the select format
   */
  public static function getArticlesFormatsSelectFormat($status = NULL, $opcion_vacia) {
    $select_options = parent::getListSelectFormat('bn_article_format', 'format', $status, $opcion_vacia);

    return $select_options;
  }

  /**
   * Get the list of Article Formats in the select format
   */
  public static function getEditorialesSelectFormat($newOption = false, $status = NULL, $opcion_vacia) {
    $query = \Drupal::database()->select('bn_editorial', 'e')
      ->fields('e', ['id', 'editorial', 'status']);
    if(isset($status))
      $query->condition('e.status', $status, '=');
    // Query execution.
    $result = $query->execute()->fetchAll();

    $select_options = [];
    //$select_options[0] = 'Seleccione un Autor';
    if ($newOption)
      $select_options[-1] = 'Crear nueva Editorial';
    foreach ($result as $key => $row) {
      $select_options[$row->id] = $row->editorial;
    }
    return $select_options;
  }

  /**
   * To insert a new record into DB.
   *
   * @param array $fields
   *   An array conating the bn_item_author data in key value pair.
   */
  public static function LinkAuthorToItem(int $idNewItem, int $idAuthor, int $userId ) {
    $fields = [
      'item_id' => $idNewItem,
      'author_id' => $idAuthor,
      'createdby' => $userId,
      'createdOn' => date("Y-m-d h:m:s"),
    ];
    return \Drupal::database()->insert('bn_item_author')->fields($fields)->execute();
  }

  /**
   * To delete relationships between authors and an item
   *
   * @param int $id
   *   Item ID
   */
  public static function UnlinkAuthorToItem(int $idItem) {
    return \Drupal::database()->delete('bn_item_author')->condition('item_id', $idItem)->execute();
  }

  /**
   * To insert a new record into DB.
   *
   * @param array $fields
   *   An array conating the bn_item_clasification data in key value pair.
   */
  public static function LinkClasificationToItem(int $idNewItem, int $idClasification, int $userId ) {
    $fields = [
      'item_id' => $idNewItem,
      'clasification_id' => $idClasification,
      'createdby' => $userId,
      'createdOn' => date("Y-m-d h:m:s"),
    ];
    return \Drupal::database()->insert('bn_item_clasification')->fields($fields)->execute();
  }

  /**
   * To delete relationships between clasification and an item
   *
   * @param int $id
   *   Item ID
   */
  public static function UnlinkClasificationToItem(int $idItem) {
    return \Drupal::database()->delete('bn_item_clasification')->condition('item_id', $idItem)->execute();
  }

  /** Utis methods *********************************************************************************/
  /**
   * Create a ItemDTO from stdClass from DB Record
   *
   * @param stdClass $row
   *   stdClass DB record
   * @return ItemDTO $itemDTO
   *   DTO object
   */
  private static function getItemDTOFromRecord($row) {
    $itemDTO = new ItemDTO();
    $createdBy = new UserDTO();
    $updatedBy = new UserDTO();

    // set simple fields
    $itemDTO->setId($row->id);
    $itemDTO->setTitle($row->title);

    // Item Type DGM
    if (isset($row->it_id)) {
      $itemTypeDTO = new ItemTypeDTO();
      $itemTypeDTO->setId($row->it_id);
      $itemTypeDTO->setType($row->type);
      $itemTypeDTO->setStatus($row->status);
      $itemDTO->setItemType($itemTypeDTO);
    }

    $itemDTO->setParallelTitle($row->parallel_title);
    $itemDTO->setCover($row->cover);
    $itemDTO->setEdition($row->edition);
    $itemDTO->setPublicationPlace($row->publication_place);

    // Editorial
    if (isset($row->ed_id)) {
      $editorialDTO = new EditorialDTO();
      $editorialDTO->setId($row->ed_id);
      $editorialDTO->setEditorial($row->editorial);
      $editorialDTO->setStatus($row->status);
      $itemDTO->setEditorial($editorialDTO);
    }

    $itemDTO->setPublicationYear($row->publication_year);
    $itemDTO->setExtension($row->extension);
    $itemDTO->setDimensions($row->dimensions);
    $itemDTO->setOthersPhysicalDetails($row->others_physical_details);
    $itemDTO->setComplements($row->complements);
    $itemDTO->setSerieTitle($row->serie_title);
    $itemDTO->setSerieNumber($row->serie_number);
    $itemDTO->setNotes($row->notes);
    $itemDTO->setIsbn($row->isbn);
    $itemDTO->setIssn($row->issn);

    // Acquison Condition
    if (isset($row->aci_id)) {
      $acquisitionConditionDTO = new AcquisitionConditionDTO();
      $acquisitionConditionDTO->setId($row->aci_id);
      $acquisitionConditionDTO->setCondition($row->condition);
      $acquisitionConditionDTO->setStatus($row->status);
      $itemDTO->setAcquisitionCondition($acquisitionConditionDTO);
    }

    $itemDTO->setAcquisitionConditionNotes($row->acquisition_condition_notes);

    // set audit fields
    $createdBy->setUid($row->createdby_uid);
    $createdBy->setUsername($row->createdby);
    $itemDTO->setCreatedBy($createdBy);

    $itemDTO->setCreatedOn($row->createdon);

    $updatedBy->setUid($row->updatedby_uid);
    $updatedBy->setUsername($row->updatedby);
    $itemDTO->setUpdatedBy($updatedBy);

    $itemDTO->setUpdatedOn($row->updatedon);

    return $itemDTO;
  }
}
