<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200810173253 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment_comments (id UUID NOT NULL, author_id UUID NOT NULL, text TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, entity_type VARCHAR(255) NOT NULL, entity_id VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_42DAF52C8B8E8428 ON comment_comments (created_at)');
        $this->addSql('CREATE INDEX IDX_42DAF52CC412EE0281257D5D ON comment_comments (entity_type, entity_id)');
        $this->addSql('COMMENT ON COLUMN comment_comments.id IS \'(DC2Type:comment_comment_id)\'');
        $this->addSql('COMMENT ON COLUMN comment_comments.author_id IS \'(DC2Type:comment_comment_author_id)\'');
        $this->addSql('COMMENT ON COLUMN comment_comments.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN comment_comments.updated_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE comment_comments');
    }
}
