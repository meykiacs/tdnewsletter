<?php
declare(strict_types=1);

namespace TdNewsletter\Table\Model;

abstract class Table
{

  protected string $table_name;
  protected string $charset_collate;
  protected \wpdb $wpdb;

  public function __construct()
  {
    global $wpdb;
    $this->wpdb = $wpdb;
    $this->table_name = $wpdb->prefix . $this->getName();
    $this->charset_collate = $wpdb->get_charset_collate();
  }

  abstract public function getName(): string;
  abstract public function getCreateSql(): string;

  public function getDropSql(): string
  {
    return "DROP TABLE IF EXISTS $this->table_name";
  }
}