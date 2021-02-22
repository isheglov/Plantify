<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210222202630 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE region_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE region (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE planting ADD region_id INT NOT NULL');
        $this->addSql('ALTER TABLE planting ALTER planting_month TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE planting ALTER planting_month DROP DEFAULT');
        $this->addSql('ALTER TABLE planting ALTER planting_seedling_month TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE planting ALTER planting_seedling_month DROP DEFAULT');
        $this->addSql('ALTER TABLE planting ADD CONSTRAINT FK_EB3D080D98260155 FOREIGN KEY (region_id) REFERENCES region (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_EB3D080D98260155 ON planting (region_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE planting DROP CONSTRAINT FK_EB3D080D98260155');
        $this->addSql('DROP SEQUENCE region_id_seq CASCADE');
        $this->addSql('DROP TABLE region');
        $this->addSql('DROP INDEX IDX_EB3D080D98260155');
        $this->addSql('ALTER TABLE planting DROP region_id');
        $this->addSql('ALTER TABLE planting ALTER planting_month TYPE INT');
        $this->addSql('ALTER TABLE planting ALTER planting_month DROP DEFAULT');
        $this->addSql('ALTER TABLE planting ALTER planting_seedling_month TYPE INT');
        $this->addSql('ALTER TABLE planting ALTER planting_seedling_month DROP DEFAULT');
    }
}
