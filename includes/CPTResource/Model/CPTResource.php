<?php

declare(strict_types=1);

namespace TdNewsletter\CPTResource\Model;

class CPTResource {
  public CPT $cpt;

  public function __construct(CPT $cpt) {
    $this->cpt = $cpt;
  }

  /**
   * @var Field[]
   */
  public array $fields = [];
}
