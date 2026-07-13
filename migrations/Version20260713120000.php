<?php
// file generated with AI assistance: Claude Code - 2026-07-13 12:00:00 UTC

declare(strict_types=1);

namespace Dmstr\DoctrineAuditLog\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260713120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename ext_log_entries to dmstr_log_entries (vendor prefix for bundle tables)';
    }

    public function up(Schema $schema): void
    {
        // Portable across MySQL 8+, PostgreSQL and SQLite (unlike the MySQL-only
        // `RENAME TABLE`, which PostgreSQL does not understand).
        $this->addSql('ALTER TABLE ext_log_entries RENAME TO dmstr_log_entries');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE dmstr_log_entries RENAME TO ext_log_entries');
    }
}
