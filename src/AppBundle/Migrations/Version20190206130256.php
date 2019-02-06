<?php

namespace AppBundle\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190206130256 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE model (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, image LONGBLOB DEFAULT NULL, active TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_D79572D977153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE model_audit (id INT NOT NULL, rev INT NOT NULL, code VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, image LONGBLOB DEFAULT NULL, active TINYINT(1) DEFAULT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_2ed4bb7c75f58a51a5ff0e8d1e35ca5b_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci ENGINE = InnoDB');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE model');
        $this->addSql('DROP TABLE model_audit');
    }
}
