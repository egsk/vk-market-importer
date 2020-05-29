<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200528191940 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE vk_product DROP FOREIGN KEY FK_4CBB869FE6560B5');
        $this->addSql('DROP INDEX IDX_4CBB869FE6560B5 ON vk_product');
        $this->addSql('ALTER TABLE vk_product DROP vk_market_category_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE vk_product ADD vk_market_category_id INT NOT NULL');
        $this->addSql('ALTER TABLE vk_product ADD CONSTRAINT FK_4CBB869FE6560B5 FOREIGN KEY (vk_market_category_id) REFERENCES vk_market_category (id)');
        $this->addSql('CREATE INDEX IDX_4CBB869FE6560B5 ON vk_product (vk_market_category_id)');
    }
}
