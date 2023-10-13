<?php

declare(strict_types=1);

namespace TdNewsletter\Sanitize;

interface SanitizerInterface {
  public function sanitize($value);
}
