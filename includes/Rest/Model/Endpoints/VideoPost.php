<?php
declare(strict_types=1);
namespace TdNewsletter\Rest\Model\Endpoints;


class VideoPost extends Endpoint
{
  public function __construct()
  {
    parent::__construct('POST');
  }

  public function getPermissionCallback(): callable
  {
    return fn() => is_user_logged_in();
  }

  public function getCallback(): callable
  {
    /**
     * @return \WP_REST_Response|\WP_Error
     */
    return function (\WP_REST_Request $request) {
        return new \WP_REST_Response([], 201);
    };
  }

}