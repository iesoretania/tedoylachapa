<?php

namespace AppBundle\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190207114236 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE invoice ADD finished_on DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice_audit ADD finished_on DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice_line CHANGE `order` order_nr INT NOT NULL');
        $this->addSql('ALTER TABLE invoice_line_audit CHANGE `order` order_nr INT DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE invoice DROP finished_on');
        $this->addSql('ALTER TABLE invoice_audit DROP finished_on');
        $this->addSql('ALTER TABLE invoice_line CHANGE order_nr `order` INT NOT NULL');
        $this->addSql('ALTER TABLE invoice_line_audit CHANGE order_nr `order` INT DEFAULT NULL');
    }
}
