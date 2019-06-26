<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190626100325 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE transport_offer (id INT AUTO_INCREMENT NOT NULL, transport_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_B4D920C69909C13F (transport_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transport_mode (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE spec_offer (id INT AUTO_INCREMENT NOT NULL, offer_id INT NOT NULL, name VARCHAR(255) NOT NULL, unity VARCHAR(30) NOT NULL, min_value INT NOT NULL, price INT NOT NULL, INDEX IDX_3E2EA79E53C674EE (offer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE transport_offer ADD CONSTRAINT FK_B4D920C69909C13F FOREIGN KEY (transport_id) REFERENCES transport_mode (id)');
        $this->addSql('ALTER TABLE spec_offer ADD CONSTRAINT FK_3E2EA79E53C674EE FOREIGN KEY (offer_id) REFERENCES transport_offer (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE spec_offer DROP FOREIGN KEY FK_3E2EA79E53C674EE');
        $this->addSql('ALTER TABLE transport_offer DROP FOREIGN KEY FK_B4D920C69909C13F');
        $this->addSql('DROP TABLE transport_offer');
        $this->addSql('DROP TABLE transport_mode');
        $this->addSql('DROP TABLE spec_offer');
    }
}
