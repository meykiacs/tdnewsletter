<?php

declare(strict_types=1);

namespace TdNewsletter\Entity\Model;

class Newsletter extends Entity {
  public string $confirmation_code;
  public string $email;
  public string $subscription_status;
  public string $created_at;
  public string $updated_at;

  public function setCode(): string {
    $this->confirmation_code = $this->generateCode();
    return $this->confirmation_code;
  }
  
  private function generateCode(): string {
    return wp_generate_password(32, false);
  }
}
