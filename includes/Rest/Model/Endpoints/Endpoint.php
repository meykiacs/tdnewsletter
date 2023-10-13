<?php

declare(strict_types=1);

namespace TdNewsletter\Rest\Model\Endpoints;

use TdNewsletter\Rest\Model\Fields\Field;

abstract class Endpoint {
  protected string $method;

  /**
   * @var Field[]
   */
  public array $fields = [];


  public function __construct(string $method) {
    $this->method = $method;
  }

  abstract public function getPermissionCallback(): callable;

  abstract public function getCallback(): callable;


  final public function addField(Field $field): self {
    $this->fields[] = $field;
    return $this;
  }

  final public function asArgs(): array {
    $args = [];
    foreach ($this->fields as $field) {
      $args[$field->getName()] = $field->asArgs();
    }
    return $args;
  }

  final public function getMethod(): string {
    return $this->method;
  }
}
