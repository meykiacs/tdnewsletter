<?php

declare(strict_types=1);

namespace TdNewsletter\Models\Meta;

use TdNewsletter\CPTResource\Model\Meta;

class NewsletterMeta extends Meta {

  public string $slug = 'newsletter';
  public string $type = 'object';


  public array $schema = [
    'type'  => 'object',
    'properties'  => [
      'sent'  =>  [
        'type'  =>  'boolean',
        'type'  =>  'required',
      ],
    ]
  ];
}
