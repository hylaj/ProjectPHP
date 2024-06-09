<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240609211107 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE covers (id INT AUTO_INCREMENT NOT NULL, book_id INT NOT NULL, filename VARCHAR(191) NOT NULL, UNIQUE INDEX UNIQ_F08DF1B216A2B381 (book_id), UNIQUE INDEX uq_covers_filename (filename), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE covers ADD CONSTRAINT FK_F08DF1B216A2B381 FOREIGN KEY (book_id) REFERENCES books (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE covers DROP FOREIGN KEY FK_F08DF1B216A2B381');
        $this->addSql('DROP TABLE covers');
    }
}
