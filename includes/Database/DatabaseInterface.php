<?php
declare(strict_types=1);
namespace TdNewsletter\Database;


interface Database {
  public function prepare(string $query, ...$args);
  public function get_row($query);
  public function update($table, $data, $where);

}