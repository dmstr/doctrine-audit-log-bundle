<?php
// file generated with AI assistance: Claude Code - 2026-06-10 11:00:00 UTC

declare(strict_types=1);

namespace Dmstr\DoctrineAuditLog\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model\Operation;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;

/**
 * LogEntry entity for Gedmo Loggable extension.
 *
 * Stores change history for all loggable entities (entities annotated
 * with `#[Gedmo\Loggable]`). Exposed as a read-only API Platform admin
 * resource so audit data is browsable via `/api/admin/log_entries`.
 *
 * Inherits the following columns from AbstractLogEntry:
 * - action: 'create' / 'update' / 'remove'
 * - logged_at: timestamp of the change
 * - object_id: ID of the changed entity
 * - object_class: class name of the changed entity
 * - version: version number
 * - data: array of changed field values
 * - username: user who made the change (if available)
 */
#[ORM\Entity]
#[ORM\Table(name: 'ext_log_entries')]
#[ORM\Index(name: 'log_class_lookup_idx', columns: ['object_class'])]
#[ORM\Index(name: 'log_date_lookup_idx', columns: ['logged_at'])]
#[ORM\Index(name: 'log_user_lookup_idx', columns: ['username'])]
#[ORM\Index(name: 'log_version_lookup_idx', columns: ['object_id', 'object_class', 'version'])]
#[ApiResource(
    routePrefix: '/admin',
    operations: [
        new Get(),
        new GetCollection(),
    ],
    security: "is_granted('ROLE_ADMIN')",
    openapi: new Operation(tags: ['System'])
)]
class LogEntry extends AbstractLogEntry
{
}
