<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221224012006 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE licence ADD obtention_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE licence ADD CONSTRAINT FK_1DAAE6486BDB9F4B FOREIGN KEY (obtention_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_1DAAE6486BDB9F4B ON licence (obtention_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE licence DROP FOREIGN KEY FK_1DAAE6486BDB9F4B');
        $this->addSql('DROP INDEX IDX_1DAAE6486BDB9F4B ON licence');
        $this->addSql('ALTER TABLE licence DROP obtention_id');
    }
}
