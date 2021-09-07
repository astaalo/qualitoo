<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210907080320 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE document (id INT AUTO_INCREMENT NOT NULL, utilisateur INT DEFAULT NULL, type_document_id INT DEFAULT NULL, libelle VARCHAR(100) NOT NULL, nom_fichier VARCHAR(100) DEFAULT NULL, date_creation DATETIME NOT NULL, etat TINYINT(1) DEFAULT NULL, description LONGTEXT DEFAULT NULL, file VARCHAR(255) NOT NULL, profils LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', annee INT DEFAULT NULL, deleted TINYINT(1) NOT NULL, INDEX IDX_D8698A761D1C63B3 (utilisateur), INDEX IDX_D8698A768826AFA6 (type_document_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, is_read TINYINT(1) NOT NULL, dateCreation DATETIME NOT NULL, dateModification DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification_receivers (notification_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_A4B606A9EF1A9D84 (notification_id), INDEX IDX_A4B606A9A76ED395 (user_id), PRIMARY KEY(notification_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE processus (id INT AUTO_INCREMENT NOT NULL, structure_id INT DEFAULT NULL, type_processus_id INT DEFAULT NULL, origine INT DEFAULT NULL, parent_id INT DEFAULT NULL, numero INT DEFAULT NULL, libelle VARCHAR(255) DEFAULT NULL, libelle_sans_carspecial VARCHAR(255) DEFAULT NULL, code VARCHAR(50) DEFAULT NULL, etat TINYINT(1) DEFAULT NULL, description LONGTEXT DEFAULT NULL, lft INT NOT NULL, lvl INT NOT NULL, rgt INT NOT NULL, root INT DEFAULT NULL, INDEX IDX_EEEA8C1D2534008B (structure_id), INDEX IDX_EEEA8C1D221593D9 (type_processus_id), INDEX IDX_EEEA8C1D150BB66F (origine), INDEX IDX_EEEA8C1D727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE societe (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(100) NOT NULL, photo VARCHAR(255) DEFAULT NULL, etat TINYINT(1) DEFAULT NULL, isAdmin TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE structure (id INT AUTO_INCREMENT NOT NULL, type_structure_id INT DEFAULT NULL, societe_id INT DEFAULT NULL, code VARCHAR(100) NOT NULL, name VARCHAR(255) NOT NULL, name_sans_spec_char VARCHAR(255) NOT NULL, libelle VARCHAR(100) NOT NULL, date_creation DATETIME NOT NULL, etat TINYINT(1) DEFAULT NULL, INDEX IDX_6F0137EAA277BA8E (type_structure_id), INDEX IDX_6F0137EAFCF77503 (societe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_document (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(100) DEFAULT NULL, code VARCHAR(15) DEFAULT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_processus (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(45) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_structure (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(45) NOT NULL, code VARCHAR(100) NOT NULL, etat TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, structure_id INT DEFAULT NULL, societe_id INT DEFAULT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', prenom VARCHAR(100) NOT NULL, nom VARCHAR(100) NOT NULL, matricule INT DEFAULT NULL, telephone VARCHAR(25) DEFAULT NULL, etat INT NOT NULL, manager TINYINT(1) NOT NULL, connectWindows TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_1D1C63B392FC23A8 (username_canonical), UNIQUE INDEX UNIQ_1D1C63B3A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_1D1C63B3C05FB297 (confirmation_token), INDEX IDX_1D1C63B32534008B (structure_id), INDEX IDX_1D1C63B3FCF77503 (societe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE administrateur (utilisateur_id INT NOT NULL, societe_id INT NOT NULL, INDEX IDX_32EB52E8FB88E14F (utilisateur_id), INDEX IDX_32EB52E8FCF77503 (societe_id), PRIMARY KEY(utilisateur_id, societe_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A761D1C63B3 FOREIGN KEY (utilisateur) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A768826AFA6 FOREIGN KEY (type_document_id) REFERENCES type_document (id)');
        $this->addSql('ALTER TABLE notification_receivers ADD CONSTRAINT FK_A4B606A9EF1A9D84 FOREIGN KEY (notification_id) REFERENCES notification (id)');
        $this->addSql('ALTER TABLE notification_receivers ADD CONSTRAINT FK_A4B606A9A76ED395 FOREIGN KEY (user_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE processus ADD CONSTRAINT FK_EEEA8C1D2534008B FOREIGN KEY (structure_id) REFERENCES structure (id)');
        $this->addSql('ALTER TABLE processus ADD CONSTRAINT FK_EEEA8C1D221593D9 FOREIGN KEY (type_processus_id) REFERENCES type_processus (id)');
        $this->addSql('ALTER TABLE processus ADD CONSTRAINT FK_EEEA8C1D150BB66F FOREIGN KEY (origine) REFERENCES processus (id)');
        $this->addSql('ALTER TABLE processus ADD CONSTRAINT FK_EEEA8C1D727ACA70 FOREIGN KEY (parent_id) REFERENCES processus (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE structure ADD CONSTRAINT FK_6F0137EAA277BA8E FOREIGN KEY (type_structure_id) REFERENCES type_structure (id)');
        $this->addSql('ALTER TABLE structure ADD CONSTRAINT FK_6F0137EAFCF77503 FOREIGN KEY (societe_id) REFERENCES societe (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B32534008B FOREIGN KEY (structure_id) REFERENCES structure (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3FCF77503 FOREIGN KEY (societe_id) REFERENCES societe (id)');
        $this->addSql('ALTER TABLE administrateur ADD CONSTRAINT FK_32EB52E8FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE administrateur ADD CONSTRAINT FK_32EB52E8FCF77503 FOREIGN KEY (societe_id) REFERENCES societe (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification_receivers DROP FOREIGN KEY FK_A4B606A9EF1A9D84');
        $this->addSql('ALTER TABLE processus DROP FOREIGN KEY FK_EEEA8C1D150BB66F');
        $this->addSql('ALTER TABLE processus DROP FOREIGN KEY FK_EEEA8C1D727ACA70');
        $this->addSql('ALTER TABLE structure DROP FOREIGN KEY FK_6F0137EAFCF77503');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3FCF77503');
        $this->addSql('ALTER TABLE administrateur DROP FOREIGN KEY FK_32EB52E8FCF77503');
        $this->addSql('ALTER TABLE processus DROP FOREIGN KEY FK_EEEA8C1D2534008B');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B32534008B');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A768826AFA6');
        $this->addSql('ALTER TABLE processus DROP FOREIGN KEY FK_EEEA8C1D221593D9');
        $this->addSql('ALTER TABLE structure DROP FOREIGN KEY FK_6F0137EAA277BA8E');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A761D1C63B3');
        $this->addSql('ALTER TABLE notification_receivers DROP FOREIGN KEY FK_A4B606A9A76ED395');
        $this->addSql('ALTER TABLE administrateur DROP FOREIGN KEY FK_32EB52E8FB88E14F');
        $this->addSql('DROP TABLE document');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE notification_receivers');
        $this->addSql('DROP TABLE processus');
        $this->addSql('DROP TABLE societe');
        $this->addSql('DROP TABLE structure');
        $this->addSql('DROP TABLE type_document');
        $this->addSql('DROP TABLE type_processus');
        $this->addSql('DROP TABLE type_structure');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE administrateur');
    }
}
