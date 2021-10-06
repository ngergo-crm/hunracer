<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210824212554 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE metric_record DROP FOREIGN KEY FK_1BD2A4A525E7D495');
        $this->addSql('DROP INDEX IDX_1BD2A4A525E7D495 ON metric_record');
        $this->addSql('ALTER TABLE metric_record CHANGE axis_x_type_id axis_xtype_id INT NOT NULL');
        $this->addSql('ALTER TABLE metric_record ADD CONSTRAINT FK_1BD2A4A542CE731A FOREIGN KEY (axis_xtype_id) REFERENCES metric_type (id)');
        $this->addSql('CREATE INDEX IDX_1BD2A4A542CE731A ON metric_record (axis_xtype_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE metric_record DROP FOREIGN KEY FK_1BD2A4A542CE731A');
        $this->addSql('DROP INDEX IDX_1BD2A4A542CE731A ON metric_record');
        $this->addSql('ALTER TABLE metric_record CHANGE axis_xtype_id axis_x_type_id INT NOT NULL');
        $this->addSql('ALTER TABLE metric_record ADD CONSTRAINT FK_1BD2A4A525E7D495 FOREIGN KEY (axis_x_type_id) REFERENCES metric_type (id)');
        $this->addSql('CREATE INDEX IDX_1BD2A4A525E7D495 ON metric_record (axis_x_type_id)');
    }
}
