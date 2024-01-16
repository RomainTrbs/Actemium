<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240115074250 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE affaires DROP FOREIGN KEY FK_affaires_collaborateur');
        $this->addSql('DROP INDEX FK_affaires_collaborateur ON affaires');
        $this->addSql('ALTER TABLE affaires CHANGE num_affaire num_affaire INT DEFAULT NULL, CHANGE client client VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE affaires CHANGE num_affaire num_affaire BIGINT DEFAULT NULL, CHANGE client client VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE affaires ADD CONSTRAINT FK_affaires_collaborateur FOREIGN KEY (id_collaborateur) REFERENCES collaborateur (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX FK_affaires_collaborateur ON affaires (id_collaborateur)');
    }
}
