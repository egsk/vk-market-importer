<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200612081332 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("
            CREATE TABLE upload_task
            (
                id                      INT AUTO_INCREMENT NOT NULL,
                user_id                 INT                NOT NULL,
                created_at              DATETIME           NOT NULL,
                completed_at            DATETIME           NOT NULL,
                status                  ENUM (
                    'new',
                    'in_process',
                    'finished'
                    )                                      NOT NULL,
                total_products_count    INT                NOT NULL,
                uploaded_products_count INT                NOT NULL,
                INDEX IDX_D6FA5C5CA76ED395 (user_id),
                PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4
              COLLATE `utf8mb4_unicode_ci`
              ENGINE = InnoDB
        ");
        $this->addSql("
            CREATE TABLE uploaded_product
            (
                id             INT AUTO_INCREMENT NOT NULL,
                upload_task_id INT                NOT NULL,
                status         ENUM (
                    'created',
                    'failed_to_create',
                    'updated',
                    'failed_to_update',
                    'deleted',
                    'failed_to_delete'
                    )                             NOT NULL,
                name           VARCHAR(255)       NOT NULL,
                source_id      VARCHAR(255)       NOT NULL,
                INDEX IDX_67E80E6148811B68 (upload_task_id),
                PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4
              COLLATE `utf8mb4_unicode_ci`
              ENGINE = InnoDB
        ");
        $this->addSql('ALTER TABLE upload_task ADD CONSTRAINT FK_D6FA5C5CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE uploaded_product ADD CONSTRAINT FK_67E80E6148811B68 FOREIGN KEY (upload_task_id) REFERENCES upload_task (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE uploaded_product DROP FOREIGN KEY FK_67E80E6148811B68');
        $this->addSql('DROP TABLE upload_task');
        $this->addSql('DROP TABLE uploaded_product');
    }
}
