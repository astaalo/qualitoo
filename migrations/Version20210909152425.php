<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210909152425 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document ADD rubrique_id INT DEFAULT NULL, ADD theme_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A763BD38833 FOREIGN KEY (rubrique_id) REFERENCES rubrique (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A7659027487 FOREIGN KEY (theme_id) REFERENCES theme (id)');
        $this->addSql('CREATE INDEX IDX_D8698A763BD38833 ON document (rubrique_id)');
        $this->addSql('CREATE INDEX IDX_D8698A7659027487 ON document (theme_id)');
        $this->addSql('ALTER TABLE rubrique DROP FOREIGN KEY FK_8FA4097CC33F7837');
        $this->addSql('DROP INDEX IDX_8FA4097CC33F7837 ON rubrique');
        $this->addSql('ALTER TABLE rubrique DROP document_id');
        $this->addSql('ALTER TABLE theme DROP FOREIGN KEY FK_9775E708C33F7837');
        $this->addSql('DROP INDEX IDX_9775E708C33F7837 ON theme');
        $this->addSql('ALTER TABLE theme DROP document_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A763BD38833');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A7659027487');
        $this->addSql('DROP INDEX IDX_D8698A763BD38833 ON document');
        $this->addSql('DROP INDEX IDX_D8698A7659027487 ON document');
        $this->addSql('ALTER TABLE document DROP rubrique_id, DROP theme_id');
        $this->addSql('ALTER TABLE rubrique ADD document_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rubrique ADD CONSTRAINT FK_8FA4097CC33F7837 FOREIGN KEY (document_id) REFERENCES document (id)');
        $this->addSql('CREATE INDEX IDX_8FA4097CC33F7837 ON rubrique (document_id)');
        $this->addSql('ALTER TABLE theme ADD document_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE theme ADD CONSTRAINT FK_9775E708C33F7837 FOREIGN KEY (document_id) REFERENCES document (id)');
        $this->addSql('CREATE INDEX IDX_9775E708C33F7837 ON theme (document_id)');
    }
}
