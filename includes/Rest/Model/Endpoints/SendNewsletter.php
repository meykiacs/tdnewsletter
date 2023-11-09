<?php

declare(strict_types=1);

namespace TdNewsletter\Rest\Model\Endpoints;

use TdNewsletter\Entity\Model\Newsletter;
use TdNewsletter\Entity\Service\EntityManager;
use TdNewsletter\Rest\Model\Endpoints\Endpoint;


class SendNewsletter extends Endpoint
{
  private EntityManager $em;

  public function __construct(
    EntityManager $em
  ) {
    parent::__construct('POST');
    $this->em = $em;

  }

  public function getPermissionCallback(): callable
  {
    return function () {
      return current_user_can('edit_posts');
    };
  }

  public function getCallback(): callable
  {
    /**
     * @return \WP_REST_Response|\WP_Error
     */
    return function (\WP_REST_Request $request) {
      $post_id = $request->get_param('post_id');
      $sent = get_post_meta($post_id, '_tdn_newsletter', true);
      if (!$sent) {
        $entities = $this->em->getAllBy(Newsletter::class, 'subscription_status', 'active');
        $emails = [];
        foreach ($entities as $entity) {
          $emails[] = $entity->email;
        }
        $post = get_post($post_id);
        $title = $post->post_title;
        $content = $post->post_content;
        // var_dump($emails);wp_die();
        foreach ($emails as $email) {
          $result = wp_mail($email, $title, $content);

        }
        update_post_meta($post_id, '_tdn_newsletter', true);
      }
      return new \WP_REST_Response(['success' => true]);
    };
  }
}
