<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240130073059 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE affaire (id INT AUTO_INCREMENT NOT NULL, num_affaire VARCHAR(255) DEFAULT NULL, client VARCHAR(255) DEFAULT NULL, designation VARCHAR(255) DEFAULT NULL, nbre_heure DOUBLE PRECISION NOT NULL, date_debut DATETIME NOT NULL, heure_passe INT NOT NULL, date_fin DATETIME DEFAULT NULL, nbre_jour_fractionnement INT DEFAULT NULL, pourcent_reserve INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE affaire_collaborateur (affaire_id INT NOT NULL, collaborateur_id INT NOT NULL, INDEX IDX_DAD29791F082E755 (affaire_id), INDEX IDX_DAD29791A848E3B1 (collaborateur_id), PRIMARY KEY(affaire_id, collaborateur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE collaborateur (id INT AUTO_INCREMENT NOT NULL, poste_id INT DEFAULT NULL, status_id INT NOT NULL, representant_id INT DEFAULT NULL, prenom VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, hr_jour DOUBLE PRECISION NOT NULL, hr_semaine DOUBLE PRECISION NOT NULL, jour_semaine INT NOT NULL, INDEX IDX_770CBCD3A0905086 (poste_id), INDEX IDX_770CBCD36BF700BD (status_id), INDEX IDX_770CBCD36C4A52F0 (representant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE poste (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE status (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, collaborateur_id INT DEFAULT NULL, username VARCHAR(255) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649A848E3B1 (collaborateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE affaire_collaborateur ADD CONSTRAINT FK_DAD29791F082E755 FOREIGN KEY (affaire_id) REFERENCES affaire (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE affaire_collaborateur ADD CONSTRAINT FK_DAD29791A848E3B1 FOREIGN KEY (collaborateur_id) REFERENCES collaborateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE collaborateur ADD CONSTRAINT FK_770CBCD3A0905086 FOREIGN KEY (poste_id) REFERENCES poste (id)');
        $this->addSql('ALTER TABLE collaborateur ADD CONSTRAINT FK_770CBCD36BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE collaborateur ADD CONSTRAINT FK_770CBCD36C4A52F0 FOREIGN KEY (representant_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649A848E3B1 FOREIGN KEY (collaborateur_id) REFERENCES collaborateur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE affaire_collaborateur DROP FOREIGN KEY FK_DAD29791F082E755');
        $this->addSql('ALTER TABLE affaire_collaborateur DROP FOREIGN KEY FK_DAD29791A848E3B1');
        $this->addSql('ALTER TABLE collaborateur DROP FOREIGN KEY FK_770CBCD3A0905086');
        $this->addSql('ALTER TABLE collaborateur DROP FOREIGN KEY FK_770CBCD36BF700BD');
        $this->addSql('ALTER TABLE collaborateur DROP FOREIGN KEY FK_770CBCD36C4A52F0');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649A848E3B1');
        $this->addSql('DROP TABLE affaire');
        $this->addSql('DROP TABLE affaire_collaborateur');
        $this->addSql('DROP TABLE collaborateur');
        $this->addSql('DROP TABLE poste');
        $this->addSql('DROP TABLE status');
        $this->addSql('DROP TABLE user');
    }
}
