<?php

namespace TdNewsletter\Rest\Model\Endpoints;

use DI\Container;
use TdNewsletter\Entity\Model\Newsletter;
use TdNewsletter\Entity\Service\EntityManager;
use TdNewsletter\Rest\Model\Endpoints\Endpoint;
use TdNewsletter\Rest\Model\Fields\Field;
use TdNewsletter\Sanitize\EmailSanitizer;
use TdNewsletter\Validate\EmailValidator;

class Confirm extends Endpoint {
  private EntityManager $em;
  public function __construct(EntityManager $em, Container $container) {
    parent::__construct('GET');
    $this->em = $em;
  }

  public function getPermissionCallback(): callable {
    return '__return_true';
  }
  public function getCallback(): callable {
    /**
     * @return \WP_REST_Response|\WP_Error
     */
    return function (\WP_REST_Request $request) {
      $code = $request->get_query_params()['code'];
      $id = sanitize_text_field($request->get_query_params()['id']);
      $newsletter = $this->em->getById(Newsletter::class, $id);
      if (!$newsletter) {
        return new \WP_Error(404, 'Newsletter not found');
      }

      // wp_die();
      if ($newsletter && $newsletter->subscription_status === 'active') {
        return new \WP_REST_Response(array('message' => 'Email already confirmed'), 200);
      } elseif ($newsletter && $code ===  $newsletter->confirmation_code) {
        $newsletter->subscription_status = 'active';
        $this->em->save($newsletter);
        return new \WP_REST_Response(['message' => 'Subscription is successful'], 200);
      } else {
        return new \WP_Error(400, 'Invalid confirmation code');
      }
    };
  }
}
