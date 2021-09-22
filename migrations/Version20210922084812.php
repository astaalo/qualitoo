<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210922084812 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE structure DROP FOREIGN KEY FK_6F0137EAAF73D997');
        $this->addSql('ALTER TABLE structure DROP FOREIGN KEY FK_6F0137EA727ACA70');
        $this->addSql('DROP INDEX IDX_6F0137EAAF73D997 ON structure');
        $this->addSql('ALTER TABLE structure ADD code VARCHAR(100) NOT NULL, ADD name VARCHAR(255) NOT NULL, ADD direction VARCHAR(255) NOT NULL, ADD lft INT NOT NULL, ADD lvl INT NOT NULL, ADD rgt INT NOT NULL, CHANGE direction_id root INT DEFAULT NULL');
        $this->addSql('ALTER TABLE structure ADD CONSTRAINT FK_6F0137EA727ACA70 FOREIGN KEY (parent_id) REFERENCES structure (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE structure DROP FOREIGN KEY FK_6F0137EA727ACA70');
        $this->addSql('ALTER TABLE structure DROP code, DROP name, DROP direction, DROP lft, DROP lvl, DROP rgt, CHANGE root direction_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE structure ADD CONSTRAINT FK_6F0137EAAF73D997 FOREIGN KEY (direction_id) REFERENCES direction (id)');
        $this->addSql('ALTER TABLE structure ADD CONSTRAINT FK_6F0137EA727ACA70 FOREIGN KEY (parent_id) REFERENCES structure (id)');
        $this->addSql('CREATE INDEX IDX_6F0137EAAF73D997 ON structure (direction_id)');
    }
}
