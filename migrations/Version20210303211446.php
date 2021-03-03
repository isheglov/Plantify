<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210303211446 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE plant_plant (plant_source INT NOT NULL, plant_target INT NOT NULL, PRIMARY KEY(plant_source, plant_target))');
        $this->addSql('CREATE INDEX IDX_58D994FABC1AAEF8 ON plant_plant (plant_source)');
        $this->addSql('CREATE INDEX IDX_58D994FAA5FFFE77 ON plant_plant (plant_target)');
        $this->addSql('ALTER TABLE plant_plant ADD CONSTRAINT FK_58D994FABC1AAEF8 FOREIGN KEY (plant_source) REFERENCES plant (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE plant_plant ADD CONSTRAINT FK_58D994FAA5FFFE77 FOREIGN KEY (plant_target) REFERENCES plant (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE plant_plant');
    }
}
