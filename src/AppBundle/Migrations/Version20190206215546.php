<?php

namespace AppBundle\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190206215546 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE reference_reception (id INT AUTO_INCREMENT NOT NULL, reference_id INT NOT NULL, added_by VARCHAR(255) NOT NULL, quantity INT NOT NULL, date DATE NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_90B4E3D91645DEA9 (reference_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reference_reception ADD CONSTRAINT FK_90B4E3D91645DEA9 FOREIGN KEY (reference_id) REFERENCES reference (id)');
        $this->addSql('DROP TABLE model_reception');
        $this->addSql('DROP TABLE model_reception_audit');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE model_reception (id INT AUTO_INCREMENT NOT NULL, model_id INT NOT NULL, added_by VARCHAR(255) NOT NULL COLLATE utf8mb4_spanish_ci, quantity INT NOT NULL, date DATE NOT NULL, description VARCHAR(255) NOT NULL COLLATE utf8mb4_spanish_ci, INDEX IDX_691FECBC7975B7E7 (model_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE model_reception_audit (id INT NOT NULL, rev INT NOT NULL, model_id INT DEFAULT NULL, added_by VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_spanish_ci, quantity INT DEFAULT NULL, date DATE DEFAULT NULL, description VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_spanish_ci, revtype VARCHAR(4) NOT NULL COLLATE utf8mb4_spanish_ci, INDEX rev_85bfe3124de6438cadaa4c9696b06289_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE model_reception ADD CONSTRAINT FK_691FECBC7975B7E7 FOREIGN KEY (model_id) REFERENCES model (id)');
        $this->addSql('DROP TABLE reference_reception');
    }
}
