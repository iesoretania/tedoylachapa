<?php

namespace AppBundle\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190131111643 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE event_log (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, date_time DATETIME NOT NULL, ip VARCHAR(255) DEFAULT NULL, event VARCHAR(255) NOT NULL, data VARCHAR(255) DEFAULT NULL, data_attachment LONGTEXT DEFAULT NULL, INDEX IDX_9EF0AD16A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, login_username VARCHAR(255) DEFAULT NULL, password VARCHAR(255) DEFAULT NULL, enabled TINYINT(1) NOT NULL, administrator TINYINT(1) NOT NULL, last_access DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649D6FA26E8 (login_username), UNIQUE INDEX UNIQ_8D93D64935C246D5 (password), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_audit (id INT NOT NULL, rev INT NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, login_username VARCHAR(255) DEFAULT NULL, password VARCHAR(255) DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, administrator TINYINT(1) DEFAULT NULL, last_access DATETIME DEFAULT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_e06395edc291d0719bee26fd39a32e8a_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE revisions (id INT AUTO_INCREMENT NOT NULL, timestamp DATETIME NOT NULL, username VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event_log ADD CONSTRAINT FK_9EF0AD16A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE event_log DROP FOREIGN KEY FK_9EF0AD16A76ED395');
        $this->addSql('DROP TABLE event_log');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_audit');
        $this->addSql('DROP TABLE revisions');
    }
}
