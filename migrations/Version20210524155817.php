<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210524155817 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE team ADD shortname VARCHAR(255) NOT NULL, ADD contactname VARCHAR(255) DEFAULT NULL, ADD contactmail VARCHAR(255) DEFAULT NULL, ADD contactphone VARCHAR(255) DEFAULT NULL, ADD created_at DATETIME NOT NULL, CHANGE name fullname VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user ADD birthday DATE DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE team ADD name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP fullname, DROP shortname, DROP contactname, DROP contactmail, DROP contactphone, DROP created_at');
        $this->addSql('ALTER TABLE user DROP birthday');
    }
}
