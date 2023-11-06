<?php

declare(strict_types=1);

namespace TdNewsletter\Rest\Model\Endpoints;

use TdNewsletter\Entity\Model\Newsletter;
use TdNewsletter\Entity\Service\EntityManager;
use TdNewsletter\Rest\Model\Endpoints\Endpoint;
use TdNewsletter\Rest\Model\Fields\Field;
use TdNewsletter\Sanitize\EmailSanitizer;
use TdNewsletter\Validate\EmailValidator;

class SubscribePost extends Endpoint
{
  private EntityManager $em;
  private string $confirmEndpoint;
  private string $textDomain;
  private string $restNamespace;
  private EmailValidator $emailValidator;
  private EmailSanitizer $emailSanitizer;

  public function __construct(EntityManager $em, string $confirmEndpoint, string $restNamespace, EmailValidator $emailValidator, EmailSanitizer $emailSanitizer, $textDomain)
  {
    parent::__construct('POST');
    $this->em = $em;
    $this->confirmEndpoint = $confirmEndpoint;
    $this->restNamespace = $restNamespace;
    $this->textDomain = $textDomain;
    $this->emailValidator = $emailValidator;
    $this->emailSanitizer = $emailSanitizer;
    $this->addField(new Field('email', true, 'string', $this->emailValidator, $this->emailSanitizer));
  }

  public function getPermissionCallback(): callable
  {
    return '__return_true';
  }
  public function getCallback(): callable
  {
    /**
     * @return \WP_REST_Response|\WP_Error
     */
    return function (\WP_REST_Request $request) {
      $email = $request->get_param('email');
      $newsletter = $this->em->getBy(Newsletter::class, 'email', $email);
      if ($newsletter && $newsletter->subscription_status === 'active') {
        return new \WP_REST_Response(array('message' => 'Email already exists'), 409);
      } else {
        $newsletter = new Newsletter();
        $newsletter->email = $email;
        $newsletter->subscription_status = 'inactive';
        $code = $newsletter->setCode();
        $id = $this->em->save($newsletter);
        $confirmation_url = get_rest_url(null, $this->restNamespace . '/' . $this->confirmEndpoint);
        $confirmation_url .= "?id=" . urlencode_deep($id) . "&code=" . urlencode_deep($code);
        $message = esc_html__("Please click the following link to confirm your email address: ", $this->textDomain) . $confirmation_url;
        $to = $email;
        $subject = esc_html__("Confirm your email", $this->textDomain);
        $headers = 'From: Me <me@' . $_SERVER['SERVER_NAME'] . '>' . "\r\n";
        wp_mail($to, $subject, $message, $headers);
        return new \WP_REST_Response(['id' => $id,], 200);
      }
    };
  }
}
