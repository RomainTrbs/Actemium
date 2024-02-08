<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240207152221 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE collaborateur CHANGE hr_jour hr_jour DOUBLE PRECISION NOT NULL, CHANGE hr_semaine hr_semaine DOUBLE PRECISION NOT NULL, CHANGE jour_semaine jour_semaine INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE collaborateur CHANGE hr_jour hr_jour DOUBLE PRECISION DEFAULT NULL, CHANGE hr_semaine hr_semaine DOUBLE PRECISION DEFAULT NULL, CHANGE jour_semaine jour_semaine INT DEFAULT NULL');
    }
}
