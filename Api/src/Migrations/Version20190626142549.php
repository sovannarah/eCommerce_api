<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190626142549 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_order_item (id INT AUTO_INCREMENT NOT NULL, article_id INT DEFAULT NULL, user_order_id INT DEFAULT NULL, quantity INT UNSIGNED NOT NULL, INDEX IDX_F58DD9E57294869C (article_id), INDEX IDX_F58DD9E56D128938 (user_order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock_order (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, receive DATETIME DEFAULT NULL, send DATETIME NOT NULL, status TINYINT(1) NOT NULL, INDEX IDX_69398F69A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_order (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, receive DATETIME DEFAULT NULL, send DATETIME NOT NULL, status TINYINT(1) NOT NULL, INDEX IDX_17EB68C0A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock_order_item (id INT AUTO_INCREMENT NOT NULL, article_id INT DEFAULT NULL, stock_order_id INT DEFAULT NULL, quantity INT UNSIGNED NOT NULL, INDEX IDX_FCA3E8A57294869C (article_id), INDEX IDX_FCA3E8A5C259397A (stock_order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_order_item ADD CONSTRAINT FK_F58DD9E57294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE user_order_item ADD CONSTRAINT FK_F58DD9E56D128938 FOREIGN KEY (user_order_id) REFERENCES user_order (id)');
        $this->addSql('ALTER TABLE stock_order ADD CONSTRAINT FK_69398F69A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_order ADD CONSTRAINT FK_17EB68C0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE stock_order_item ADD CONSTRAINT FK_FCA3E8A57294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE stock_order_item ADD CONSTRAINT FK_FCA3E8A5C259397A FOREIGN KEY (stock_order_id) REFERENCES stock_order (id)');
        $this->addSql('ALTER TABLE article ADD kg INT NOT NULL, CHANGE nb_views nb_views INT UNSIGNED DEFAULT 0 NOT NULL, CHANGE stock stock INT UNSIGNED NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE stock_order_item DROP FOREIGN KEY FK_FCA3E8A5C259397A');
        $this->addSql('ALTER TABLE user_order_item DROP FOREIGN KEY FK_F58DD9E56D128938');
        $this->addSql('DROP TABLE user_order_item');
        $this->addSql('DROP TABLE stock_order');
        $this->addSql('DROP TABLE user_order');
        $this->addSql('DROP TABLE stock_order_item');
        $this->addSql('ALTER TABLE article DROP kg, CHANGE nb_views nb_views INT DEFAULT 0 NOT NULL, CHANGE stock stock INT DEFAULT NULL');
    }
}
