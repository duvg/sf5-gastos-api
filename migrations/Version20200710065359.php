<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200710065359 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create category table and its realtionships';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql(
            'CREATE TABLE category (
                    id CHAR(36) NOT NULL PRIMARY KEY,
                    name VARCHAR(100) NOT NULL,
                    user_id CHAR(36) DEFAULT NULL,
                    group_id CHAR(36) DEFAULT NULL,
                    created_at DATETIME NOT NULL,
                    updated_at DATETIME NOT NULL,
                    INDEX IDX_category_user_id (user_id),
                    INDEX IDX_category_group_id (group_id),
                    CONSTRAINT FK_category_user_id FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE,
                    CONSTRAINT FK_category_group_id FOREIGN KEY (group_id) REFERENCES user_group (id) ON UPDATE CASCADE ON DELETE CASCADE
                ) DEFAULT  CHARACTER SET  utf8mb4 COLLATE utf8mb4_general_ci ENGINE = InnoDB'
        );

    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE category');

    }
}
