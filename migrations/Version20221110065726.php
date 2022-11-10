<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221110065726 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE balance (id INT AUTO_INCREMENT NOT NULL, value DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, unit VARCHAR(255) DEFAULT NULL, price DOUBLE PRECISION DEFAULT NULL, subscription TINYINT(1) DEFAULT NULL, quantity DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, service_id INT DEFAULT NULL, resulting_balance_id INT NOT NULL, type_id INT NOT NULL, sum DOUBLE PRECISION NOT NULL, INDEX IDX_723705D1ED5CA9E6 (service_id), INDEX IDX_723705D1D261EAA3 (resulting_balance_id), INDEX IDX_723705D1C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction_type (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1D261EAA3 FOREIGN KEY (resulting_balance_id) REFERENCES balance (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1C54C8C93 FOREIGN KEY (type_id) REFERENCES transaction_type (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1ED5CA9E6');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1D261EAA3');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1C54C8C93');
        $this->addSql('DROP TABLE balance');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE transaction_type');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
