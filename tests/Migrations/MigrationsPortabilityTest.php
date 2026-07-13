<?php
// file generated with AI assistance: Claude Code - 2026-07-01 14:30:00 UTC

declare(strict_types=1);

namespace Dmstr\DoctrineAuditLog\Tests\Migrations;

use Dmstr\DoctrineAuditLog\Migrations\Version20260516000001;
use Dmstr\DoctrineAuditLog\Migrations\Version20260713120000;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Schema;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

/**
 * Regression guard: the migration must run on a non-MySQL platform.
 *
 * We execute the real migration class against an in-memory SQLite database
 * (a platform that is neither MySQL nor PostgreSQL). Any MySQL-only DDL
 * (INT AUTO_INCREMENT, LONGTEXT, inline INDEX, ENGINE=InnoDB, …) would make
 * SQLite throw, so a green test proves the DDL is portable — which is what lets
 * the bundle run on PostgreSQL.
 */
final class MigrationsPortabilityTest extends TestCase
{
    private Connection $connection;

    protected function setUp(): void
    {
        $this->connection = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ]);
    }

    public function testMigrationRunsOnNonMysqlPlatform(): void
    {
        $platform = $this->connection->getDatabasePlatform();
        $schema = new Schema();

        $create = new Version20260516000001($this->connection, new NullLogger());
        $create->up($schema);
        foreach ($schema->toSql($platform) as $sql) {
            $this->connection->executeStatement($sql);
        }

        // Version20260713120000 — rename to dmstr_log_entries (portable SQL).
        $vendorPrefix = new Version20260713120000($this->connection, new NullLogger());
        $vendorPrefix->up($schema);
        foreach ($vendorPrefix->getSql() as $query) {
            $this->connection->executeStatement($query->getStatement());
        }

        $sm = $this->connection->createSchemaManager();
        self::assertTrue($sm->tablesExist(['dmstr_log_entries']), 'renamed table exists');
        self::assertFalse($sm->tablesExist(['ext_log_entries']), 'old table name is gone');

        // SQLite quotes reserved-word identifiers (e.g. "action") in the
        // introspected key, so normalise before comparing.
        $columns = array_map(
            static fn (string $name): string => trim($name, '"'),
            array_keys($sm->listTableColumns('dmstr_log_entries'))
        );
        foreach (['id', 'action', 'logged_at', 'object_id', 'object_class', 'version', 'data', 'username'] as $column) {
            self::assertContains($column, $columns, sprintf('column %s exists', $column));
        }

        $indexes = array_keys($sm->listTableIndexes('dmstr_log_entries'));
        foreach (['log_class_lookup_idx', 'log_date_lookup_idx', 'log_user_lookup_idx', 'log_version_lookup_idx'] as $index) {
            self::assertContains($index, $indexes, sprintf('index %s exists', $index));
        }
    }
}
