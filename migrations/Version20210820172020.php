<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210820172020 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE metric_type (id INT AUTO_INCREMENT NOT NULL, metric_group_id INT NOT NULL, description VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, axis VARCHAR(1) DEFAULT NULL, unit VARCHAR(20) DEFAULT NULL, INDEX IDX_3A4053EF4754372 (metric_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE metric_type ADD CONSTRAINT FK_3A4053EF4754372 FOREIGN KEY (metric_group_id) REFERENCES metric_group (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE metric_type');
    }
}
