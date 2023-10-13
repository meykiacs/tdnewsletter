<?php

declare(strict_types=1);

namespace TdNewsletter\Table\Model;

class Newsletter extends Table {
  public function getName(): string {
    return 'newsletter';
  }

  public function getCreateSql(): string {
    $sql = "CREATE TABLE $this->table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      email varchar(255) NOT NULL,
      subscription_status varchar(50) NOT NULL,
      hashed_confirmation_code varchar(255) DEFAULT NULL,
      created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
      updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
      PRIMARY KEY  (id)
  ) $this->charset_collate;";

    return $sql;
  }
}
