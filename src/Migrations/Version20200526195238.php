<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200526195238 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE csv_link_data_source ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE csv_link_data_source ADD CONSTRAINT FK_12E6F38A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_12E6F38A76ED395 ON csv_link_data_source (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE csv_link_data_source DROP FOREIGN KEY FK_12E6F38A76ED395');
        $this->addSql('DROP INDEX IDX_12E6F38A76ED395 ON csv_link_data_source');
        $this->addSql('ALTER TABLE csv_link_data_source DROP user_id');
    }
}
