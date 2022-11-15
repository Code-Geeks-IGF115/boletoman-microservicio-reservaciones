<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221115234101 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE butaca DROP disponible');
        $this->addSql('ALTER TABLE butaca DROP mesa');
        $this->addSql('ALTER TABLE butaca DROP detalle_compra_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE butaca ADD disponible VARCHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE butaca ADD mesa INT DEFAULT NULL');
        $this->addSql('ALTER TABLE butaca ADD detalle_compra_id INT DEFAULT NULL');
    }
}
