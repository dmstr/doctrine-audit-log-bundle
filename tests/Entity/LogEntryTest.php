<?php
// file generated with AI assistance: Claude Code - 2026-06-13 23:14:54 UTC

declare(strict_types=1);

namespace Dmstr\DoctrineAuditLog\Tests\Entity;

use Dmstr\DoctrineAuditLog\Entity\LogEntry;
use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the {@see LogEntry} entity as a plain object — no Doctrine,
 * no database. Confirms it is instantiable and correctly inherits the Gedmo
 * AbstractLogEntry getter/setter surface that the audit-log API exposes.
 */
final class LogEntryTest extends TestCase
{
    public function testIsAGedmoAbstractLogEntry(): void
    {
        self::assertInstanceOf(AbstractLogEntry::class, new LogEntry());
    }

    public function testIdIsNullBeforePersistence(): void
    {
        self::assertNull((new LogEntry())->getId());
    }

    public function testInheritedFieldsRoundTrip(): void
    {
        $entry = new LogEntry();
        $entry->setAction('update');
        $entry->setObjectId('5302d197-...');
        $entry->setObjectClass('Herzog\\Entity\\Customer');
        $entry->setVersion(3);
        $entry->setData(['name' => 'ACME']);
        $entry->setUsername('alice');

        self::assertSame('update', $entry->getAction());
        self::assertSame('5302d197-...', $entry->getObjectId());
        self::assertSame('Herzog\\Entity\\Customer', $entry->getObjectClass());
        self::assertSame(3, $entry->getVersion());
        self::assertSame(['name' => 'ACME'], $entry->getData());
        self::assertSame('alice', $entry->getUsername());
    }
}
