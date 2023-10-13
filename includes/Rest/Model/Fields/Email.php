<?php
declare(strict_types=1);
namespace TdNewsletter\Rest\Model\Fields;

class Email extends Field
{
  
  public function __construct(string $name, bool $isRequired, ) {
    $this->name = $name;
    $this->isRequired = $isRequired;
    $this->type = 'string';

  }
  public string $name = 'email';
  public string $type = 'string';
  public string $isRequied = 'string';


  public function getValidateCallback(): callable
  {
    return $this->validate->email();
  }
  public function getSanitizeCallback(): callable
  {
    return $this->sanitize->email();
  }

}