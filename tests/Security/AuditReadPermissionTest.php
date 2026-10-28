<?php
// file generated with AI assistance: Claude Code - 2026-10-28 07:20:00 UTC

declare(strict_types=1);

namespace Dmstr\DoctrineAuditLog\Tests\Security;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Dmstr\DoctrineAuditLog\Entity\LogEntry;
use Dmstr\DoctrineAuditLog\Security\AuditPermissions;
use Dmstr\DoctrineAuditLog\Tests\Fixtures\MapPermissionVoter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authorization\ExpressionLanguage;
use Symfony\Component\Security\Core\User\InMemoryUser;

/**
 * The audit log resource is guarded by the permission
 * {@see AuditPermissions::READ}, not by a role. The expression is read from
 * the {@see ApiResource} attribute of {@see LogEntry} and evaluated the way
 * API Platform does, without a kernel; the za7-core voter is replaced by
 * {@see MapPermissionVoter}.
 */
final class AuditReadPermissionTest extends TestCase
{
    public function testResourceRequiresTheReadPermission(): void
    {
        $security = self::resource()->getSecurity();

        self::assertSame(sprintf("is_granted('%s')", AuditPermissions::READ), $security);
        self::assertSame("is_granted('audit:read')", $security);
    }

    public function testOperationsAreGetAndGetCollectionWithoutOwnSecurity(): void
    {
        $operations = self::resource()->getOperations();
        self::assertNotNull($operations);

        $operations = iterator_to_array($operations, false);
        self::assertCount(2, $operations);

        $classes = array_map(static fn (object $operation): string => $operation::class, $operations);
        sort($classes);
        self::assertSame([Get::class, GetCollection::class], $classes);

        foreach ($operations as $operation) {
            self::assertNull(
                $operation->getSecurity(),
                sprintf('%s must inherit the resource security expression.', $operation::class),
            );
        }
    }

    /**
     * @param array<string, list<string>> $map
     * @param list<string>                $roles
     */
    #[DataProvider('decisions')]
    public function testExpressionDecision(array $map, array $roles, bool $expected): void
    {
        $token = new UsernamePasswordToken(new InMemoryUser('alice', null, $roles), 'test', $roles);

        self::assertSame($expected, self::evaluate($map, $token));
    }

    /**
     * @return iterable<string, array{array<string, list<string>>, list<string>, bool}>
     */
    public static function decisions(): iterable
    {
        yield 'mapped role is allowed' => [['audit:read' => ['ROLE_ADMIN']], ['ROLE_ADMIN'], true];
        yield 'unmapped role is denied' => [['audit:read' => ['ROLE_ADMIN']], ['ROLE_USER'], false];
        yield 'empty map is denied' => [[], ['ROLE_ADMIN'], false];
        yield 'permission without roles is denied' => [['audit:read' => []], ['ROLE_ADMIN'], false];
    }

    public function testDeniedWithoutToken(): void
    {
        self::assertFalse(self::evaluate(['audit:read' => ['ROLE_ADMIN']], null));
    }

    public function testRoleAloneDecidesNothing(): void
    {
        $roles = ['ROLE_ADMIN'];
        $storage = new TokenStorage();
        $storage->setToken(new UsernamePasswordToken(new InMemoryUser('alice', null, $roles), 'test', $roles));

        // The voter abstains on a role attribute; with all voters abstaining
        // the default strategy denies. Only the permission grants access.
        self::assertFalse(self::checker($storage, ['audit:read' => ['ROLE_ADMIN']])->isGranted('ROLE_ADMIN'));
    }

    private static function resource(): ApiResource
    {
        $attributes = (new \ReflectionClass(LogEntry::class))->getAttributes(ApiResource::class);
        self::assertCount(1, $attributes);

        return $attributes[0]->newInstance();
    }

    /**
     * @param array<string, list<string>> $map
     */
    private static function checker(TokenStorage $storage, array $map): AuthorizationChecker
    {
        return new AuthorizationChecker(
            $storage,
            new AccessDecisionManager([new MapPermissionVoter([AuditPermissions::READ], $map)]),
        );
    }

    /**
     * @param array<string, list<string>> $map
     */
    private static function evaluate(array $map, ?TokenInterface $token): bool
    {
        $expression = self::resource()->getSecurity();
        self::assertIsString($expression);

        $storage = new TokenStorage();
        $storage->setToken($token);

        $result = (new ExpressionLanguage())->evaluate($expression, [
            'auth_checker' => self::checker($storage, $map),
            'object' => null,
            'subject' => null,
            'token' => $token,
            'user' => $token?->getUser(),
            'roles' => $token?->getRoleNames() ?? [],
            'trust_resolver' => new AuthenticationTrustResolver(),
        ]);
        self::assertIsBool($result);

        return $result;
    }
}
