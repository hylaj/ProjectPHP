<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240522064440 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE books_tags (book_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_4C35340916A2B381 (book_id), INDEX IDX_4C353409BAD26311 (tag_id), PRIMARY KEY(book_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE books_tags ADD CONSTRAINT FK_4C35340916A2B381 FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE books_tags ADD CONSTRAINT FK_4C353409BAD26311 FOREIGN KEY (tag_id) REFERENCES tags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tasks_tags DROP FOREIGN KEY FK_85533A5016A2B381');
        $this->addSql('ALTER TABLE tasks_tags DROP FOREIGN KEY FK_85533A50BAD26311');
        $this->addSql('DROP TABLE tasks_tags');
        $this->addSql('CREATE UNIQUE INDEX uq_tags_title ON tags (title)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tasks_tags (book_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_85533A5016A2B381 (book_id), INDEX IDX_85533A50BAD26311 (tag_id), PRIMARY KEY(book_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE tasks_tags ADD CONSTRAINT FK_85533A5016A2B381 FOREIGN KEY (book_id) REFERENCES books (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tasks_tags ADD CONSTRAINT FK_85533A50BAD26311 FOREIGN KEY (tag_id) REFERENCES tags (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE books_tags DROP FOREIGN KEY FK_4C35340916A2B381');
        $this->addSql('ALTER TABLE books_tags DROP FOREIGN KEY FK_4C353409BAD26311');
        $this->addSql('DROP TABLE books_tags');
        $this->addSql('DROP INDEX uq_tags_title ON tags');
    }
}
