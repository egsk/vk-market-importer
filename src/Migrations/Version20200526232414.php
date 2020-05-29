<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200526232414 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE vk_product (id INT AUTO_INCREMENT NOT NULL, vk_market_category_id INT NOT NULL, data_source_id INT NOT NULL, vk_market_id INT NOT NULL, source_id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, price DOUBLE PRECISION NOT NULL, photo_url VARCHAR(255) NOT NULL, album_name VARCHAR(255) DEFAULT NULL, url VARCHAR(255) NOT NULL, owner_id INT NOT NULL, old_price DOUBLE PRECISION DEFAULT NULL, discr VARCHAR(255) NOT NULL, INDEX IDX_4CBB869FE6560B5 (vk_market_category_id), INDEX IDX_4CBB8691A935C57 (data_source_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE vk_product ADD CONSTRAINT FK_4CBB869FE6560B5 FOREIGN KEY (vk_market_category_id) REFERENCES vk_market_category (id)');
        $this->addSql('ALTER TABLE vk_product ADD CONSTRAINT FK_4CBB8691A935C57 FOREIGN KEY (data_source_id) REFERENCES csv_link_data_source (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE vk_product');
    }
}
