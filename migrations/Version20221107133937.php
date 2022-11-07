<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221107133937 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscriptions DROP FOREIGN KEY FK_4778A01ED5CA9E6');
        $this->addSql('DROP TABLE subscriptions');
        $this->addSql('ALTER TABLE services ADD subscription TINYINT(1) NOT NULL, ADD quantity DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subscriptions (id INT AUTO_INCREMENT NOT NULL, service_id INT NOT NULL, quantity DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_4778A01ED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE subscriptions ADD CONSTRAINT FK_4778A01ED5CA9E6 FOREIGN KEY (service_id) REFERENCES services (id)');
        $this->addSql('ALTER TABLE services DROP subscription, DROP quantity');
    }
}
