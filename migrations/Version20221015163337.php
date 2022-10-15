<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221015163337 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE butaca_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE categoria_butaca_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE celda_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE sala_de_eventos_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE butaca (id INT NOT NULL, celda_id INT NOT NULL, codigo_butaca VARCHAR(15) NOT NULL, disponible VARCHAR(10) NOT NULL, mesa INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1CE19F3BEBD6899B ON butaca (celda_id)');
        $this->addSql('CREATE TABLE categoria_butaca (id INT NOT NULL, sala_de_eventos_id INT NOT NULL, codigo VARCHAR(10) NOT NULL, precio_unitario NUMERIC(10, 2) NOT NULL, nombre VARCHAR(25) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1E8D3BF8ADDC7720 ON categoria_butaca (sala_de_eventos_id)');
        $this->addSql('CREATE TABLE celda (id INT NOT NULL, categoria_butaca_id INT DEFAULT NULL, fila INT NOT NULL, columna INT NOT NULL, cantidad_mesas INT NOT NULL, cantidad_butacas INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6EA13098722DF7E3 ON celda (categoria_butaca_id)');
        $this->addSql('CREATE TABLE sala_de_eventos (id INT NOT NULL, nombre VARCHAR(50) NOT NULL, direccion VARCHAR(255) NOT NULL, telefono VARCHAR(9) NOT NULL, email VARCHAR(255) NOT NULL, forma INT NOT NULL, filas INT NOT NULL, columnas INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE butaca ADD CONSTRAINT FK_1CE19F3BEBD6899B FOREIGN KEY (celda_id) REFERENCES celda (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE categoria_butaca ADD CONSTRAINT FK_1E8D3BF8ADDC7720 FOREIGN KEY (sala_de_eventos_id) REFERENCES sala_de_eventos (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE celda ADD CONSTRAINT FK_6EA13098722DF7E3 FOREIGN KEY (categoria_butaca_id) REFERENCES categoria_butaca (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE butaca_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE categoria_butaca_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE celda_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE sala_de_eventos_id_seq CASCADE');
        $this->addSql('ALTER TABLE butaca DROP CONSTRAINT FK_1CE19F3BEBD6899B');
        $this->addSql('ALTER TABLE categoria_butaca DROP CONSTRAINT FK_1E8D3BF8ADDC7720');
        $this->addSql('ALTER TABLE celda DROP CONSTRAINT FK_6EA13098722DF7E3');
        $this->addSql('DROP TABLE butaca');
        $this->addSql('DROP TABLE categoria_butaca');
        $this->addSql('DROP TABLE celda');
        $this->addSql('DROP TABLE sala_de_eventos');
    }
}
