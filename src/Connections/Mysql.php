<?php

namespace Src\Connections;

use PDO;
use PDOException;

/**
 * Class Mysql
 * @package Src\Connections
 */
class Mysql implements ConnectionInterface
{
    /** @var \PDO $mysql_connection */
    private $mysql_connection;

    /**
     * Mysql constructor.
     */
    public function __construct()
    {
        $dsn = "mysql:dbname=".$_ENV['DB_NAME'].";host=".$_ENV['DB_HOST'];
        $user = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASSWORD'];
        try {
            $this->mysql_connection = new PDO($dsn, $user, $password);
            $this->mysql_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Connexion échouée : ' . $e->getMessage();
        }
    }

    /**
     * Get PDO instance
     *
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->mysql_connection;
    }

    /**
     * Get las inserted id
     *
     * @return int
     */
    public function getLastInsertId(): int
    {
        return (int) $this->mysql_connection->lastInsertId();
    }
}
