<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210907205000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE processus ADD societe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE processus ADD CONSTRAINT FK_EEEA8C1DFCF77503 FOREIGN KEY (societe_id) REFERENCES societe (id)');
        $this->addSql('CREATE INDEX IDX_EEEA8C1DFCF77503 ON processus (societe_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE processus DROP FOREIGN KEY FK_EEEA8C1DFCF77503');
        $this->addSql('DROP INDEX IDX_EEEA8C1DFCF77503 ON processus');
        $this->addSql('ALTER TABLE processus DROP societe_id');
    }
}
