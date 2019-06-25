<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190625130446 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE order_items DROP FOREIGN KEY FK_62809DB0CFFE9AD6');
        $this->addSql('CREATE TABLE variant_article (id INT AUTO_INCREMENT NOT NULL, parent_id INT NOT NULL, INDEX IDX_6D9F2320727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE variant_article ADD CONSTRAINT FK_6D9F2320727ACA70 FOREIGN KEY (parent_id) REFERENCES article (id)');
        $this->addSql('DROP TABLE order_items');
        $this->addSql('DROP TABLE stock_order');
        $this->addSql('ALTER TABLE article ADD variant_of_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66DA873C21 FOREIGN KEY (variant_of_id) REFERENCES article (id)');
        $this->addSql('CREATE INDEX IDX_23A0E66DA873C21 ON article (variant_of_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE order_items (id INT AUTO_INCREMENT NOT NULL, article_id INT DEFAULT NULL, orders_id INT DEFAULT NULL, quantity INT NOT NULL, INDEX IDX_62809DB07294869C (article_id), INDEX IDX_62809DB0CFFE9AD6 (orders_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE stock_order (id INT AUTO_INCREMENT NOT NULL, send DATETIME NOT NULL, recive DATETIME DEFAULT NULL, status TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB07294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB0CFFE9AD6 FOREIGN KEY (orders_id) REFERENCES stock_order (id)');
        $this->addSql('DROP TABLE variant_article');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66DA873C21');
        $this->addSql('DROP INDEX IDX_23A0E66DA873C21 ON article');
        $this->addSql('ALTER TABLE article DROP variant_of_id');
    }
}
