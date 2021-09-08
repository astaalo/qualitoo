<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210907122430 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE document_structure (document_id INT NOT NULL, structure_id INT NOT NULL, INDEX IDX_7D391F4BC33F7837 (document_id), INDEX IDX_7D391F4B2534008B (structure_id), PRIMARY KEY(document_id, structure_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rubrique (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE theme (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE document_structure ADD CONSTRAINT FK_7D391F4BC33F7837 FOREIGN KEY (document_id) REFERENCES document (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE document_structure ADD CONSTRAINT FK_7D391F4B2534008B FOREIGN KEY (structure_id) REFERENCES structure (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE document DROP profils, DROP annee');
        $this->addSql('ALTER TABLE processus DROP FOREIGN KEY FK_EEEA8C1D150BB66F');
        $this->addSql('DROP INDEX IDX_EEEA8C1D150BB66F ON processus');
        $this->addSql('ALTER TABLE processus DROP origine');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE document_structure');
        $this->addSql('DROP TABLE rubrique');
        $this->addSql('DROP TABLE theme');
        $this->addSql('ALTER TABLE document ADD profils LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:array)\', ADD annee INT DEFAULT NULL');
        $this->addSql('ALTER TABLE processus ADD origine INT DEFAULT NULL');
        $this->addSql('ALTER TABLE processus ADD CONSTRAINT FK_EEEA8C1D150BB66F FOREIGN KEY (origine) REFERENCES processus (id)');
        $this->addSql('CREATE INDEX IDX_EEEA8C1D150BB66F ON processus (origine)');
    }
}
