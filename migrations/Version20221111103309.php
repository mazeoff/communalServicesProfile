<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221111103309 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction ADD result_balance DOUBLE PRECISION DEFAULT NULL, ADD datetime DATETIME DEFAULT NULL, CHANGE resulting_balance_id balance_id INT NOT NULL');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1AE91A3DD FOREIGN KEY (balance_id) REFERENCES balance (id)');
        $this->addSql('CREATE INDEX IDX_723705D1AE91A3DD ON transaction (balance_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1AE91A3DD');
        $this->addSql('DROP INDEX IDX_723705D1AE91A3DD ON transaction');
        $this->addSql('ALTER TABLE transaction DROP result_balance, DROP datetime, CHANGE balance_id resulting_balance_id INT NOT NULL');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1D261EAA3 FOREIGN KEY (resulting_balance_id) REFERENCES balance (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_723705D1D261EAA3 ON transaction (resulting_balance_id)');
    }
}
