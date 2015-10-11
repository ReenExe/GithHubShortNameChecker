<?php

namespace AppBundle\Service;

use Doctrine\DBAL\Connection;

class CheckNameService
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function progress($limit)
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://api.github.com'
        ]);

        $source = $this->connection->fetchAll("
            SELECT `id`, `name`
            FROM `s_names`
            WHERE `github_response_code` = 0
            LIMIT $limit;
        ");

        $names = array_column($source, 'name', 'id');

        $responseCodeIdMap = [];

        foreach ($names as $id => $name) {
            try {
                $responseCode = $client->get("/users/$name")->getStatusCode();
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                $responseCode = $e->getCode();
            }

            $responseCodeIdMap[$responseCode][] = $id;
        }

        foreach ($responseCodeIdMap as $code => $idList) {
            $idStringList = join(',', $idList);
            $this->connection->executeUpdate("
                UPDATE `s_names`
                SET `github_response_code` = $code
                WHERE `id` IN ($idStringList)
            ");
        }
    }
}