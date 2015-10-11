<?php

namespace AppBundle\Service;

use Doctrine\DBAL\Connection;

class DefaultFillService
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function fill()
    {
        $this->connection->exec('TRUNCATE TABLE `s_names`');

        $this->connection->exec('
            CREATE TABLE `alphabet` (
              `letter` CHAR(1)
            ) ENGINE = MEMORY;
        ');

        $sql = '';
        foreach (range('a', 'z') as $letter) {
            $sql .= "INSERT INTO `alphabet`(`letter`) VALUE('$letter');";
        }

        $this->connection->exec($sql);

        $matrix = [$this->generate()];

        $selectData = join(' UNION ALL ', $matrix);

        $this->connection->exec("
            INSERT INTO `s_names` (`name`)
            $selectData;
        ");

        $this->connection->exec('DROP TABLE `alphabet`;');
    }

    private function generateMultiDimensional($value)
    {
        $aliases = [];
        $fields = [];

        while ($value--) {
            $alias = "a$value";
            $aliases[] = $alias;
            $fields[] = "`$alias`.`letter`";
        }

        $fromAlias = array_shift($aliases);
        $concat = join(',', $fields);
        $sql = "SELECT CONCAT($concat) FROM `alphabet` $fromAlias";
        foreach ($aliases as $alias) {
            $sql .= " JOIN `alphabet` $alias ";
        }

        return $sql;
    }

    private function generate()
    {
        return 'SELECT `letter` FROM `alphabet`';
    }
}