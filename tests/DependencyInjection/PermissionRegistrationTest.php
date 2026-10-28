<?php
// file generated with AI assistance: Claude Code - 2026-10-28 07:20:00 UTC

declare(strict_types=1);

namespace Dmstr\DoctrineAuditLog\Tests\DependencyInjection;

use ApiPlatform\Metadata\ApiResource;
use Dmstr\DoctrineAuditLog\DoctrineAuditLogBundle;
use Dmstr\DoctrineAuditLog\Entity\LogEntry;
use Dmstr\DoctrineAuditLog\Security\AuditPermissions;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * The bundle announces the permission its resource checks at the za7-core
 * container tag `za7_core.permission`. Loads the bundle extension into a
 * bare container (no kernel, no za7-core) and checks the tag against the
 * security expression of the {@see LogEntry} resource.
 */
final class PermissionRegistrationTest extends TestCase
{
    private const TAG = 'za7_core.permission';

    /**
     * Copy of za7-core's `Permission::PATTERN`; the bundle cannot depend on
     * za7-core, so the format is pinned here.
     */
    private const PERMISSION_PATTERN = '/^[a-z][a-z0-9]*:[a-z][a-z0-9_]*$/';

    public function testExactlyOnePermissionIsRegistered(): void
    {
        $tagged = self::load()->findTaggedServiceIds(self::TAG);

        self::assertCount(1, $tagged);
        self::assertCount(1, array_values($tagged)[0]);
    }

    public function testRegisteredPermissionIsTheOneTheResourceRequires(): void
    {
        $tags = array_values(self::load()->findTaggedServiceIds(self::TAG))[0] ?? [];
        self::assertCount(1, $tags);
        self::assertArrayHasKey('permission', $tags[0]);

        $permission = $tags[0]['permission'];
        self::assertIsString($permission);
        self::assertSame(AuditPermissions::READ, $permission);
        self::assertMatchesRegularExpression(self::PERMISSION_PATTERN, $permission);

        $security = (new \ReflectionClass(LogEntry::class))
            ->getAttributes(ApiResource::class)[0]
            ->newInstance()
            ->getSecurity();
        self::assertIsString($security);
        self::assertSame(1, preg_match('/^is_granted\(\'([^\']+)\'\)$/', $security, $m));
        self::assertSame($m[1], $permission);
    }

    private static function load(): ContainerBuilder
    {
        $builder = new ContainerBuilder();
        $builder->setParameter('kernel.environment', 'test');
        $builder->setParameter('kernel.build_dir', sys_get_temp_dir());

        $extension = (new DoctrineAuditLogBundle())->getContainerExtension();
        self::assertNotNull($extension);
        $extension->load([], $builder);

        return $builder;
    }
}
