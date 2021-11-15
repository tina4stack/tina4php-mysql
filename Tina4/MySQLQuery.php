<?php
/**
 * Tina4 - This is not a 4ramework.
 * Copy-right 2007 - current Tina4
 * License: MIT https://opensource.org/licenses/MIT
 */

namespace Tina4;

/**
 * Mysql query
 */
class MySQLQuery extends DataConnection implements DataBaseQuery
{
    /**
     * Runs an MySQL query
     * @param $sql
     * @param int $noOfRecords
     * @param int $offSet
     * @param array $fieldMapping
     * @return DataResult|null
     */
    public function query($sql, int $noOfRecords = 10, int $offSet = 0, array $fieldMapping = []): ?DataResult
    {
        $initialSQL = $sql;

        //Don't add a limit if there is a limit already or if there is a stored procedure call
        if (stripos($sql, "limit") === false && stripos($sql, "call") === false) {
            $sql .= " limit {$offSet},{$noOfRecords}";
        }

        $recordCursor = \mysqli_query($this->getDbh(), $sql);
        $error = $this->getConnection()->error();

        $records = null;
        $fields = null;
        $resultCount = [];
        $resultCount["COUNT_RECORDS"] = 1;

        if ($error->getError()["errorCode"] === 0) {
            if (isset($recordCursor, $recordCursor->num_rows) && !empty($recordCursor) && $recordCursor->num_rows > 0) {
                while ($record = mysqli_fetch_assoc($recordCursor)) {
                    if (is_array($record)) {
                        $records[] = (new DataRecord($record,
                            $fieldMapping,
                            $this->getConnection()->getDefaultDatabaseDateFormat(),
                            $this->getConnection()->dateFormat)
                        );
                    }
                }

                if (is_array($records) && count($records) > 0 && stripos($sql, "returning") === false) {
                    //Check to prevent second call of procedure
                    if (stripos($sql, "call") !== false) {
                        $resultCount["COUNT_RECORDS"] = count($records);
                    } else {
                        $sqlCount = "select count(*) as COUNT_RECORDS from ($initialSQL) tcount";

                        $recordCount = mysqli_query($this->getDbh(), $sqlCount);

                        $resultCount = mysqli_fetch_assoc($recordCount);

                        if (empty($resultCount)) {
                            $resultCount["COUNT_RECORDS"] = 0;
                        }
                    }
                } else {
                    $resultCount["COUNT_RECORDS"] = 0;
                }
            } else {
                $resultCount["COUNT_RECORDS"] = 0;
            }

            //populate the fields
            $fid = 0;
            $fields = [];
            if (!empty($records)) {
                //$record = $records[0];
                $fields = mysqli_fetch_fields($recordCursor);

                foreach ($fields as $fieldId => $fieldInfo) {
                    $fieldInfo = (array)json_decode(json_encode($fieldInfo));

                    $fields[] = (new DataField($fid, $fieldInfo["name"], $fieldInfo["orgname"], $fieldInfo["type"], $fieldInfo["length"]));
                    $fid++;
                }
            }
        } else {
            $resultCount["COUNT_RECORDS"] = 0;
        }

        //Ensures the pointer is at the end in order to close the connection - Might be a buggy fix
        if (stripos($sql, "call") !== false) {
            while (mysqli_next_result($this->getDbh())) {
            }
        }

        return (new DataResult($records, $fields, $resultCount["COUNT_RECORDS"], $offSet, $error));

    }
}