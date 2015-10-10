<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151010231307 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('
            CREATE TABLE `s_names` (
              `id` INT(11) PRIMARY KEY AUTO_INCREMENT,
              `name` VARCHAR(11),
              `github_response_code` INT(11) DEFAULT 0,
              UNIQUE KEY (`name`)
            ) ENGINE=InnoDB;
        ');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) {}
}
