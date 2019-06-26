<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190625124555 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE article_stock_order (article_id INT NOT NULL, stock_order_id INT NOT NULL, INDEX IDX_F3BAE50E7294869C (article_id), INDEX IDX_F3BAE50EC259397A (stock_order_id), PRIMARY KEY(article_id, stock_order_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_order (id INT AUTO_INCREMENT NOT NULL, receive DATETIME DEFAULT NULL, send DATETIME NOT NULL, status TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_item (id INT AUTO_INCREMENT NOT NULL, article_id INT DEFAULT NULL, quantity INT UNSIGNED NOT NULL, the_order_id INT DEFAULT NULL, INDEX IDX_52EA1F097294869C (article_id), INDEX IDX_52EA1F09C416F85B (the_order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock_order (id INT AUTO_INCREMENT NOT NULL, receive DATETIME DEFAULT NULL, send DATETIME NOT NULL, status TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE article_stock_order ADD CONSTRAINT FK_F3BAE50E7294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE article_stock_order ADD CONSTRAINT FK_F3BAE50EC259397A FOREIGN KEY (stock_order_id) REFERENCES stock_order (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F097294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE article CHANGE nb_views nb_views INT UNSIGNED DEFAULT 0 NOT NULL, CHANGE stock stock INT UNSIGNED NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE article_stock_order DROP FOREIGN KEY FK_F3BAE50EC259397A');
        $this->addSql('DROP TABLE article_stock_order');
        $this->addSql('DROP TABLE user_order');
        $this->addSql('DROP TABLE order_item');
        $this->addSql('DROP TABLE stock_order');
        $this->addSql('ALTER TABLE article CHANGE nb_views nb_views INT DEFAULT 0, CHANGE stock stock INT DEFAULT NULL');
    }
}
