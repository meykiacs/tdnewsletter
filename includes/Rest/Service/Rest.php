<?php
declare(strict_types=1);
namespace TdNewsletter\Rest\Service;

use TdNewsletter\Rest\Model\Route\Route;


class Rest implements RestServiceInterface
{

  /**
   * Summary of routes
   * @var Route[]
   */
  private array $routes;

  public function addRoute(Route $route): self
  {
    $this->routes[] = $route;
    return $this;
  }

  public function register(): void
  {
    add_action(
      'rest_api_init',
      function () {

        foreach ($this->routes as $route) {
          $args = [];
          foreach ($route->getEndpoints() as $endpoint) {
            $args[] = [
              'methods' => $endpoint->getMethod(),
              'callback' => $endpoint->getCallback(),
              'permission_callback' => $endpoint->getPermissionCallback(),
              'args' => $endpoint->asArgs()
            ];
          }

          register_rest_route(
            $route->getNamespace(),
            '/' . $route->getName(),
            $args
          );
        }
      }
    );

  }

}