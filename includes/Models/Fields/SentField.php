<?php

declare(strict_types=1);

namespace TdNewsletter\Models\Fields;

use TdNewsletter\CPTResource\Model\Field;
use TdNewsletter\Entity\Model\Newsletter;
use TdNewsletter\Entity\Service\EntityManager;

class SentField extends Field {
  public string $slug = 'sent';
  public string $type = 'boolean';
  public string $description = 'Sent status of the newsletter.';
  private EntityManager $em;

  public function __construct(EntityManager $em) {
    $this->em = $em;
  }

  public function getCallback(): ?callable {
    return function (array $post_arr) {
      return get_post_meta($post_arr['id'], 'sent', true);
    };
  }

  public function getUpdateCallback(): ?callable {
    return function ($value, \WP_Post $post) {
      if ($value) {
        $entities = $this->em->getAllBy(Newsletter::class, 'subscription_status', 'active');
        $emails = [];
        foreach ($entities as $entity) {
          $email[] = $entity->email;
        }
        foreach ($emails as $email) {
          $sent = wp_mail($email->email, 'Newsletter', 'Your newsletter content');
        }
      }
      return update_post_meta($post->ID, 'sent', $value);
    };
  }
  public function getSanitizeCallback(): ?callable {
    return null;
  }
  public function getValidateCallback(): ?callable {
    return null;
  }
}
