<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200713015107 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create `expenses` table and relationships';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql(
            'CREATE TABLE expense(
                    id CHAR(36) NOT NULL PRIMARY KEY,
                    category_id CHAR(36) NOT NULL,
                    user_id CHAR(36) NOT NULL,
                    group_id CHAR(36) DEFAULT NULL,
                    amount DECIMAL(8,2) NOT NULL,
                    description TINYTEXT DEFAULT NULL,
                    created_at DATETIME NOT NULL,
                    updated_at DATETIME NOT NULL,
                    INDEX IDX_expense_category_id (category_id),
                    INDEX IDX_expense_user_id (user_id),
                    INDEX IDX_expense_group_id (group_id),
                    CONSTRAINT FK_expense_category_id FOREIGN KEY (category_id) REFERENCES category (id) ON UPDATE CASCADE ON DELETE CASCADE,
                    CONSTRAINT FK_expense_user_id FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE,
                    CONSTRAINT FK_expense_group_id FOREIGN KEY (group_id) REFERENCES user_group (id) ON UPDATE CASCADE ON DELETE CASCADE
                ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE = InnoDB'
        );

    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE exopense');

    }
}
