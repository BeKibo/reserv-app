<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250622124855 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create salle_equipement join table';
    }

    public function up(Schema $schema): void
    {
        // Create the salle_equipement join table
        $this->addSql(<<<'SQL'
            CREATE TABLE salle_equipement (
                salle_id INT NOT NULL,
                equipement_id INT NOT NULL,
                PRIMARY KEY(salle_id, equipement_id),
                CONSTRAINT FK_D338336BDC304035 FOREIGN KEY (salle_id) REFERENCES salle (id) ON DELETE CASCADE,
                CONSTRAINT FK_D338336B806F0F5C FOREIGN KEY (equipement_id) REFERENCES equipement (id) ON DELETE CASCADE
            )
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D338336BDC304035 ON salle_equipement (salle_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D338336B806F0F5C ON salle_equipement (equipement_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // Drop the salle_equipement join table
        $this->addSql(<<<'SQL'
            DROP TABLE salle_equipement
        SQL);
    }
}
