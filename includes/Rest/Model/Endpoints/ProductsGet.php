<?php

namespace TdNewsletter\Rest\Model\Endpoints;
use TdNewsletter\Rest\Model\Endpoints\Endpoint;
use TdNewsletter\Woo\Woo;


class ProductsGet extends Endpoint
{
  private Woo $woo;
  public function __construct(Woo $woo)
  {
    parent::__construct('GET');
    $this->woo = $woo;
  }

  public function getPermissionCallback(): callable
  {
    return '__return_true';
  }
  public function getCallback(): callable
  {
    return function (\WP_REST_Request $request) {
      // return new \WP_REST_Response(['hi']);
      return $this->woo->getProducts($request);
    };
  }
}