<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240517080001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE marques (id INT AUTO_INCREMENT NOT NULL, image_id INT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_67884F2D3DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE marques ADD CONSTRAINT FK_67884F2D3DA5256D FOREIGN KEY (image_id) REFERENCES images (id)');
        $this->addSql('ALTER TABLE products ADD marques_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5AC256483C FOREIGN KEY (marques_id) REFERENCES marques (id)');
        $this->addSql('CREATE INDEX IDX_B3BA5A5AC256483C ON products (marques_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5AC256483C');
        $this->addSql('ALTER TABLE marques DROP FOREIGN KEY FK_67884F2D3DA5256D');
        $this->addSql('DROP TABLE marques');
        $this->addSql('DROP INDEX IDX_B3BA5A5AC256483C ON products');
        $this->addSql('ALTER TABLE products DROP marques_id');
    }
}
