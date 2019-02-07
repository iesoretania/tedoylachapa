<?php

namespace AppBundle\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190207080359 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE invoice (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, date_time DATETIME NOT NULL, notes LONGTEXT DEFAULT NULL, finalized_on DATETIME DEFAULT NULL, served_on DATETIME DEFAULT NULL, INDEX IDX_90651744B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invoice_audit (id INT NOT NULL, rev INT NOT NULL, created_by_id INT DEFAULT NULL, date_time DATETIME DEFAULT NULL, notes LONGTEXT DEFAULT NULL, finalized_on DATETIME DEFAULT NULL, served_on DATETIME DEFAULT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_6cdde4b41f08fc983373be2501d1d663_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invoice_line (id INT AUTO_INCREMENT NOT NULL, invoice_id INT NOT NULL, reference_id INT DEFAULT NULL, model_id INT DEFAULT NULL, `order` INT NOT NULL, description VARCHAR(255) NOT NULL, quantity INT NOT NULL, rate INT NOT NULL, discount INT NOT NULL, INDEX IDX_D3D1D6932989F1FD (invoice_id), INDEX IDX_D3D1D6931645DEA9 (reference_id), INDEX IDX_D3D1D6937975B7E7 (model_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invoice_line_audit (id INT NOT NULL, rev INT NOT NULL, invoice_id INT DEFAULT NULL, reference_id INT DEFAULT NULL, model_id INT DEFAULT NULL, `order` INT DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, quantity INT DEFAULT NULL, rate INT DEFAULT NULL, discount INT DEFAULT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_3157985996499a867a30c36d76f4aee9_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE invoice_line ADD CONSTRAINT FK_D3D1D6932989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id)');
        $this->addSql('ALTER TABLE invoice_line ADD CONSTRAINT FK_D3D1D6931645DEA9 FOREIGN KEY (reference_id) REFERENCES reference (id)');
        $this->addSql('ALTER TABLE invoice_line ADD CONSTRAINT FK_D3D1D6937975B7E7 FOREIGN KEY (model_id) REFERENCES model (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE invoice_line DROP FOREIGN KEY FK_D3D1D6932989F1FD');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP TABLE invoice_audit');
        $this->addSql('DROP TABLE invoice_line');
        $this->addSql('DROP TABLE invoice_line_audit');
    }
}
