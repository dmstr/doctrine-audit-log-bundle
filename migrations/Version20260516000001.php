<?php
// file generated with AI assistance: Claude Code - 2026-07-01 14:30:00 UTC

declare(strict_types=1);

namespace Dmstr\DoctrineAuditLog\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Create the ext_log_entries table for Gedmo Loggable.
 *
 * Written against the DBAL Schema API (not raw platform SQL) so Doctrine emits
 * the correct DDL for whatever platform the consuming app runs on — MySQL,
 * PostgreSQL or SQLite. See MigrationsPortabilityTest.
 */
final class Version20260516000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create ext_log_entries table for Gedmo Loggable';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('ext_log_entries');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('action', 'string', ['length' => 8]);
        $table->addColumn('logged_at', 'datetime');
        $table->addColumn('object_id', 'string', ['length' => 64, 'notnull' => false]);
        $table->addColumn('object_class', 'string', ['length' => 191]);
        $table->addColumn('version', 'integer');
        $table->addColumn('data', 'array', ['notnull' => false]);
        $table->addColumn('username', 'string', ['length' => 191, 'notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['object_class'], 'log_class_lookup_idx');
        $table->addIndex(['logged_at'], 'log_date_lookup_idx');
        $table->addIndex(['username'], 'log_user_lookup_idx');
        $table->addIndex(['object_id', 'object_class', 'version'], 'log_version_lookup_idx');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('ext_log_entries');
    }
}
