<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260531144852 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande_intervention DROP CONSTRAINT fk_86d186d116227374');
        $this->addSql('ALTER TABLE demande_intervention ADD CONSTRAINT FK_86D186D116227374 FOREIGN KEY (campagne_id) REFERENCES campagne_validation (id) ON DELETE SET NULL NOT DEFERRABLE');
        $this->addSql('ALTER TABLE tache DROP CONSTRAINT fk_9387207516227374');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_9387207516227374 FOREIGN KEY (campagne_id) REFERENCES campagne_validation (id) ON DELETE SET NULL NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande_intervention DROP CONSTRAINT FK_86D186D116227374');
        $this->addSql('ALTER TABLE demande_intervention ADD CONSTRAINT fk_86d186d116227374 FOREIGN KEY (campagne_id) REFERENCES campagne_validation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tache DROP CONSTRAINT FK_9387207516227374');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT fk_9387207516227374 FOREIGN KEY (campagne_id) REFERENCES campagne_validation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
