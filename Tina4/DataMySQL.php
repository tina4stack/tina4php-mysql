<?php
/**
 * Tina4 - This is not a 4ramework.
 * Copy-right 2007 - current Tina4
 * License: MIT https://opensource.org/licenses/MIT
 */

namespace Tina4;

/**
 * The MySQL database database implementation
 * @package Tina4
 */
class DataMySQL implements DataBase
{
    use DataBaseCore;

    /**
     * @var null database metadata
     */
    private $databaseMetaData;

    /**
     * Opens database connection
     * @return bool|void
     * @throws \Exception
     */
    public function open()
    {
        if (!function_exists("mysqli_connect")) {
            throw new \Exception("Mysql extension for PHP needs to be installed");
        }

        $this->dbh = (new MySQLConnection(
            $this->hostName,
            $this->username,
            $this->password,
            $this->databaseName,
            $this->port
        ))->getConnection();

    }

    /**
     * Closes database connection
     * @return bool|void
     */
    final public function close()
    {
        mysqli_close($this->dbh);
    }

    /**
     * Executes
     * @return DataError|DataResult
     */
    final public function exec()
    {
        $params = $this->parseParams(func_get_args());
        $params = $params["params"];

        if (stripos($params[0], "call") !== false) {
            return $this->fetch($params[0]);
        }

        return (new MySQLExec($this))->exec($params, null);
    }

    /**
     * Fetches records from database
     * @param string $sql SQL Query
     * @param integer $noOfRecords Number of records requested
     * @param integer $offSet Record offset
     * @param array $fieldMapping Mapped Fields
     * @return null|DataResult
     */
    final public function fetch($sql = "", int $noOfRecords = 10, int $offSet = 0, array $fieldMapping = []): ?DataResult
    {
        return (new MySQLQuery($this))->query($sql, $noOfRecords, $offSet, $fieldMapping);
    }

    /**
     * Gets MySQL errors
     * @return DataError
     */
    final public function error() : DataError
    {
        $errorNo = mysqli_errno($this->dbh);
        $errorMessage = mysqli_error($this->dbh);

        return (new DataError($errorNo, $errorMessage));
    }

    /**
     * Gets the default database date format
     * @return string
     */
    final public function getDefaultDatabaseDateFormat(): string
    {
        return "Y-m-d";
    }

    /**
     * Gets the last inserted row's ID from database
     * @return string
     */
    final public function getLastId(): string
    {
        $lastId = $this->fetch("SELECT LAST_INSERT_ID() as last_id");
        return $lastId->records(0)[0]->lastId;
    }

    /**
     * Check if the table exists
     * @param string $tableName
     * @return bool
     */
    final public function tableExists(string $tableName): bool
    {
        if (!empty($tableName)) {
            $exists = $this->fetch("SELECT * 
                                    FROM information_schema.tables
                                    WHERE table_schema = '{$this->databaseName}' 
                                        AND table_name = '{$tableName}'", 1);
            return !empty($exists->records());
        } else {
            return false;
        }
    }

    /**
     * Commits
     * @param null $transactionId
     * @return bool
     */
    final public function commit($transactionId = null)
    {
        return mysqli_commit($this->dbh);
    }

    /**
     * Rollback the transaction
     * @param null $transactionId
     * @return bool|mixed
     */
    final public function rollback($transactionId = null)
    {
        return mysqli_rollback($this->dbh);
    }

    /**
     * Start the transaction
     * @return string
     */
    final public function startTransaction(): string
    {
        $this->dbh->autocommit(false);
        mysqli_begin_transaction($this->dbh);
        return "Resource id #0";
    }

    /**
     * Auto commit on for mysql
     * @param bool $onState
     * @return void
     */
    final public function autoCommit(bool $onState = true): void
    {
        $this->dbh->autocommit($onState);
    }

    /**
     * Gets the database metadata
     * @return array|mixed
     */
    final public function getDatabase(): array
    {
        if (!empty($this->databaseMetaData)) {
            return $this->databaseMetaData;
        }

        $this->databaseMetaData = (new MySQLMetaData($this))->getDatabaseMetaData();

        return $this->databaseMetaData;
    }

    /**
     * The default MySQL port
     * @return int
     */
    final public function getDefaultDatabasePort(): int
    {
        return 3306;
    }

    /**
     * Is it a No SQL database?
     * @return bool
     */
    final public function isNoSQL(): bool
    {
        return false;
    }

    /**
     * Get short name for the database migrations
     * @return string
     */
    public function getShortName(): string
    {
        return "mysql";
    }
}
