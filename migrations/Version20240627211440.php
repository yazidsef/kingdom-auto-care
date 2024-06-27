<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240627211440 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE marques DROP FOREIGN KEY FK_67884F2D3DA5256D');
        $this->addSql('DROP INDEX UNIQ_67884F2D3DA5256D ON marques');
        $this->addSql('ALTER TABLE marques DROP image_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE marques ADD image_id INT NOT NULL');
        $this->addSql('ALTER TABLE marques ADD CONSTRAINT FK_67884F2D3DA5256D FOREIGN KEY (image_id) REFERENCES images (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_67884F2D3DA5256D ON marques (image_id)');
    }
}
