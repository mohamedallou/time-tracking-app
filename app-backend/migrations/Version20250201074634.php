<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250201074634 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE token_access_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE token_access (id INT NOT NULL, user_id INT DEFAULT NULL, token_jti VARCHAR(255) NOT NULL, access_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, token_expiration_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7118456A76ED395 ON token_access (user_id)');
        $this->addSql('COMMENT ON COLUMN token_access.access_time IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN token_access.token_expiration_time IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE token_access ADD CONSTRAINT FK_7118456A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE token_access_id_seq CASCADE');
        $this->addSql('ALTER TABLE token_access DROP CONSTRAINT FK_7118456A76ED395');
        $this->addSql('DROP TABLE token_access');
    }
}
