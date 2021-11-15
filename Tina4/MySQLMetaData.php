<?php
/**
 * Tina4 - This is not a 4ramework.
 * Copy-right 2007 - current Tina4
 * License: MIT https://opensource.org/licenses/MIT
 */

namespace Tina4;

/**
 * Gets the Metadata for a MySQL database
 */
class MySQLMetaData extends DataConnection implements DataBaseMetaData
{
    /**
     * Gets all the tables for a database schema
     * @return array
     */
    final public function getTables(): array
    {
        $sqlTables = "SELECT table_name
                      FROM INFORMATION_SCHEMA.tables
                     WHERE upper(table_schema) = upper('{$this->getConnection()->databaseName}')
                     ORDER BY table_type ASC, table_name DESC";

        return $this->getConnection()->fetch($sqlTables, 10000, 0)->asObject();
    }

    /**
     * Get the primary keys for a table
     * @param string $tableName
     * @return array
     */
    final public function getPrimaryKeys(string $tableName): array
    {
        return [];
    }

    /**
     * Gets the foreign keys for a table
     * @param string $tableName
     * @return array
     */
    final public function getForeignKeys(string $tableName): array
    {
        return $this->getConnection()->fetch("SELECT 
                                  COLUMN_NAME as field_name,
                            FROM  INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                           WHERE  REFERENCED_TABLE_SCHEMA = '".$this->getConnection()->databaseName."' 
                             AND TABLE_NAME = '$tableName'")->asObject();

    }

    /**
     * Get information for a table
     * @param string $tableName
     * @return array
     */
    final public function getTableInformation(string $tableName): array
    {
        $tableInformation = [];

        $sqlInfo = "SELECT *
                      FROM information_schema.COLUMNS   
                     WHERE upper(table_schema) = upper('{$this->getConnection()->databaseName}')
                       AND TABLE_NAME = '{$tableName}'
                  ORDER BY ORDINAL_POSITION";

        $columns = $this->getConnection()->fetch($sqlInfo, 10000)->asObject();

        $foreignKeys = $this->getForeignKeys($tableName);
        $foreignKeyLookup = [];
        foreach ($foreignKeys as $foreignKey) {
            $foreignKeyLookup[$foreignKey->fieldName] = true;
        }

        foreach ($columns as $columnIndex => $columnData) {
            $fieldData = new \Tina4\DataField(
                $columnIndex,
                trim($columnData->columnName),
                trim($columnData->columnName),
                trim($columnData->dataType),
                (int)trim($columnData->characterMaximumLength),
                (int)trim($columnData->numericPrecision)
            );

            $fieldData->description = trim($columnData->extra);

            $fieldData->isNotNull = false;
            if ($columnData->isNullable === 1) {
                $fieldData->isNotNull = true;
            }

            $fieldData->isPrimaryKey = false;
            if ($columnData->columnKey === "PRI") {
                $fieldData->isPrimaryKey = true;
            }

            $fieldData->isForeignKey = false;
            if (isset($foreignKeyLookup[$fieldData->fieldName])) {
                $fieldData->isForeignKey = true;
            }

            $fieldData->defaultValue = $columnData->columnDefault;
            $tableInformation[] = $fieldData;
        }

        return $tableInformation;
    }

    /**
     * Gets the complete database metadata
     * @return array
     */
    final public function getDatabaseMetaData(): array
    {
        $database = [];
        $tables = $this->getTables();

        foreach ($tables as $record) {
            $tableInfo = $this->getTableInformation($record->tableName);

            $database[strtolower($record->tableName)] = $tableInfo;
        }

        return $database;
    }
}