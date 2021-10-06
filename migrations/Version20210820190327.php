<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210820190327 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE metric_record DROP FOREIGN KEY FK_1BD2A4A542CE731A');
        $this->addSql('ALTER TABLE metric_record DROP FOREIGN KEY FK_1BD2A4A58E647384');
        $this->addSql('DROP INDEX IDX_1BD2A4A58E647384 ON metric_record');
        $this->addSql('DROP INDEX IDX_1BD2A4A542CE731A ON metric_record');
        $this->addSql('ALTER TABLE metric_record CHANGE axis_xtype_id axis_x_type_id INT NOT NULL, CHANGE axis_ytype_id axis_y_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE metric_record ADD CONSTRAINT FK_1BD2A4A525E7D495 FOREIGN KEY (axis_x_type_id) REFERENCES metric_type (id)');
        $this->addSql('ALTER TABLE metric_record ADD CONSTRAINT FK_1BD2A4A5329CC0D6 FOREIGN KEY (axis_y_type_id) REFERENCES metric_type (id)');
        $this->addSql('CREATE INDEX IDX_1BD2A4A525E7D495 ON metric_record (axis_x_type_id)');
        $this->addSql('CREATE INDEX IDX_1BD2A4A5329CC0D6 ON metric_record (axis_y_type_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE metric_record DROP FOREIGN KEY FK_1BD2A4A525E7D495');
        $this->addSql('ALTER TABLE metric_record DROP FOREIGN KEY FK_1BD2A4A5329CC0D6');
        $this->addSql('DROP INDEX IDX_1BD2A4A525E7D495 ON metric_record');
        $this->addSql('DROP INDEX IDX_1BD2A4A5329CC0D6 ON metric_record');
        $this->addSql('ALTER TABLE metric_record CHANGE axis_x_type_id axis_xtype_id INT NOT NULL, CHANGE axis_y_type_id axis_ytype_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE metric_record ADD CONSTRAINT FK_1BD2A4A542CE731A FOREIGN KEY (axis_xtype_id) REFERENCES metric_type (id)');
        $this->addSql('ALTER TABLE metric_record ADD CONSTRAINT FK_1BD2A4A58E647384 FOREIGN KEY (axis_ytype_id) REFERENCES metric_type (id)');
        $this->addSql('CREATE INDEX IDX_1BD2A4A58E647384 ON metric_record (axis_ytype_id)');
        $this->addSql('CREATE INDEX IDX_1BD2A4A542CE731A ON metric_record (axis_xtype_id)');
    }
}
