<?php
/**
 * Tina4 - This is not a 4ramework.
 * Copy-right 2007 - current Tina4
 * License: MIT https://opensource.org/licenses/MIT
 */

namespace Tina4;

/**
 * Mysql exec functionality
 */
class MySQLExec extends DataConnection implements DataBaseExec
{
    use DataUtility;

    /**
     * Execute an SQL statement
     * @param $params
     * @param $tranId
     * @return DataError
     */
    final public function exec($params, $tranId): DataError
    {
        $preparedQuery = $this->getDbh()->prepare($params[0]);
        $executeError = $this->getConnection()->error();

        if (!empty($preparedQuery)) {
            unset($params[0]);
            if (!empty($params)) {
                $paramTypes = "";
                foreach ($params as $pid => $param) {
                    $paramTypes .= $this->getParamType($param);
                }

                //Fix for reference values https://stackoverflow.com/questions/16120822/mysqli-bind-param-expected-to-be-a-reference-value-given
                \mysqli_stmt_bind_param($preparedQuery, $paramTypes, ...$params);
                \mysqli_stmt_execute($preparedQuery);
                $executeError = $this->getConnection()->error(); //We need the error here!

                \mysqli_stmt_affected_rows($preparedQuery);
                \mysqli_stmt_close($preparedQuery);
            } else { //Execute a statement without params
                $params[0] = $preparedQuery;
                mysqli_stmt_execute(...$params);
                $executeError = $this->getConnection()->error(); //We need the error here!
            }
        }

        return $executeError;
    }

    /**
     * Gets the param type
     * @param $param
     * @return string
     * @tests
     *   assert('072654332111' 's')
     */
    private function getParamType($param): string
    {
        $paramType = "s";

        if (!$this->isBinary($param)) {

            if (is_int($param)) {
                $paramType = "i";
            } elseif (is_array($param)) {
                if (array_key_exists(0, $param)) {
                    $paramType = (($param[0] !== "0") ? "d" : "s");
                }
            } elseif ($param !== '' && is_numeric($param) && !empty($param)) {
                if (!is_string($param)  ) {
                    $paramType = "d";
                }
            }
        }

        return $paramType;
    }

}