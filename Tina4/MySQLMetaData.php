<?php

namespace Tina4;

class MySQLMetaData extends DataConnection implements DataBaseMetaData
{
    public function getTables(): array
    {
        // TODO: Implement getTables() method.
    }

    public function getPrimaryKeys(string $tableName): array
    {
        // TODO: Implement getPrimaryKeys() method.
    }

    public function getForeignKeys(string $tableName): array
    {
        // TODO: Implement getForeignKeys() method.
    }

    public function getTableInformation(string $tableName): array
    {
        // TODO: Implement getTableInformation() method.
    }
}