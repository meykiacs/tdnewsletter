<?php

namespace TdNewsletter\Rest\Model\Endpoints;

use DI\Container;
use TdNewsletter\Entity\Model\Newsletter;
use TdNewsletter\Entity\Service\EntityManager;
use TdNewsletter\Rest\Model\Endpoints\Endpoint;
use TdNewsletter\Rest\Model\Fields\Field;
use TdNewsletter\Sanitize\EmailSanitizer;
use TdNewsletter\Validate\EmailValidator;

class SubscribePost extends Endpoint {
  private EntityManager $em;
  public function __construct(EntityManager $em, Container $container) {
    parent::__construct('POST');
    $this->em = $em;
    $this->addField(new Field('email', true, 'string', $container->get(EmailValidator::class), $container->get(EmailSanitizer::class)));
  }

  public function getPermissionCallback(): callable {
    return '__return_true';
  }
  public function getCallback(): callable {
    /**
     * @return \WP_REST_Response|\WP_Error
     */
    return function (\WP_REST_Request $request) {
      $email = $request->get_param('email');
      $row = $this->em->getBy(Newsletter::class, 'email', $email);
      if ($row) {
        return new \WP_REST_Response(array('message' => 'Email already exists'), 409);
      } else {
        $newsletter = new Newsletter();
        $newsletter->email = $email;
        $newsletter->subscription_status = 'inactive';
        $newsletter->setCodes();
        $id = $this->em->save($newsletter);
        return new \WP_REST_Response(['id' => $id], 200);
      }
    };
  }
}
