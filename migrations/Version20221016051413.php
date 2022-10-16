<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221016051413 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE celda ADD sala_de_eventos_id INT NOT NULL');
        $this->addSql('ALTER TABLE celda ADD CONSTRAINT FK_6EA13098ADDC7720 FOREIGN KEY (sala_de_eventos_id) REFERENCES sala_de_eventos (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_6EA13098ADDC7720 ON celda (sala_de_eventos_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE celda DROP CONSTRAINT FK_6EA13098ADDC7720');
        $this->addSql('DROP INDEX IDX_6EA13098ADDC7720');
        $this->addSql('ALTER TABLE celda DROP sala_de_eventos_id');
    }
}
