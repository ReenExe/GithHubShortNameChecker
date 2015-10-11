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

            CREATE TABLE `generator` (
              `name` CHAR(1)
            ) ENGINE = MyISAM;
        ');

        $sql = '';
        foreach (range('a', 'z') as $letter) {
            $sql .= "INSERT INTO `alphabet`(`letter`) VALUE('$letter');";
        }

        $this->connection->exec($sql);

        $this->connection->exec('
            INSERT INTO `generator` (`name`)
            SELECT `letter` FROM `alphabet`;
        ');

        $dimensional = 5;
        for ($i = 1; $i < $dimensional; ++$i) {
            $this->connection->exec('
                INSERT INTO `generator` (`name`)
                SELECT CONCAT(`name`, `letter`) FROM `alphabet`
                JOIN `generator`;
            ');
        }

        $this->connection->exec('
            INSERT INTO `s_names` (`name`)
            SELECT `name` FROM `generator`;
        ');

        $this->connection->exec('DROP TABLE `alphabet`;');
        $this->connection->exec('DROP TABLE `generator`;');
    }
}