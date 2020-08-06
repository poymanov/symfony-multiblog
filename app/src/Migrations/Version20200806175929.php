<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200806175929 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE like_likes (id UUID NOT NULL, author_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, entity_type VARCHAR(255) NOT NULL, entity_id VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FEAE679AF675F31BC412EE0281257D5D ON like_likes (author_id, entity_type, entity_id)');
        $this->addSql('COMMENT ON COLUMN like_likes.id IS \'(DC2Type:like_like_id)\'');
        $this->addSql('COMMENT ON COLUMN like_likes.author_id IS \'(DC2Type:like_like_author_id)\'');
        $this->addSql('COMMENT ON COLUMN like_likes.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE post_posts ALTER author_id TYPE UUID');
        $this->addSql('ALTER TABLE post_posts ALTER author_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN post_posts.author_id IS \'(DC2Type:post_post_author_id)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE like_likes');
        $this->addSql('ALTER TABLE post_posts ALTER author_id TYPE UUID');
        $this->addSql('ALTER TABLE post_posts ALTER author_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN post_posts.author_id IS NULL');
    }
}
