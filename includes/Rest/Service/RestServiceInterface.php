<?php
declare(strict_types=1);
namespace TdNewsletter\Rest\Service;
use TdNewsletter\Rest\Model\Route\Route;

interface RestServiceInterface
{
  public function addRoute(Route $route): self;
  public function register() : void;
}