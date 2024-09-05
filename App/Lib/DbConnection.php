<?php

namespace App\Lib;

class DbConnection
{
  private static $conn;

  public static function getConn(): \PDO
  {
    if (is_null(self::$conn)) {
      try {
        $host = $_ENV['DB_HOST'];
        $name = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $pass = $_ENV['DB_PASS'];

        $dns = 'mysql:host='.$host.';dbname='.$name.'';
        self::$conn = new \PDO($dns, $user, $pass);
      } catch (\PDOException $ex) {
        die('Houve um erro na conexão com o banco de dados');
      }
    }
    return self::$conn;
  }
}
