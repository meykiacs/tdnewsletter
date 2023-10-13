<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use TdNewsletter\PluginHooks\PluginHooks;
use TdNewsletter\Rest\Model\Endpoints\Confirm;
use TdNewsletter\Rest\Model\Endpoints\SubscribePost;
use TdNewsletter\Rest\Model\Route\Route;
use TdNewsletter\Rest\Service\Rest;
use TdNewsletter\Table\Service\TableManager;
use TdNewsletter\Table\Model\Newsletter;

/**
 * Plugin Name: tdnewsletter
 * Description: A plugin for newsletter
 * Author:      meykiacs
 */

defined('ABSPATH') or exit;

require __DIR__ . '/vendor/autoload.php';

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions([
  'plugin.filepath' => __FILE__,
  'textDomain' => DI\value('tdnewsletter'),
  'rest.namespace' => DI\value('tdnewsletter/v1'),
  'endpoint.subscribe'  => DI\value('subscribe'),
  'endpoint.confirm'  => DI\value('confirm'),
  EmailValidator::class => DI\create(EmailValidator::class),
  EmailSanitizer::class => DI\create(EmailSanitizer::class),
  SubscribePost::class => DI\autowire()->constructorParameter('confirmEndpoint', DI\get('endpoint.confirm'))->constructorParameter('restNamespace', DI\get('rest.namespace'))->constructorParameter('textDomain', DI\get('textDomain')),
]);

$container = $containerBuilder->build();


$container->get(TableManager::class)->addTable(new Newsletter())
  ->register();

$subscribePost = $container->make(SubscribePost::class);
$subscribeRoute = new Route($container->get('rest.namespace'), $container->get('endpoint.subscribe'));
$subscribeRoute->addEndpoint($subscribePost);

$confirm = $container->make(Confirm::class);
$confirmRoute = new Route($container->get('rest.namespace'), $container->get('endpoint.confirm'));
$confirmRoute->addEndpoint($confirm);
$container->get(Rest::class)->addRoute($subscribeRoute)->addRoute($confirmRoute)->register();


$container->get(PluginHooks::class)->registerActivationHook()
  ->registerDeactivationHook();
