<?php
// file generated with AI assistance: Claude Code - 2026-06-10 13:00:00 UTC

declare(strict_types=1);

namespace Dmstr\DoctrineAuditLog\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260516000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create ext_log_entries table for Gedmo Loggable';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE ext_log_entries (
                id INT AUTO_INCREMENT NOT NULL,
                action VARCHAR(8) NOT NULL,
                logged_at DATETIME NOT NULL,
                object_id VARCHAR(64) DEFAULT NULL,
                object_class VARCHAR(191) NOT NULL,
                version INT NOT NULL,
                data LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)',
                username VARCHAR(191) DEFAULT NULL,
                INDEX log_class_lookup_idx (object_class),
                INDEX log_date_lookup_idx (logged_at),
                INDEX log_user_lookup_idx (username),
                INDEX log_version_lookup_idx (object_id, object_class, version),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE ext_log_entries');
    }
}
