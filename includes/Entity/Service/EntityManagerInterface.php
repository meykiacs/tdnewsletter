<?php
declare(strict_types=1);

namespace TdNewsletter\Entity\Service;

use TdNewsletter\Entity\Model\Entity;

interface EntityManagerInterface
{
  public function getById(string $entityClassName, $id): Entity;
  public function save(Entity $entity): bool;
  public function getAll(Entity $entity): array;
  public function delete(Entity $entity): bool;
}