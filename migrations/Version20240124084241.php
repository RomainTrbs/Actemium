<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240124084241 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE collaborateur DROP FOREIGN KEY FK_collaborateur_status');
        $this->addSql('DROP TABLE status');
        $this->addSql('DROP INDEX FK_collaborateur_status ON collaborateur');
        $this->addSql('ALTER TABLE collaborateur DROP status_id');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_user_collaborateur');
        $this->addSql('DROP INDEX FK_user_collaborateur ON user');
        $this->addSql('ALTER TABLE user DROP collaborateur_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE status (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE collaborateur ADD status_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE collaborateur ADD CONSTRAINT FK_collaborateur_status FOREIGN KEY (status_id) REFERENCES status (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX FK_collaborateur_status ON collaborateur (status_id)');
        $this->addSql('ALTER TABLE user ADD collaborateur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_user_collaborateur FOREIGN KEY (collaborateur_id) REFERENCES collaborateur (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX FK_user_collaborateur ON user (collaborateur_id)');
    }
}
