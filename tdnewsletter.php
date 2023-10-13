<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use TdNewsletter\PluginHooks\PluginHooks;
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
  'rest.namespace' => 'tdnewsletter/v1'
]);

$container = $containerBuilder->build();


$container->get(TableManager::class)->addTable(new Newsletter())
  ->register();

$subscribePost = $container->make(SubscribePost::class);
$subscribeRoute = new Route($container->get('rest.namespace'), 'subscribe');
$subscribeRoute->addEndpoint($subscribePost);
$container->get(Rest::class)->addRoute($subscribeRoute)->register();
// $brandTaxonomy = new Taxonomy('brand', 'product');
// $brandTaxonomy->setSemiAutoLabels();
// $container->get(TaxonomyController::class)->addTaxonomy($brandTaxonomy)->register()->addMetaBox($brandTaxonomy);
  

$container->get(PluginHooks::class)->registerActivationHook()
  ->registerDeactivationHook();
