<?php
/**
 * Tina4 - This is not a 4ramework.
 * Copy-right 2007 - current Tina4
 * License: MIT https://opensource.org/licenses/MIT
 */

use PHPUnit\Framework\TestCase;

require_once "./Tina4/DataMySQL.php";

class DataMySQLTest extends TestCase
{
    public string $connectionString;
    public $DBA;

    public static function callMethod($obj, $name, array $args) {
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $args);
    }

    final public function setUp(): void
    {
        $this->connectionString = "127.0.0.1/33066:test";
        $this->DBA = new \Tina4\DataMySQL($this->connectionString, "root", "pass1234", "YYYY-mm-dd", "utf8mb4", "./docker/certs/ca.crt" );
    }

    final public function testParamTypes(): void
    {
        $mysqlExec = new \Tina4\MySQLExec($this->DBA);
        $string = "0836464535";
        $this->assertEquals("s", $this->callMethod( $mysqlExec, "getParamType", [$string]), "Broken string param type");

        $string = 836464535;
        $this->assertEquals("i", $this->callMethod( $mysqlExec, "getParamType", [$string]), "Broken string param type");

        $string = 80.20;
        $this->assertEquals("d", $this->callMethod( $mysqlExec, "getParamType", [$string]), "Broken string param type");

    }

    final public function testOpen(): void
    {
        $this->assertNotEmpty($this->DBA);
    }

    final public function testTableExists() : void
    {
        $exists = $this->DBA->tableExists("user");
        $this->assertIsBool($exists, "Not working");
        $exists = $this->DBA->tableExists("user_one");
        $this->assertEquals(false, $exists, "Not working false table check");
    }

    final public function testDropCreateTable() : void
    {
        if ($this->DBA->tableExists("sub_testing")) {
            $error = $this->DBA->exec("drop table sub_testing");
        }

        if ($this->DBA->tableExists("testing")) {
            $error = $this->DBA->exec("drop table testing");
        }

        $this->DBA->commit();

        $error = $this->DBA->exec("create table testing(id integer default 0, name varchar(200) default 'Name', age integer default 22, salary numeric (10,2), my_date timestamp default null, my_date_2 timestamp default null, my_date_3 date default null, primary key(id))");

        $this->DBA->commit();

        $exists = $this->DBA->tableExists("testing");

        $error = $this->DBA->exec("create table sub_testing (id integer default 1 not null, testing_id integer default 0 not null references testing(id) on delete cascade, primary key(id))");
        $this->DBA->commit();

        $this->assertEquals(true, $exists, "Not working false table check");
    }



    final public function testRead(): void
    {
        $this->DBA->exec("insert into testing (id) values (?)", 1);

        $this->DBA->exec("insert into testing (id) values (2)");

        $records = $this->DBA->fetch("select * from testing")->asArray();

        $this->assertCount(2, $records, "Records were not 2");

    }

    final public function testGetDatabase(): void
    {
        $database = $this->DBA->getDatabase();
        $this->assertArrayHasKey("testing", $database);
        $this->assertArrayHasKey("sub_testing", $database);
    }


}
