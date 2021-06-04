<?php

namespace Drupal\io_generic_abml\DAOs;

use Drupal\Component\Utility\Html;

use Drupal\io_generic_abml\DTOs\UserDTO;
use Drupal\io_generic_abml\DTOs\IndexDTO;
use Drupal\io_generic_abml\DTOs\ItemDTO;

/**
 * DAO class for Index entity.
 */
class IndexDAO extends GenericDAO {
    /**
     * @var const TABLE_NAME
     *  Name of the db table related with the entity
     */
    private const TABLE_NAME = 'bn_index';
    /**
     * @var const TABLE_ALIAS
     *  Table alias used by the differents querys
     */
    private const TABLE_ALIAS = 'ind';

  /**
   * To get multiples Index records.
   *
   * @param int $limit
   *   The number of records to be fetched.
   * @param string $orderBy
   *   The field on which the sorting to be performed.
   * @param string $order
   *   The sorting order. Default is 'DESC'.
   */
//   public static function getAll($orderBy = NULL, $order = 'DESC', $limit = NULL) {
    
//     $query = \Drupal::database()->select('io_localizacion', 'loc')
//       ->fields('loc', ['id', 'localizacion', 'localizacion_id', 'peso', 'fecha_alta', 'fecha_mod']);
//      // Get crator username 
//      $query->addField('usualt', 'uid', 'usuario_alta_uid');
//      $query->addField('usualt', 'name', 'usuario_alta');
//      // Get updater username 
//      $query->addField('usumod', 'uid', 'usuario_mod_uid');
//      $query->addField('usumod', 'name', 'usuario_mod');
//      // Get localizacon_id PADRE
//      $query->addField('locpad', 'id', 'loc_padre_id');
//      $query->addField('locpad', 'localizacion', 'loc_padre_localizacion');
//      // Join with user table
//      $query->join('users_field_data', 'usualt', 'usualt.uid = loc.usuario_alta');
//      $query->leftJoin('users_field_data', 'usumod', 'usumod.uid = loc.usuario_mod');
//      // Join with localizacion table
//      $query->leftJoin('io_localizacion', 'locpad', 'locpad.id = loc.localizacion_id');
    
//     $limit = 100;
    
//     if ($limit) {
//       $query->range(0, $limit);
//     }
//     if ($orderBy) {
//       $query->orderBy($orderBy, $order);
//     }
//     $result = $query->execute()
//       ->fetchAll();

//     //Now we have to build the DTO list result.
//     $resultsDTO = [];
//     // DB results iterations
//     foreach ($result as $key => $row) {
//       $localizacionDTO = LocalizacionDAO::getLocalizacionDTOFromRecord($row);

//       // Add element to result array
//       array_push($resultsDTO, $localizacionDTO);
//     }

//     return $resultsDTO;
//   }

  /**
   * To load an io_localizacion record.
   *
   * @param int $id
   *   The localizacion ID.
   */
  public static function load($idItem) {
    $query = \Drupal::database()->select(self::TABLE_NAME, self::TABLE_ALIAS)
    ->fields(self::TABLE_ALIAS, ['id', 'content', 'number', 'index_id', 'item_id', 'peso', 'createdon', 'updatedon']);
    // Add the audit fields to the query.
    $query =  parent::addAuditFields($query, self::TABLE_ALIAS);
    //$query->condition(self::TABLE_ALIAS. '.item_id', $idItem, '=');

    // Join with index table
    $query->leftJoin('bn_index', 'indpad', 'indpad.id = ind.index_id');

    $result = $query->condition('ind.id', $idItem, '=')->execute()->fetchObject();
    $indexDTO = IndexDAO::getIndexDTOFromRecord($result);
    
    return $indexDTO;
  }

  /**
   * Load all items in a tree form
   *
   * @return  IndexDTO array
   *   DTOs array 
   */
  public static function getIndexTreeByItemId($idItem) {
    $query = \Drupal::database()->select(self::TABLE_NAME, self::TABLE_ALIAS)
    ->fields(self::TABLE_ALIAS, ['id', 'content', 'number', 'index_id', 'item_id', 'peso', 'createdon', 'updatedon']);
    // Add the audit fields to the query.
    $query =  parent::addAuditFields($query, self::TABLE_ALIAS);
    $query->condition(self::TABLE_ALIAS. '.item_id', $idItem, '=');

    $result = $query->isNull('ind.index_id')
      ->orderBy('ind.peso')
      ->execute()
      ->fetchAll();
    //Now we have to build the DTO list result.
    $resultsDTO = [];
    // DB results iterations
    foreach ($result as $key => $row) {
      $indexDTO = IndexDAO::getIndexDTOFromRecord($row);

      // Add element to result array
      array_push($resultsDTO, $indexDTO);
    }

    // Initialize a variable to store our ordered tree structure.
    $tree = [];

    // Depth will be incremented in our getTree()
    // function for the first parent item, so we start it at -1.
    $depth = -1;

    // Loop through the root item, and add their trees to the array.
    foreach ($resultsDTO as $root_item) {
      self::getTree($root_item, $tree, $depth);
    }

    return $tree;
  }

  /**
   * Load all items in a tree form to be rendered into Select component
   *
   * @return  array
   *   asociative common array 
   */
  public static function getIndexTreeSelect($idItem) {
    $query = \Drupal::database()->select(self::TABLE_NAME, self::TABLE_ALIAS)
    ->fields(self::TABLE_ALIAS, ['id', 'content', 'number', 'index_id', 'item_id', 'peso', 'createdon', 'updatedon']);
    // Add the audit fields to the query.
    $query =  parent::addAuditFields($query, self::TABLE_ALIAS);
    $query->condition(self::TABLE_ALIAS. '.item_id', $idItem, '=');

    $result = $query->isNull('ind.index_id')
      ->orderBy('ind.peso')
      ->execute()
      ->fetchAll();

    //Now we have to build the DTO list result.
    $resultsDTO = [];
    // DB results iterations
    foreach ($result as $key => $row) {
      $indexDTO = self::getIndexDTOFromRecord($row);

      // Add element to result array
      array_push($resultsDTO, $indexDTO);
    }

    // Initialize a variable to store our ordered tree structure.
    $tree = [];

    // Depth will be incremented in our getTree()
    // function for the first parent item, so we start it at -1.
    $depth = -1;

    // Loop through the root item, and add their trees to the array.
    foreach ($resultsDTO as $root_item) {
      self::getTree($root_item, $tree, $depth);
    }
    
    $renderArray = [];
    foreach ($tree as $k => $item) {
      $identation = '';
      for ($i=0; $i < $item->getPeso(); $i++) { 
        $identation.= '-';
      }
      $renderArray[$item->getId()] = $identation . ' ' . $item->getContent();
    }
    return $renderArray;
  }

  /**
   * Save Localizaciones tree changes
   *
   * @param array $sumbission
   *   Form table with rows: each row is a Localizacion
   */
  public static function updateLocalizacionTree($items) { 
    foreach ($items as $id => $item) {
      \Drupal::database()->update('io_localizacion')
          ->fields([
          'peso' => $item['weight'],
          'localizacion_id' => (($item['pid'] != null && $item['pid'] != '') ? $item['pid'] : null),
          ])
          ->condition('id', $id, '=')
          ->execute();
      }
  }

  /**
   * To check if a Localizacion is valid.
   *
   * @param int $id
   *   The localizacion ID.
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
   * To insert a new io_localizacion record.
   *
   * @param array $fields
   *   An array conating the localizacion data in key value pair.
   */
  public static function add(array $fields) {
    return \Drupal::database()->insert(self::TABLE_NAME)->fields($fields)->execute();
  }

  /**
   * To update an existing io_localizacion record.
   *
   * @param int $id
   *   The io_localizacion ID.
   * @param array $fields
   *   An array conating the io_localizacion data in key value pair.
   */
  public static function update($id, array $fields) {
    return \Drupal::database()->update(self::TABLE_NAME)->fields($fields)
      ->condition('id', $id)
      ->execute();
  }

  /**
   * To delete a specific io_localizacion record.
   *
   * @param int $id
   *   The io_localizacion ID.
   */
  public static function delete($id) {
    $record = self::load($id);
    return \Drupal::database()->delete(self::TABLE_NAME)->condition('id', $id)->execute();
  }

  /**
   * To check if a Localizacion is a leaf.
   *
   * @param int $id
   *   The localizacion ID.
   */
  public static function esHoja($id) {
    $result = \Drupal::database()->select(self::TABLE_NAME, self::TABLE_ALIAS)
      ->fields(self::TABLE_ALIAS, ['id'])
      ->condition('index_id', $id, '=')
      ->execute()
      ->fetchField();
    return !(bool) $result;
  }

  /** Utils Methods Section **********************************************************/

  /**
   * Create a IndexDTO from stdClass from DB Record
   *
   * @param stdClass $row
   *   stdClass DB record
   * @return IndexDTO $indexDTO
   *   DTO object 
   */
  private static function getIndexDTOFromRecord($row) {
    $indexDTO = new IndexDTO();
    $createdBy = new UserDTO();
    $updatedBy = new UserDTO();

    $indexDTO->setId($row->id);
    $indexDTO->setContent($row->content);
    $indexDTO->setNumber($row->number);
    $indexDTO->setPeso($row->peso);

    //Has father? index_id
    if(isset($row->index_id)){
        $indexPadreDTO = new IndexDTO();
        $indexPadreDTO->setId($row->index_id);
        $indexDTO->setIndexPadre($indexPadreDTO);
    }

    //item
    if(isset($row->item_id)){
        $itemDTO = new ItemDTO();
        $itemDTO->setId($row->item_id);
        $indexDTO->setIndexPadre($itemDTO);
    }

    // set audit fields
    $createdBy->setUid($row->createdby_uid);
    $createdBy->setUsername($row->createdby);
    $indexDTO->setCreatedBy($createdBy);

    $indexDTO->setCreatedOn($row->createdon);

    $updatedBy->setUid($row->updatedby_uid);
    $updatedBy->setUsername($row->updatedby);
    $indexDTO->setUpdatedBy($updatedBy);

    $indexDTO->setUpdatedOn($row->updatedon);

    return $indexDTO;
  }

  /**
   * Recursively adds $item to $item_tree, ordered by parent/child/weight.
   *
   * @param mixed $item
   *   The item.
   * @param array $tree
   *   The item tree.
   * @param int $depth
   *   The depth of the item.
   */
  private static function getTree($item, array &$tree = [], &$depth = 0) {
    // Increase our $depth value by one.
    $depth++;

    // Set the current tree 'depth' for this item, used to calculate
    // indentation.
    //$item->depth = $depth;
    $item->setPeso($depth);

    // Add the item to the tree.
    $tree[$item->getId()] = $item;

    // Retrieve each of the children belonging to this nested demo.
    $query = \Drupal::database()->select(self::TABLE_NAME, self::TABLE_ALIAS)
    ->fields(self::TABLE_ALIAS, ['id', 'content', 'number', 'index_id', 'item_id', 'peso', 'createdon', 'updatedon']);
    // Add the audit fields to the query.
    $query =  parent::addAuditFields($query, self::TABLE_ALIAS);
    

    // Get index_id PADRE
    $query->addField('indpad', 'id', 'index_id');
    $query->addField('indpad', 'content', 'number', 'ind_padre_index');
    
    // Join with index table
    $query->leftJoin('bn_index', 'indpad', 'indpad.id = ind.index_id');
   

    $children = $query->condition('ind.index_id', $item->getId(), '=')
      ->orderBy('peso')
      ->execute()
      ->fetchAll();

    foreach ($children as $child) {
      $indexChildDTO = self::getIndexDTOFromRecord($child);

      // Make sure this child does not already exist in the tree, to
      // avoid loops.
      if (!in_array($indexChildDTO->getId(), array_keys($tree))) {
        // Add this child's tree to the $itemtree array.
        self::getTree($indexChildDTO, $tree, $depth);
      }
    }

    // Finished processing this tree branch.  Decrease our $depth value by one
    // to represent moving to the next branch.
    $depth--;
  }
}