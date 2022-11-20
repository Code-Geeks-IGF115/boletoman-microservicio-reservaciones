<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221119235141 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE butaca ADD categoria_butaca_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE butaca ADD CONSTRAINT FK_1CE19F3B722DF7E3 FOREIGN KEY (categoria_butaca_id) REFERENCES categoria_butaca (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_1CE19F3B722DF7E3 ON butaca (categoria_butaca_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE butaca DROP CONSTRAINT FK_1CE19F3B722DF7E3');
        $this->addSql('DROP INDEX IDX_1CE19F3B722DF7E3');
        $this->addSql('ALTER TABLE butaca DROP categoria_butaca_id');
    }
}
