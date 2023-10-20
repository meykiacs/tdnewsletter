<?php

declare(strict_types=1);

namespace TdNewsletter\CPTResource\Model;

abstract class Field {
  public string $slug = 'defaultField';
  public string $type = 'string';
  public string $description = '';

  public function getCallback(): ?callable {
    return null;
  }

  public function getUpdateCallback(): ?callable {
    return null;
  }
  public function getSanitizeCallback(): ?callable {
    return null;
  }
  public function getValidateCallback(): ?callable {
    return null;
  }
}
