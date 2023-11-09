<?php

declare(strict_types=1);

namespace TdNewsletter\CPTResource\Model;

abstract class Meta {
  public string $slug;
  public string $type;
  public $schema;
  public string $description = '';
  public bool $single = true;
  public bool $showInRest = true;

  public function sanitizeCallback(): ?callable {
    return null;
  }

  public function authCallback(): callable {
    return function () {
      return current_user_can('edit_posts');
    };
  }
}
