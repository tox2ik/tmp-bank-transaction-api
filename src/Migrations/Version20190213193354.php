<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190213193354 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE bank_transaction (
          id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, 
          uuid VARCHAR(36) DEFAULT NULL, 
          amount NUMERIC(22, 2) DEFAULT NULL, 
          booking_date DATETIME DEFAULT NULL, 
          INDEX bt_uuid (uuid), 
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bank_transaction_part (
          id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, 
          bank_transaction_id BIGINT UNSIGNED DEFAULT NULL, 
          amount NUMERIC(22, 2) DEFAULT NULL, 
          reason VARCHAR(80) NOT NULL, 
          INDEX btt_transaction_fk (bank_transaction_id), 
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE 
          bank_transaction_part 
        ADD 
          CONSTRAINT FK_A2059A7EB898B7D6 FOREIGN KEY (bank_transaction_id) REFERENCES bank_transaction (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bank_transaction_part DROP FOREIGN KEY FK_A2059A7EB898B7D6');
        $this->addSql('DROP TABLE bank_transaction');
        $this->addSql('DROP TABLE bank_transaction_part');
    }
}
