<?php

namespace AppBundle\Service;

use Doctrine\DBAL\Connection;

class CheckNameService
{
    private $connection;

    private $authToken;

    public function __construct(Connection $connection, $authToken)
    {
        $this->connection = $connection;

        $this->authToken = $authToken;
    }

    public function progress($limit)
    {
        $source = $this->connection->fetchAll("
            SELECT `id`, `name`
            FROM `s_names`
            WHERE `github_response_code` = 0
            LIMIT $limit;
        ");

        $names = array_column($source, 'name', 'id');

        $responseCodeIdMap = [];

        /* @var $api \Github\Api\User */
        $client = new \Github\Client();
        $client->authenticate($this->authToken, \Github\Client::AUTH_HTTP_PASSWORD);
        $api = $client->api('user');
        foreach ($names as $id => $name) {
            try {
                $api->show($name);
                $code = 200;
            } catch (\Github\Exception\RuntimeException $e) {
                $code = $e->getCode();
            }

            $responseCodeIdMap[$code][] = $id;
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