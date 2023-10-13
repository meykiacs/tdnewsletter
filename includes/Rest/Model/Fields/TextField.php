<?php
declare(strict_types=1);
namespace TdNewsletter\Rest\Model\Fields;

class TextField extends Field
{

  public function __construct($name, $isRequired)
  {
    $this->type = 'string';
    $this->name = $name;
    $this->isRequired = $isRequired;
  }

  public function getValidateCallback(): callable
  {
    return $this->validate->textField();
  }
  public function getSanitizeCallback(): callable
  {
    return $this->sanitize->textField();
  }

}