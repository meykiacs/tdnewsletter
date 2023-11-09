<?php

declare(strict_types=1);

namespace TdNewsletter\Models\Meta;

use TdNewsletter\CPTResource\Model\Meta;
use TdNewsletter\Entity\Model\Newsletter;
use TdNewsletter\Entity\Service\EntityManager;

class NewsletterMeta extends Meta
{

  public string $slug = 'newsletter';
  public string $type = 'boolean';
  private EntityManager $em;

  public function __construct(EntityManager $em)
  {
    $this->em = $em;
  }

  public $schema = false;

}
