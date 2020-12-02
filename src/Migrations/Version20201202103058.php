<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201202103058 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE app_wishlist (id INT AUTO_INCREMENT NOT NULL, customer_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, INDEX IDX_94614A9C9395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wishlists_products (wishlist_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_727B85F5FB8E54CD (wishlist_id), INDEX IDX_727B85F54584665A (product_id), PRIMARY KEY(wishlist_id, product_id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE app_wishlist ADD CONSTRAINT FK_94614A9C9395C3F3 FOREIGN KEY (customer_id) REFERENCES sylius_customer (id)');
        $this->addSql('ALTER TABLE wishlists_products ADD CONSTRAINT FK_727B85F5FB8E54CD FOREIGN KEY (wishlist_id) REFERENCES app_wishlist (id)');
        $this->addSql('ALTER TABLE wishlists_products ADD CONSTRAINT FK_727B85F54584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE wishlists_products DROP FOREIGN KEY FK_727B85F5FB8E54CD');
        $this->addSql('DROP TABLE app_wishlist');
        $this->addSql('DROP TABLE wishlists_products');
    }
}
