<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210420064538 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE garden ADD owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE garden ADD CONSTRAINT FK_3C0918EA7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3C0918EA7E3C61F9 ON garden (owner_id)');
        $this->addSql('ALTER TABLE history ALTER planted_from SET NOT NULL');
        $this->addSql('ALTER TABLE history ALTER planted_to SET NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE garden DROP CONSTRAINT FK_3C0918EA7E3C61F9');
        $this->addSql('DROP INDEX UNIQ_3C0918EA7E3C61F9');
        $this->addSql('ALTER TABLE garden DROP owner_id');
        $this->addSql('ALTER TABLE history ALTER planted_from DROP NOT NULL');
        $this->addSql('ALTER TABLE history ALTER planted_to DROP NOT NULL');
    }
}
