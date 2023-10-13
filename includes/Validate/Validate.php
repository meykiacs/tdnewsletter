<?php
declare(strict_types=1);

namespace TdNewsletter\Validate;

class Validate
{

  public function email(): callable
  {
    return function (string $param, \WP_REST_Request $request, string $key) {
      return is_email($param);
    };
  }

  public function password(): callable
  {
    return function (string $param, \WP_REST_Request $request, string $key) {
      return
        preg_match('/^(?=.*\d)(?=.*[@#\-_$%^&+=§!\?])(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z@#\-_$%^&+=§!\?]{8,20}$/', $param) === 1;
    };
  }

  public function textField(): callable
  {
    return function (string $param, \WP_REST_Request $request, string $key) {
      return is_string($param);
    };
  }

  public function nonEmptyTextField() : callable {
    return function (string $param, \WP_REST_Request $request, string $key) {
      return self::textField()($param, $request, $key) && (!empty($param));
    };
  }

}