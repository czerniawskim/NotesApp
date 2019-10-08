<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191008161344 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_11BA68CA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__notes AS SELECT id, user_id, content, created_at FROM notes');
        $this->addSql('DROP TABLE notes');
        $this->addSql('CREATE TABLE notes (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, content CLOB NOT NULL COLLATE BINARY, created_at DATETIME NOT NULL, CONSTRAINT FK_11BA68CA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO notes (id, user_id, content, created_at) SELECT id, user_id, content, created_at FROM __temp__notes');
        $this->addSql('DROP TABLE __temp__notes');
        $this->addSql('CREATE INDEX IDX_11BA68CA76ED395 ON notes (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_11BA68CA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__notes AS SELECT id, user_id, content, created_at FROM notes');
        $this->addSql('DROP TABLE notes');
        $this->addSql('CREATE TABLE notes (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, content CLOB NOT NULL, created_at DATETIME NOT NULL, color VARCHAR(100) DEFAULT NULL COLLATE BINARY)');
        $this->addSql('INSERT INTO notes (id, user_id, content, created_at) SELECT id, user_id, content, created_at FROM __temp__notes');
        $this->addSql('DROP TABLE __temp__notes');
        $this->addSql('CREATE INDEX IDX_11BA68CA76ED395 ON notes (user_id)');
    }
}
