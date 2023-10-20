<?php

declare(strict_types=1);

namespace TdNewsletter\Rest\Model\Endpoints;

use DI\Container;
use TdNewsletter\Entity\Model\Newsletter;
use TdNewsletter\Entity\Service\EntityManager;
use TdNewsletter\Rest\Model\Endpoints\Endpoint;
use TdNewsletter\Rest\Model\Fields\Field;
use TdNewsletter\Sanitize\EmailSanitizer;
use TdNewsletter\Validate\EmailValidator;

class SendPost extends Endpoint {
  private EntityManager $em;
  private string $confirmEndpoint;
  private string $textDomain;
  private string $restNamespace;
  private EmailValidator $emailValidator;
  private EmailSanitizer $emailSanitizer;

  public function __construct(EntityManager $em, string $confirmEndpoint, string $restNamespace, EmailValidator $emailValidator, EmailSanitizer $emailSanitizer, $textDomain) {
    parent::__construct('POST');
    $this->em = $em;
    $this->confirmEndpoint = $confirmEndpoint;
    $this->restNamespace = $restNamespace;
    $this->textDomain = $textDomain;
    $this->emailValidator = $emailValidator;
    $this->emailSanitizer = $emailSanitizer;
    $this->addField(new Field('email', true, 'string', $this->emailValidator, $this->emailSanitizer));
  }

  public function getPermissionCallback(): callable {
    return '__return_true';
  }
  public function getCallback(): callable {
    /**
     * @return \WP_REST_Response|\WP_Error
     */
    return function (\WP_REST_Request $request) {
      $newsletter_id = $request['id'];
      $newsletter_post = get_post($newsletter_id);

      if ($newsletter_post->post_type != 'tdn_newsletter') {
        return new \WP_Error('not_newsletter', 'Post is not a newsletter.', array('status' => 404));

        $entities = $this->em->getAllBy(Newsletter::class, 'subscription_status', 'active');
        $emails = [];
        foreach($entities as $entity) {
          $email[] = $entity->email;
        }
        // foreach ($emails as $email) {
          // wp_mail($email->email, $newsletter_post->post_title, $newsletter_post->post_content);
      // }

      update_post_meta($newsletter_id, 'sent', true);
      return new \WP_REST_Response('Newsletter sent successfully!', 200);
      }
    };
  }
}
