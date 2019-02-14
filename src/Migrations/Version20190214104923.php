<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190214104923 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE bank_transaction_part_bank_charge (id BIGINT UNSIGNED NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bank_transaction_part_debtor_payback (id BIGINT UNSIGNED NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bank_transaction_part_payment_request (id BIGINT UNSIGNED NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bank_transaction_part_unidentified (id BIGINT UNSIGNED NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bank_transaction_part_bank_charge ADD CONSTRAINT FK_F10BA3A3BF396750 FOREIGN KEY (id) REFERENCES bank_transaction_part (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bank_transaction_part_debtor_payback ADD CONSTRAINT FK_B1F1090CBF396750 FOREIGN KEY (id) REFERENCES bank_transaction_part (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bank_transaction_part_payment_request ADD CONSTRAINT FK_3AE0840DBF396750 FOREIGN KEY (id) REFERENCES bank_transaction_part (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bank_transaction_part_unidentified ADD CONSTRAINT FK_E5B5DDB6BF396750 FOREIGN KEY (id) REFERENCES bank_transaction_part (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bank_transaction_part CHANGE reason reason VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE bank_transaction_part_bank_charge');
        $this->addSql('DROP TABLE bank_transaction_part_debtor_payback');
        $this->addSql('DROP TABLE bank_transaction_part_payment_request');
        $this->addSql('DROP TABLE bank_transaction_part_unidentified');
        $this->addSql('ALTER TABLE bank_transaction_part CHANGE reason reason VARCHAR(80) NOT NULL COLLATE utf8_unicode_ci');
    }
}
