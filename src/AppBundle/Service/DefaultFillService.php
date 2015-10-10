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

        $this->connection->exec('DROP TABLE `alphabet`;');
    }
}