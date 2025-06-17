<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250617100810 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__reservation AS SELECT id, date_debut, date_fin FROM reservation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reservation
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reservation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, salles_id INTEGER NOT NULL, users_id INTEGER NOT NULL, date_debut DATE NOT NULL --(DC2Type:date_immutable)
            , date_fin DATE NOT NULL --(DC2Type:date_immutable)
            , CONSTRAINT FK_42C84955B11E4946 FOREIGN KEY (salles_id) REFERENCES salle (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_42C8495567B3B43D FOREIGN KEY (users_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO reservation (id, date_debut, date_fin) SELECT id, date_debut, date_fin FROM __temp__reservation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__reservation
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_42C84955B11E4946 ON reservation (salles_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_42C8495567B3B43D ON reservation (users_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE salle ADD COLUMN statut BOOLEAN NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__reservation AS SELECT id, date_debut, date_fin FROM reservation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reservation
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reservation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, date_debut DATE NOT NULL --(DC2Type:date_immutable)
            , date_fin DATE NOT NULL --(DC2Type:date_immutable)
            , statut BOOLEAN NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO reservation (id, date_debut, date_fin) SELECT id, date_debut, date_fin FROM __temp__reservation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__reservation
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__salle AS SELECT id, nom, lieu, capacite FROM salle
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE salle
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE salle (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(80) DEFAULT NULL, lieu VARCHAR(125) DEFAULT NULL, capacite INTEGER NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO salle (id, nom, lieu, capacite) SELECT id, nom, lieu, capacite FROM __temp__salle
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__salle
        SQL);
    }
}
