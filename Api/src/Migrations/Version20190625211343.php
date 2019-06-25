<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190625211343 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_64C19C1727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE specs_offer_price (id INT AUTO_INCREMENT NOT NULL, specoffer_id INT NOT NULL, value INT NOT NULL, price INT NOT NULL, INDEX IDX_CB11BD22B2E71E4A (specoffer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE specs_offer (id INT AUTO_INCREMENT NOT NULL, offer_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, unity VARCHAR(30) NOT NULL, INDEX IDX_7D32638253C674EE (offer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transport_offer (id INT AUTO_INCREMENT NOT NULL, transport_fee_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_B4D920C618BEDB43 (transport_fee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock_order (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, receive DATETIME DEFAULT NULL, send DATETIME NOT NULL, status TINYINT(1) NOT NULL, INDEX IDX_69398F69A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, token_expiration DATETIME DEFAULT NULL, token LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock_order_item (id INT AUTO_INCREMENT NOT NULL, article_id INT DEFAULT NULL, stock_order_id INT DEFAULT NULL, quantity INT UNSIGNED NOT NULL, INDEX IDX_FCA3E8A57294869C (article_id), INDEX IDX_FCA3E8A5C259397A (stock_order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, category_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, price INT UNSIGNED NOT NULL, images LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', nb_views INT UNSIGNED DEFAULT 0 NOT NULL, stock INT UNSIGNED NOT NULL, INDEX IDX_23A0E66A76ED395 (user_id), INDEX IDX_23A0E6612469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_order_item (id INT AUTO_INCREMENT NOT NULL, article_id INT DEFAULT NULL, user_order_id INT DEFAULT NULL, quantity INT UNSIGNED NOT NULL, INDEX IDX_F58DD9E57294869C (article_id), INDEX IDX_F58DD9E56D128938 (user_order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_order (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, receive DATETIME DEFAULT NULL, send DATETIME NOT NULL, status TINYINT(1) NOT NULL, INDEX IDX_17EB68C0A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transport_fee (id INT AUTO_INCREMENT NOT NULL, namw VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1727ACA70 FOREIGN KEY (parent_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE specs_offer_price ADD CONSTRAINT FK_CB11BD22B2E71E4A FOREIGN KEY (specoffer_id) REFERENCES specs_offer (id)');
        $this->addSql('ALTER TABLE specs_offer ADD CONSTRAINT FK_7D32638253C674EE FOREIGN KEY (offer_id) REFERENCES transport_offer (id)');
        $this->addSql('ALTER TABLE transport_offer ADD CONSTRAINT FK_B4D920C618BEDB43 FOREIGN KEY (transport_fee_id) REFERENCES transport_fee (id)');
        $this->addSql('ALTER TABLE stock_order ADD CONSTRAINT FK_69398F69A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE stock_order_item ADD CONSTRAINT FK_FCA3E8A57294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE stock_order_item ADD CONSTRAINT FK_FCA3E8A5C259397A FOREIGN KEY (stock_order_id) REFERENCES stock_order (id)');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E6612469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE user_order_item ADD CONSTRAINT FK_F58DD9E57294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE user_order_item ADD CONSTRAINT FK_F58DD9E56D128938 FOREIGN KEY (user_order_id) REFERENCES user_order (id)');
        $this->addSql('ALTER TABLE user_order ADD CONSTRAINT FK_17EB68C0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1727ACA70');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E6612469DE2');
        $this->addSql('ALTER TABLE specs_offer_price DROP FOREIGN KEY FK_CB11BD22B2E71E4A');
        $this->addSql('ALTER TABLE specs_offer DROP FOREIGN KEY FK_7D32638253C674EE');
        $this->addSql('ALTER TABLE stock_order_item DROP FOREIGN KEY FK_FCA3E8A5C259397A');
        $this->addSql('ALTER TABLE stock_order DROP FOREIGN KEY FK_69398F69A76ED395');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66A76ED395');
        $this->addSql('ALTER TABLE user_order DROP FOREIGN KEY FK_17EB68C0A76ED395');
        $this->addSql('ALTER TABLE stock_order_item DROP FOREIGN KEY FK_FCA3E8A57294869C');
        $this->addSql('ALTER TABLE user_order_item DROP FOREIGN KEY FK_F58DD9E57294869C');
        $this->addSql('ALTER TABLE user_order_item DROP FOREIGN KEY FK_F58DD9E56D128938');
        $this->addSql('ALTER TABLE transport_offer DROP FOREIGN KEY FK_B4D920C618BEDB43');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE specs_offer_price');
        $this->addSql('DROP TABLE specs_offer');
        $this->addSql('DROP TABLE transport_offer');
        $this->addSql('DROP TABLE stock_order');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE stock_order_item');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE user_order_item');
        $this->addSql('DROP TABLE user_order');
        $this->addSql('DROP TABLE transport_fee');
    }
}
