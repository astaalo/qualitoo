<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210907160822 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE processus DROP FOREIGN KEY FK_EEEA8C1D727ACA70');
        $this->addSql('ALTER TABLE processus DROP lft, DROP lvl, DROP rgt, DROP root');
        $this->addSql('ALTER TABLE processus ADD CONSTRAINT FK_EEEA8C1D727ACA70 FOREIGN KEY (parent_id) REFERENCES processus (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE processus DROP FOREIGN KEY FK_EEEA8C1D727ACA70');
        $this->addSql('ALTER TABLE processus ADD lft INT NOT NULL, ADD lvl INT NOT NULL, ADD rgt INT NOT NULL, ADD root INT DEFAULT NULL');
        $this->addSql('ALTER TABLE processus ADD CONSTRAINT FK_EEEA8C1D727ACA70 FOREIGN KEY (parent_id) REFERENCES processus (id) ON DELETE CASCADE');
    }
}
