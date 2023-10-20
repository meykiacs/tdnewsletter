<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use TdNewsletter\CPTResource\Model\CPT;
use TdNewsletter\CPTResource\Model\CPTResource;
use TdNewsletter\CPTResource\Service\RegisterCPTResource;
use TdNewsletter\Models\Fields\SentField;
use TdNewsletter\Models\Meta\NewsletterMeta;
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

$tdn_containerBuilder = new ContainerBuilder();
$tdn_containerBuilder->addDefinitions([
  'prefix'  => 'tdn',
  'plugin.filepath' => __FILE__,
  'textDomain' => DI\value('tdnewsletter'),
  'rest.namespace' => DI\value('tdnewsletter/v1'),
  'endpoint.subscribe'  => DI\value('subscribe'),
  'endpoint.confirm'  => DI\value('confirm'),
  EmailValidator::class => DI\create(EmailValidator::class),
  EmailSanitizer::class => DI\create(EmailSanitizer::class),
  SubscribePost::class => DI\autowire()->constructorParameter('confirmEndpoint', DI\get('endpoint.confirm'))->constructorParameter('restNamespace', DI\get('rest.namespace'))->constructorParameter('textDomain', DI\get('textDomain')),
  'cpt.newsletter' => function (ContainerInterface $c) {
    $cpt = new CPT('newsletter', 'Newsletter');
    $cpt->metas[] = new NewsletterMeta();
    $cptResource = new CPTResource($cpt);
    // $cptResource->fields[] = $c->get(SentField::class);
    return $cptResource;
  },
]);

$tdn_container = $tdn_containerBuilder->build();


// register the table
$tdn_container->get(TableManager::class)->addTable(new Newsletter())
  ->register();

// create and register subscribe and confirm endpoints
$tdn_subscribePost = $tdn_container->make(SubscribePost::class);
$tdn_subscribeRoute = new Route($tdn_container->get('rest.namespace'), $tdn_container->get('endpoint.subscribe'));
$tdn_subscribeRoute->addEndpoint($tdn_subscribePost);
$tdn_confirm = $tdn_container->make(Confirm::class);
$tdn_confirmRoute = new Route($tdn_container->get('rest.namespace'), $tdn_container->get('endpoint.confirm'));
$tdn_confirmRoute->addEndpoint($tdn_confirm);
$tdn_container->get(Rest::class)->addRoute($tdn_subscribeRoute)->addRoute($tdn_confirmRoute)->register();

// register newsletter posttype
$tdn_container->get(RegisterCPTResource::class)->add($tdn_container->get('cpt.newsletter'))->register();

// register plugin hooks
$tdn_container->get(PluginHooks::class)->registerActivationHook()
  ->registerDeactivationHook();



function tdn_getResourceList(string $postType, int $maxNumber = -1) {

  $resourceList = [];
  $query = new \WP_Query(

    [
      'post_type' => $postType,
      'posts_per_page' => $maxNumber,
    ]
  );

  if ($query->have_posts()) {
    while ($query->have_posts()) {
      $query->the_post();
      $id = get_the_ID();
      $resource = array(
        'id'  =>  get_the_ID(),
        'type'  =>  get_post_type(get_the_ID()),
        'title' => get_the_title(),
        'content' => get_the_content(),
        'permalink' => get_permalink(),
        'featured_media_url' => get_the_post_thumbnail_url($id, 'medium'),
        'featured_media' => get_post_thumbnail_id($id),
        'meta'  =>  ['_tdn_newsletter' => get_post_meta($id, '_tdn_newsletter', true)]
      );
      array_push($resourceList, $resource);
    }
  }
  wp_reset_query();

  return $resourceList;
}

function tdn_render() {
  global $tdn_container;
  $prefix = 'tdn';
  $postTypeWithLang = 'tdn_newsletter';

  // Get the CPTResource instance
  $cptResource = $tdn_container->get('cpt.newsletter');

  $data = tdn_getResourceList($postTypeWithLang);

?>
  <pre style="display: none !important" id="newsletter">
  <?php echo wp_json_encode($data, JSON_HEX_TAG); ?>
  </pre>
  <div style="display: none !important" id="newsletter-data" data-fetched="1" data-rest-url="<?php echo esc_attr(get_rest_url(null, 'wp/v2/tdn_newsletter')); ?>">
  </div>
<?php
}
