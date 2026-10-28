<?php
// file generated with AI assistance: Claude Code - 2026-10-28 07:20:00 UTC

declare(strict_types=1);

namespace Dmstr\DoctrineAuditLog\Security;

/**
 * The permission strings this bundle checks.
 *
 * Each constant is registered at the za7-core container tag
 * `za7_core.permission` in `config/services.yaml`, so the za7-core
 * permission voter knows it. The application maps the permissions to roles;
 * this bundle never names a role itself.
 */
final class AuditPermissions
{
    /**
     * Read access to the audit log resource (`/api/admin/log_entries`).
     */
    public const READ = 'audit:read';

    private function __construct()
    {
    }
}
