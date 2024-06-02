<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240602123446 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rentals DROP FOREIGN KEY FK_35ACDB4871868B2E');
        $this->addSql('ALTER TABLE rentals DROP FOREIGN KEY FK_35ACDB48BED2780A');
        $this->addSql('DROP INDEX UNIQ_35ACDB4871868B2E ON rentals');
        $this->addSql('DROP INDEX IDX_35ACDB48BED2780A ON rentals');
        $this->addSql('ALTER TABLE rentals ADD owner_id INT NOT NULL, ADD book_id INT NOT NULL, DROP owner_email_id, DROP book_id_id');
        $this->addSql('ALTER TABLE rentals ADD CONSTRAINT FK_35ACDB487E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE rentals ADD CONSTRAINT FK_35ACDB4816A2B381 FOREIGN KEY (book_id) REFERENCES books (id)');
        $this->addSql('CREATE INDEX IDX_35ACDB487E3C61F9 ON rentals (owner_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_35ACDB4816A2B381 ON rentals (book_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rentals DROP FOREIGN KEY FK_35ACDB487E3C61F9');
        $this->addSql('ALTER TABLE rentals DROP FOREIGN KEY FK_35ACDB4816A2B381');
        $this->addSql('DROP INDEX IDX_35ACDB487E3C61F9 ON rentals');
        $this->addSql('DROP INDEX UNIQ_35ACDB4816A2B381 ON rentals');
        $this->addSql('ALTER TABLE rentals ADD owner_email_id INT NOT NULL, ADD book_id_id INT NOT NULL, DROP owner_id, DROP book_id');
        $this->addSql('ALTER TABLE rentals ADD CONSTRAINT FK_35ACDB4871868B2E FOREIGN KEY (book_id_id) REFERENCES books (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE rentals ADD CONSTRAINT FK_35ACDB48BED2780A FOREIGN KEY (owner_email_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_35ACDB4871868B2E ON rentals (book_id_id)');
        $this->addSql('CREATE INDEX IDX_35ACDB48BED2780A ON rentals (owner_email_id)');
    }
}
