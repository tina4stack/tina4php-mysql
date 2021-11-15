<?php

namespace Tina4;

class MySQLQuery extends DataConnection implements DataBaseQuery
{
    public function query($sql, int $noOfRecords = 10, int $offSet = 0, array $fieldMapping = []): ?DataResult
    {
        // TODO: Implement query() method.
    }
}