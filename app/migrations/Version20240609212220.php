<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240609212220 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE covers DROP FOREIGN KEY FK_F08DF1B216A2B381');
        $this->addSql('DROP TABLE covers');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE covers (id INT AUTO_INCREMENT NOT NULL, book_id INT NOT NULL, filename VARCHAR(191) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX uq_covers_filename (filename), UNIQUE INDEX UNIQ_F08DF1B216A2B381 (book_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE covers ADD CONSTRAINT FK_F08DF1B216A2B381 FOREIGN KEY (book_id) REFERENCES books (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
