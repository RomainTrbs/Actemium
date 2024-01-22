<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240118160125 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE collaborateur ADD poste_id INT DEFAULT NULL, DROP poste');
        $this->addSql('ALTER TABLE collaborateur ADD CONSTRAINT FK_770CBCD3A0905086 FOREIGN KEY (poste_id) REFERENCES poste (id)');
        $this->addSql('CREATE INDEX IDX_770CBCD3A0905086 ON collaborateur (poste_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE collaborateur DROP FOREIGN KEY FK_770CBCD3A0905086');
        $this->addSql('DROP INDEX IDX_770CBCD3A0905086 ON collaborateur');
        $this->addSql('ALTER TABLE collaborateur ADD poste VARCHAR(50) DEFAULT NULL, DROP poste_id');
    }
}
