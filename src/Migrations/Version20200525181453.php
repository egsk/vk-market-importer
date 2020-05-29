<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200525181453 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE vk_market_category (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE import_target CHANGE category_id vk_market_category_id INT NOT NULL');
        $this->addSql('ALTER TABLE import_target ADD CONSTRAINT FK_3A5E95E4FE6560B5 FOREIGN KEY (vk_market_category_id) REFERENCES vk_market_category (id)');
        $this->addSql('CREATE INDEX IDX_3A5E95E4FE6560B5 ON import_target (vk_market_category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE import_target DROP FOREIGN KEY FK_3A5E95E4FE6560B5');
        $this->addSql('DROP TABLE vk_market_category');
        $this->addSql('DROP INDEX IDX_3A5E95E4FE6560B5 ON import_target');
        $this->addSql('ALTER TABLE import_target CHANGE vk_market_category_id category_id INT NOT NULL');
    }
}
