<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221107202736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, unit VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, subscription TINYINT(1) NOT NULL, quantity DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, service_id INT NOT NULL, resulting_balance_id INT NOT NULL, sum DOUBLE PRECISION NOT NULL, INDEX IDX_723705D1ED5CA9E6 (service_id), INDEX IDX_723705D1D261EAA3 (resulting_balance_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1D261EAA3 FOREIGN KEY (resulting_balance_id) REFERENCES balance (id)');
        $this->addSql('ALTER TABLE transactions DROP FOREIGN KEY FK_EAA81A4CD261EAA3');
        $this->addSql('ALTER TABLE transactions DROP FOREIGN KEY FK_EAA81A4CED5CA9E6');
        $this->addSql('DROP TABLE services');
        $this->addSql('DROP TABLE transactions');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE services (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, unit VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, price DOUBLE PRECISION NOT NULL, subscription TINYINT(1) NOT NULL, quantity DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE transactions (id INT AUTO_INCREMENT NOT NULL, service_id INT NOT NULL, resulting_balance_id INT NOT NULL, sum DOUBLE PRECISION NOT NULL, INDEX IDX_EAA81A4CED5CA9E6 (service_id), INDEX IDX_EAA81A4CD261EAA3 (resulting_balance_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE transactions ADD CONSTRAINT FK_EAA81A4CD261EAA3 FOREIGN KEY (resulting_balance_id) REFERENCES balance (id)');
        $this->addSql('ALTER TABLE transactions ADD CONSTRAINT FK_EAA81A4CED5CA9E6 FOREIGN KEY (service_id) REFERENCES services (id)');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1ED5CA9E6');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1D261EAA3');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE transaction');
    }
}
