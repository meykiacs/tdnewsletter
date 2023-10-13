<?php

declare(strict_types=1);

namespace TdNewsletter\Validate;

interface ValidatorInterface {
  public function validate($value): bool;
}
