<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210222200659 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE plant_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE planting_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE plant (id INT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE planting (id INT NOT NULL, plant_id INT NOT NULL, planting_month INT NOT NULL, planting_seedling_month INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EB3D080D1D935652 ON planting (plant_id)');
        $this->addSql('ALTER TABLE planting ADD CONSTRAINT FK_EB3D080D1D935652 FOREIGN KEY (plant_id) REFERENCES plant (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE planting DROP CONSTRAINT FK_EB3D080D1D935652');
        $this->addSql('DROP SEQUENCE plant_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE planting_id_seq CASCADE');
        $this->addSql('DROP TABLE plant');
        $this->addSql('DROP TABLE planting');
    }
}
