<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250129182150 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" ADD blocked BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD deleted BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER INDEX sessions_sess_lifetime_idx RENAME TO sess_lifetime_idx');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER INDEX sess_lifetime_idx RENAME TO sessions_sess_lifetime_idx');
        $this->addSql('ALTER TABLE "user" DROP blocked');
        $this->addSql('ALTER TABLE "user" DROP deleted');
    }
}
