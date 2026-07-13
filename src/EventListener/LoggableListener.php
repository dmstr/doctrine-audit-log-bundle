<?php
// file generated with AI assistance: Claude Code - 2026-07-13 12:00:00 UTC

declare(strict_types=1);

namespace Dmstr\DoctrineAuditLog\EventListener;

use Dmstr\DoctrineAuditLog\Entity\LogEntry;
use Gedmo\Loggable\LoggableListener as GedmoLoggableListener;
use Gedmo\Loggable\Mapping\Event\LoggableAdapter;

/**
 * Loggable listener that logs into the bundle's own {@see LogEntry} entity.
 *
 * Gedmo's listener defaults to `Gedmo\Loggable\Entity\LogEntry`, whose ORM
 * mapping is not registered in consuming applications — only the bundle's
 * entity (table `dmstr_log_entries`) is. Without this override, log writes
 * would target an unmapped entity class.
 *
 * An explicit `logEntryClass` on a `#[Gedmo\Loggable]` attribute still wins.
 */
final class LoggableListener extends GedmoLoggableListener
{
    protected function getLogEntryClass(LoggableAdapter $ea, $class)
    {
        return self::$configurations[$this->name][$class]['logEntryClass'] ?? LogEntry::class;
    }
}
