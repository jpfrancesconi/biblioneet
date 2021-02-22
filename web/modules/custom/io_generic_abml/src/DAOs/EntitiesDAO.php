<?php

namespace Drupal\biblioneet\DAOs;

/**
 * DAO class for author table.
 */
class AuthorDAO {

  /**
   * To get multiple authors records.
   *
   * @param int $limit
   *   The number of records to be fetched.
   * @param string $orderBy
   *   The field on which the sorting to be performed.
   * @param string $order
   *   The sorting order. Default is 'DESC'.
   */
  public static function getAll($limit = NULL, $orderBy = NULL, $order = 'DESC') {
    $query = \Drupal::database()->select('bn_author', 'a')
      ->fields('a');
    if ($limit) {
      $query->range(0, $limit);
    }
    if ($orderBy) {
      $query->orderBy($orderBy, $order);
    }
    $result = $query->execute()
      ->fetchAll();
    return $result;
  }

  /**
   * To check if an author is valid.
   *
   * @param int $id
   *   The author ID.
   */
  public static function exists($id) {
    $result = \Drupal::database()->select('bn_author', 'a')
      ->fields('a', ['id'])
      ->condition('id', $id, '=')
      ->execute()
      ->fetchField();
    return (bool) $result;
  }

  /**
   * To load an author record.
   *
   * @param int $id
   *   The author ID.
   */
  public static function load($id) {
    $result = \Drupal::database()->select('bn_author', 'a')
      ->fields('a')
      ->condition('id', $id, '=')
      ->execute()
      ->fetchObject();
    return $result;
  }

  /**
   * To load an author record with joins.
   *
   * @param int $id
   *   The author ID.
   */
  public static function loadWithProperties($id) {
    $query = \Drupal::database()->select('bn_author', 'a');
    $query->leftjoin('bn_countries', 'c', 'c.id = a.nationality');
    $query->fields('a', ['id', 'first_name', 'last_name', 'picture', 'nationality', 'status', 'description', 'createdby', 'createdon', 'updatedby', 'updatedon']);
    //$query->fields('a');
    $query->fields('c', ['id', 'en_short_name']);
    $query->condition('a.id', $id, '=');
    $result = $query->execute()->fetchObject();
    return $result;
  }

  /**
   * To insert a new author record.
   *
   * @param array $fields
   *   An array conating the author data in key value pair.
   */
  public static function add(array $fields) {
    return \Drupal::database()->insert('bn_author')->fields($fields)->execute();
  }

  /**
   * To update an existing employee record.
   *
   * @param int $id
   *   The author ID.
   * @param array $fields
   *   An array conating the author data in key value pair.
   */
  public static function update($id, array $fields) {
    return \Drupal::database()->update('bn_author')->fields($fields)
      ->condition('id', $id)
      ->execute();
  }

  /**
   * To delete a specific author record.
   *
   * @param int $id
   *   The author ID.
   */
  public static function delete($id) {
    $record = self::load($id);
    if ($record->picture) {
      file_delete($record->picture);
    }
    return \Drupal::database()->delete('bn_author')->condition('id', $id)->execute();
  }

  /**
   * To activate/ block the author record.
   *
   * @param int $id
   *   The author ID.
   * @param int $status
   *   Set 1 for activatng and 0 for blocking.
   */
  public static function changeStatus($id, $status) {
    return self::update($id, ['status' => ($status) ? 1 : 0]);
  }
}
