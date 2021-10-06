<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210820184959 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE metric_record (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, axis_xtype_id INT NOT NULL, axis_ytype_id INT DEFAULT NULL, metric_created_at DATETIME NOT NULL, axis_xdata NUMERIC(10, 2) NOT NULL, axis_ydata NUMERIC(10, 2) DEFAULT NULL, INDEX IDX_1BD2A4A5A76ED395 (user_id), INDEX IDX_1BD2A4A542CE731A (axis_xtype_id), INDEX IDX_1BD2A4A58E647384 (axis_ytype_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE metric_record ADD CONSTRAINT FK_1BD2A4A5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE metric_record ADD CONSTRAINT FK_1BD2A4A542CE731A FOREIGN KEY (axis_xtype_id) REFERENCES metric_type (id)');
        $this->addSql('ALTER TABLE metric_record ADD CONSTRAINT FK_1BD2A4A58E647384 FOREIGN KEY (axis_ytype_id) REFERENCES metric_type (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE metric_record');
    }
}
