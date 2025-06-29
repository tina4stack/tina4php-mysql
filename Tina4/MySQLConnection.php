<?php
/**
 * Tina4 - This is not a 4ramework.
 * Copy-right 2007 - current Tina4
 * License: MIT https://opensource.org/licenses/MIT
 */

namespace Tina4;

use MongoDB\BSON\PackedArray;
use mysqli_sql_exception;

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
    public function __construct(string $hostName, string $username, string $password, string $databaseName, int $port, string $charset = 'utf8mb4', string $certificateFile="mysql.crt")
    {
        try {

            if (file_exists($certificateFile))
            {
                $this->connection = mysqli_init();

                //if (DIRECTORY_SEPARATOR === "\\")
                //{
                    $this->connection->options(MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT, true);
                    $this->connection->ssl_set(null, null, $certificateFile, null, null);
                //}
                
                mysqli_real_connect($this->connection, $hostName, $username, $password, $databaseName, $port);
                $this->connection->set_charset($charset);

            } else {
                throw new mysqli_sql_exception('MySQL certificate file does not exist');
            }
        } catch (mysqli_sql_exception $e) {
            throw new \Exception (" {
                    Error }
                connecting to MySQL server: " . mysqli_connect_error());
        }
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