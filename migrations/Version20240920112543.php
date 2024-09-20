<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs! 😎
 */
final class Version20240920112543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }
    // YEEEAHHH 😍
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment ADD comment_published TINYINT(1) DEFAULT 0 NOT NULL');
    }
    // NOOOO 😭
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP comment_published');
    }
}
