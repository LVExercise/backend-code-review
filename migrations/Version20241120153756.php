<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241120153756 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, uuid CHAR(36) NOT NULL --(DC2Type:guid)
        , text VARCHAR(255) NOT NULL, status VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL)');
        $this->addSql('DROP TABLE message');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE message (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, uuid CHAR(36) NOT NULL COLLATE "BINARY" --(DC2Type:guid)
        , text VARCHAR(255) NOT NULL COLLATE "BINARY", status VARCHAR(255) DEFAULT NULL COLLATE "BINARY", created_at DATETIME NOT NULL)');
        $this->addSql('DROP TABLE messages');
    }
}
