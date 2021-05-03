<?php

namespace Drupal\io_generic_abml\DAOs;

use Drupal\Component\Utility\Html;

use Drupal\io_generic_abml\DAOs\GenericDAO;

use Drupal\io_generic_abml\DTOs\BookDTO;
use Drupal\io_generic_abml\DTOs\EditorialDTO;
use Drupal\io_generic_abml\DTOs\UserDTO;

use Drupal\io_generic_abml\DAOs\ArticleDAO;

/**
 * DAO class for editorial entity.
 */
class EditorialDAO extends GenericDAO {
    /**
     * @var const TABLE_NAME
     *  Name of the db table related with the entity
     */
    private const TABLE_NAME = 'bn_editorial';
    /**
     * @var const TABLE_ALIAS
     *  Table alias used by the differents querys
     */
    private const TABLE_ALIAS = 'e';

    /**
     * To insert a editorial record.
     *
     * @param array $fields
     *   An array conating the editorial data in key value pair.
     */
    public static function add(array $fields) {
        $idEditorial = \Drupal::database()->insert(self::TABLE_NAME)->fields($fields)->execute();
        return $idEditorial;
    }

}