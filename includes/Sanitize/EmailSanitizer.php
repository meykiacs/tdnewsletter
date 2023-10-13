<?php

declare(strict_types=1);

namespace TdNewsletter\Sanitize;

use DI\Container;

class EmailSanitizer implements SanitizerInterface {
  private $textDomain;
  public function __construct(Container $c) {
    $this->textDomain = $c->get('textDomain');
  }
  public function sanitize($value) {
    $sane = sanitize_email($value);
    if ($sane !== $value) {
      return new \WP_Error('invalid_rest_param', esc_html__('Email is invalid', $this->textDomain), ['status' => 400]);
    }
    return $sane;
  }
}
