<?php
declare(strict_types=1);

namespace TdNewsletter\Entity\Model;

class Newsletter extends Entity
{
  public string $hashed_confirmation_code;
  public string $email;
  public string $subscription_status;
  public string $created_at;
  public string $updated_at;
  private string $unhashed_code;

  public function setCodes(): void
  {
    $codePack = $this->generateCode();
    $this->unhashed_code = $codePack->unhashedCode;
    $this->hashed_confirmation_code = $codePack->hashedCode;
  }
  private function generateCode(): \stdClass
  {
    $obj = new \stdClass();
    $obj->unhashedCode = wp_generate_password(32, false);
    $obj->hashedCode = wp_hash_password($obj->code);
    return $obj;
  }
}
