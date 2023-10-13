<?php
declare(strict_types=1);
namespace TdNewsletter\Sanitize;

use TdNewsletter\Constants;

class Sanitize
{
  private $textDomain;

  public function __construct($textDomain) {
    $this->textDomain = $textDomain;
}

  public function email(): callable
  {
    return function (string $param, \WP_REST_Request $request, string $key) {
      $sane = sanitize_email($param);
      if ($sane !== $param) {
        return new \WP_Error('invalid_rest_param', esc_html__('Email is invalid', $this->textDomain), ['status' => 400]);
      }
      return $sane;
    };
  }


  public function password(): callable
  {
    return function (string $param, \WP_REST_Request $request, string $key) {
      $sane = sanitize_text_field($param);
      if ($sane !== $param) {
        return new \WP_Error('invalid_rest_param', esc_html__('Password is invalid', $this->textDomain), ['status' => 400]);
      }
      return $sane;
    };
  }

  public function textField(): callable
  {
    return function (string $param, \WP_REST_Request $request, string $key) {
      $sane = sanitize_text_field($param);
      if ($sane !== $param) {
        return new \WP_Error('invalid_rest_param', sprintf(__('%s is invalid', $this->textDomain), $key), ['status' => 400]);
      }
      return $sane;
    };
  }


}