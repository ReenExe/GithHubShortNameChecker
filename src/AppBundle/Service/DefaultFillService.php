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

        $this->connection->exec('
            INSERT INTO `s_names` (`name`)
            SELECT `letter` FROM `alphabet`;
        ');

        $this->connection->exec('DROP TABLE `alphabet`;');
    }
}