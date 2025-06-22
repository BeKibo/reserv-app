<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250618144112 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE crit_ergo (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(80) NOT NULL, categorie VARCHAR(80) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE equipement (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(80) NOT NULL, categorie VARCHAR(80) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(80) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON `user` (email)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE salle (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(80) DEFAULT NULL, lieu VARCHAR(125) DEFAULT NULL, capacite INT NOT NULL, image VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, salles_id INT NOT NULL, users_id INT NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, validation BOOLEAN NOT NULL, PRIMARY KEY(id), INDEX IDX_42C84955B11E4946 (salles_id), INDEX IDX_42C8495567B3B43D (users_id), CONSTRAINT FK_42C84955B11E4946 FOREIGN KEY (salles_id) REFERENCES salle (id), CONSTRAINT FK_42C8495567B3B43D FOREIGN KEY (users_id) REFERENCES `user` (id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE salle_equipement (salle_id INT NOT NULL, equipement_id INT NOT NULL, PRIMARY KEY(salle_id, equipement_id), INDEX IDX_D338336BDC304035 (salle_id), INDEX IDX_D338336B806F0F5C (equipement_id), CONSTRAINT FK_D338336BDC304035 FOREIGN KEY (salle_id) REFERENCES salle (id) ON DELETE CASCADE, CONSTRAINT FK_D338336B806F0F5C FOREIGN KEY (equipement_id) REFERENCES equipement (id) ON DELETE CASCADE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D338336BDC304035 ON salle_equipement (salle_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D338336B806F0F5C ON salle_equipement (equipement_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE salle_crit_ergo (salle_id INT NOT NULL, crit_ergo_id INT NOT NULL, PRIMARY KEY(salle_id, crit_ergo_id), INDEX IDX_12D4772FDC304035 (salle_id), INDEX IDX_12D4772FC0DF500E (crit_ergo_id), CONSTRAINT FK_12D4772FDC304035 FOREIGN KEY (salle_id) REFERENCES salle (id) ON DELETE CASCADE, CONSTRAINT FK_12D4772FC0DF500E FOREIGN KEY (crit_ergo_id) REFERENCES crit_ergo (id) ON DELETE CASCADE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_12D4772FDC304035 ON salle_crit_ergo (salle_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_12D4772FC0DF500E ON salle_crit_ergo (crit_ergo_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id INT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE crit_ergo
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE equipement
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reservation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE salle
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE salle_equipement
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE salle_crit_ergo
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE `user`
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
