<?php

// declare(strict_types=1);
/**
 * Class EntityManager
 *
 * This class is responsible for managing entities and their persistence in the database.
 *
 * @package TdNewsletter\Service\Manager
 */


namespace TdNewsletter\Entity\Service;

use TdNewsletter\Entity\Model\Entity;

class EntityManager {

  /**
   * @var \WPDB The WordPress database object.
   */
  private \WPDB $wpdb;

  /**
   * @var array An array mapping table names to entity classes.
   */
  private array $entityClasses;

  /**
   * EntityManager constructor.
   *
   * @param array $entityClasses An array mapping table names to entity classes.
   */
  public function __construct() {
    global $wpdb;
    $this->wpdb = $wpdb;
  }


  /**
   * Retrieves an entity by its ID from the database.
   *
   * @param string $entityClassName The entity object.
   * @param int $id The entity ID.
   * @return ?Entity The retrieved entity or null.
   */
  public function getById(string $entityClassName, $id): ?Entity {
    $tableName = $this->getTable($entityClassName);

    $query = $this->wpdb->prepare("SELECT * FROM {$tableName} WHERE id = %d", $id);
    $result = $this->wpdb->get_row($query);
    if ($result === null)
      return null;
    $entity = new $entityClassName();
    foreach ($result as $column => $value) {
      if ($column === 'id') {
        $entity->$column = intval($value);
      } elseif (in_array($column, $entity->getSerializedProps())) {
        $value = unserialize($value);
      } else {
        $entity->$column = $value;
      }
    }

    return $entity;
  }

  /**
   * Retrieves an entity by its ID from the database.
   * @param string $entityClassName The entity object.
   * @param string $column
   * @param mixed $columnValue
   * @return ?Entity The retrieved entity or null.
   * @template T of Entity
   * @psalm-param class-string<T> $entityClassName
   * @psalm-return T|null
   */
  public function getBy(string $entityClassName, string $column, $columnValue) {
    $tableName = $this->getTable($entityClassName);
    
    $query = $this->wpdb->prepare("SELECT * FROM {$tableName} WHERE {$column} = %s", $columnValue);
    $result = $this->wpdb->get_row($query);
    if ($result === null)
      return null;
    $entity = new $entityClassName();
    foreach ($result as $column => $value) {
      if (in_array($column, $entity->getSerializedProps())) {
        $value = unserialize($value);
      }

      $entity->$column = $value;
    }

    return $entity;
  }

  public function getAllBy(string $entityClassName, string $column, $columnValue): array {
    $tableName = $this->getTable($entityClassName);

    $query = $this->wpdb->prepare("SELECT * FROM {$tableName} WHERE {$column} = %s", $columnValue);
    $results = $this->wpdb->get_results($query);
    if ($results === null)
      return [];

    $entities = [];
    foreach ($results as $result) {
      $entity = new $entityClassName();
      foreach ($result as $column => $value) {
        if ($column === 'id') {
          $entity->$column = intval($value);
        } elseif (in_array($column, $entity->getSerializedProps())) {
          $value = unserialize($value);
        } else {
          $entity->$column = $value;
        }
      }
      $entities[] = $entity;
    }

    return $entities;
  }


  /**
   * Saves an entity to the database.
   *
   * @param Entity $entity The entity to be saved.
   * @return bool True if the save operation was successful, false otherwise.
   * @throws \Exception if couldn't save
   */
  public function save(Entity $entity): int {
    $tableName = $this->getTable($entity);

    $data = get_object_vars($entity);

    foreach ($entity->getSerializedProps() as $prop) {
      if (isset($data[$prop])) {
        $data[$prop] = serialize($data[$prop]);
      }
    }

    if ($entity->id !== 0) {
      $result = $this->wpdb->update($tableName, $data, ['id' => $entity->id]);
      if (!$result) {
        throw new \Exception("Error Updating the Entity");
      }
    } else {
      $result = $this->wpdb->insert($tableName, $data);
      if ($result) {
        $entity->id = $this->wpdb->insert_id;
      } else {
        throw new \Exception("Error Inserting the Entity");
      }
    }

    return $entity->id;
  }

  /**
   * Retrieves all entities from the database for a given table.
   *
   * @param string $entityClassName The class name for the entity.
   * @return Entity[] An array of retrieved entities.
   */
  public function getAll(string $entityClassName): array {
    $table = $this->getTable($entityClassName);
    $query = "SELECT * FROM {$table}";
    $results = $this->wpdb->get_results($query);

    $entities = [];

    foreach ($results as $result) {
      $entity = new $entityClassName();
      foreach ($result as $column => $value) {
        if ($column === 'id') {
          $entity->$column = intval($value);
        } elseif (in_array($column, $entity->getSerializedProps())) {
          $value = unserialize($value);
        } else{
          $entity->$column = $value;
        }
      }
      $entities[] = $entity;
    }

    return $entities;
  }

  /**
   * Deletes an entity from the database.
   *
   * @param Entity $entity The entity to be deleted.
   * @return bool True if the delete operation was successful, false otherwise.
   * @throws \Exception When attempting to delete an entity without an ID.
   */
  public function delete(Entity $entity): bool {
    $tableName = $this->getTable($entity);

    if (!$entity->id) {
      throw new \Exception('Cannot delete entity without ID');
    }

    $result = $this->wpdb->delete($tableName, ['id' => $entity->id]);

    return $result !== false;
  }

  /**
   * Get the table name for a given entity object.
   * Assuming that the table name starts with the wordpress prefix and is snake case
   * @param Entity|string $entity The entity object or its class name.
   * @return string The table name.
   */
  private function getTable($entity): string {
    if (is_string($entity)) {
      $className = $entity;
    } elseif ($entity instanceof Entity) {
      $className = get_class($entity);
    }
    $classNameParts = explode('\\', $className);
    $className = end($classNameParts);
    return $this->wpdb->prefix . strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className));
  }
}
