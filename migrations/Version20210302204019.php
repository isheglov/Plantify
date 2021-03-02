<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210302204019 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE garden_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE garden_cell_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE garden (id INT NOT NULL, dimension_x INT NOT NULL, dimension_y INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE garden_cell (id INT NOT NULL, plant_id INT DEFAULT NULL, garden_id INT NOT NULL, position_x INT NOT NULL, position_y INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1D4915871D935652 ON garden_cell (plant_id)');
        $this->addSql('CREATE INDEX IDX_1D49158739F3B087 ON garden_cell (garden_id)');
        $this->addSql('ALTER TABLE garden_cell ADD CONSTRAINT FK_1D4915871D935652 FOREIGN KEY (plant_id) REFERENCES plant (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE garden_cell ADD CONSTRAINT FK_1D49158739F3B087 FOREIGN KEY (garden_id) REFERENCES garden (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE garden_cell DROP CONSTRAINT FK_1D49158739F3B087');
        $this->addSql('DROP SEQUENCE garden_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE garden_cell_id_seq CASCADE');
        $this->addSql('DROP TABLE garden');
        $this->addSql('DROP TABLE garden_cell');
    }
}
