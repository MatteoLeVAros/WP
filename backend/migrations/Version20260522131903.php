<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260522131903 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire ALTER campagne_id DROP NOT NULL');
        $this->addSql('ALTER INDEX idx_bf5476ca961ddaa7 RENAME TO IDX_BF5476CAFB88E14F');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire ALTER campagne_id SET NOT NULL');
        $this->addSql('ALTER INDEX idx_bf5476cafb88e14f RENAME TO idx_bf5476ca961ddaa7');
    }
}
