<?php

namespace Drupal\io_generic_abml\DAOs;

/**
 * DAO class for utils methods.
 */
class UtilsDAO {

  /**
   * To get multiple countires records.
   *
   * @param int $limit
   *   The number of records to be fetched.
   * @param string $orderBy
   *   The field on which the sorting to be performed.
   * @param string $order
   *   The sorting order. Default is 'DESC'.
   */
  public static function getAllCountries($limit = NULL, $orderBy = NULL, $order = 'ASC') {
    $query = \Drupal::database()->select('bn_countries', 'c')
      ->fields('c');
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
   * Undocumented function
   *
   * @param int $id
   *    The Country id searched
   * @return string
   *    Name of the country searched
   */
  public static function getCountryNameById($id) {
    $result = \Drupal::database()->query("SELECT c.en_short_name FROM {bn_countries} AS c WHERE c.id = :id", [':id' => $id]);
    return $result;
  }
}
