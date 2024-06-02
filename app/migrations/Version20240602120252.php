<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240602120252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rentals (id INT AUTO_INCREMENT NOT NULL, owner_email_id INT NOT NULL, book_id_id INT NOT NULL, rental_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_35ACDB48BED2780A (owner_email_id), UNIQUE INDEX UNIQ_35ACDB4871868B2E (book_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rentals ADD CONSTRAINT FK_35ACDB48BED2780A FOREIGN KEY (owner_email_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE rentals ADD CONSTRAINT FK_35ACDB4871868B2E FOREIGN KEY (book_id_id) REFERENCES books (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rentals DROP FOREIGN KEY FK_35ACDB48BED2780A');
        $this->addSql('ALTER TABLE rentals DROP FOREIGN KEY FK_35ACDB4871868B2E');
        $this->addSql('DROP TABLE rentals');
    }
}
