<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200526192102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE csv_link_data_source (id INT AUTO_INCREMENT NOT NULL, import_target_id INT NOT NULL, source_url VARCHAR(255) NOT NULL, delimiter VARCHAR(1) NOT NULL, enclosure VARCHAR(1) NOT NULL, name VARCHAR(100) NOT NULL, name_handle_pattern VARCHAR(255) DEFAULT NULL, description_pattern LONGTEXT NOT NULL, category_name VARCHAR(255) DEFAULT NULL, price VARCHAR(255) NOT NULL, photo_url VARCHAR(255) DEFAULT NULL, album_name VARCHAR(255) DEFAULT NULL, album_handle_pattern VARCHAR(255) DEFAULT NULL, INDEX IDX_12E6F38484FBFDA (import_target_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE csv_link_data_source ADD CONSTRAINT FK_12E6F38484FBFDA FOREIGN KEY (import_target_id) REFERENCES import_target (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE csv_link_data_source');
    }
}
