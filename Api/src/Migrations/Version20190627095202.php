<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190627095202 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE variant_article (id INT AUTO_INCREMENT NOT NULL, parent_id INT NOT NULL, spec VARCHAR(255) NOT NULL, var_price INT NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_6D9F2320727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE variant_article ADD CONSTRAINT FK_6D9F2320727ACA70 FOREIGN KEY (parent_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE article ADD variant_of_id INT DEFAULT NULL, ADD kg INT NOT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66DA873C21 FOREIGN KEY (variant_of_id) REFERENCES article (id)');
        $this->addSql('CREATE INDEX IDX_23A0E66DA873C21 ON article (variant_of_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE variant_article');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66DA873C21');
        $this->addSql('DROP INDEX IDX_23A0E66DA873C21 ON article');
        $this->addSql('ALTER TABLE article DROP variant_of_id, DROP kg');
    }
}
