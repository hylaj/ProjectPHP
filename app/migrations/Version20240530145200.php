<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240530145200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, UNIQUE INDEX email_idx (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE books ADD item_author_id INT NOT NULL');
        $this->addSql('ALTER TABLE books ADD CONSTRAINT FK_4A1B2A92FAAFE5EB FOREIGN KEY (item_author_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_4A1B2A92FAAFE5EB ON books (item_author_id)');
        $this->addSql('ALTER TABLE categories ADD item_author_id INT NOT NULL');
        $this->addSql('ALTER TABLE categories ADD CONSTRAINT FK_3AF34668FAAFE5EB FOREIGN KEY (item_author_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_3AF34668FAAFE5EB ON categories (item_author_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE books DROP FOREIGN KEY FK_4A1B2A92FAAFE5EB');
        $this->addSql('ALTER TABLE categories DROP FOREIGN KEY FK_3AF34668FAAFE5EB');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP INDEX IDX_4A1B2A92FAAFE5EB ON books');
        $this->addSql('ALTER TABLE books DROP item_author_id');
        $this->addSql('DROP INDEX IDX_3AF34668FAAFE5EB ON categories');
        $this->addSql('ALTER TABLE categories DROP item_author_id');
    }
}
