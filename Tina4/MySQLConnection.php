<?php
/**
 * Tina4 - This is not a 4ramework.
 * Copy-right 2007 - current Tina4
 * License: MIT https://opensource.org/licenses/MIT
 */

namespace Tina4;

/**
 * Mysql connection
 */
class MySQLConnection
{
    /**
     * Database connection
     * @var false|resource
     */
    private $connection;

    /**
     * Creates a MySQL Database Connection
     * @param string $hostName Hostname where server is located
     * @param string $username database username
     * @param string $password password of the user
     * @param string $databaseName name of the database
     * @param int $port port
     */
    public function __construct(string $hostName, string $username, string $password, string $databaseName, int $port)
    {
        $this->connection = mysqli_connect($hostName, $username, $password, $databaseName, $port);
        $this->connection->set_charset("utf8mb4");
    }

    /**
     * Returns a database connection or false if failed
     * @return false|resource
     */
    final public function getConnection()
    {
        return $this->connection;
    }
}