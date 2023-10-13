<?php
declare(strict_types=1);

namespace TdNewsletter\Entity\Model;

abstract class Entity
{
  /**
   * @var int The entity ID.
   */
  public int $id = 0;
    /**
   * Get the properties that ought to be serialized
   * @return string[]
   */
  public function getSerializedProps(): array
  {
    return [];
  }
}