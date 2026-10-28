<!-- file generated with AI assistance: Claude Code - 2026-06-09 19:27:00 UTC -->

# dmstr/doctrine-audit-log-bundle

Audit logging for Doctrine entities via Gedmo Loggable, exposed as a read-only API Platform admin resource.

## Purpose

The bundle records the change history of selected Doctrine entities and makes it readable through an API. It provides:

- a concrete `LogEntry` entity (extending Gedmo's `AbstractLogEntry`) mapped to the table `dmstr_log_entries`,
- the Gedmo Loggable listener, registered per Doctrine event via `doctrine.event_listener` (`onFlush`, `loadClassMetadata`, `postPersist`) and wired with Gedmo's `AttributeReader`, see `config/services.yaml`,
- a read-only API Platform resource at `/api/admin/log_entries` (operations `Get` and `GetCollection`, OpenAPI tag "System"), protected by the permission `audit:read`.

## Requirements

- PHP >= 8.5
- Symfony 7.4
- API Platform 4
- Doctrine ORM 3
- `gedmo/doctrine-extensions` 3

## Installation

```bash
composer require dmstr/doctrine-audit-log-bundle
```

Register the bundle in `config/bundles.php` (Symfony Flex does this automatically if a recipe is available):

```php
return [
    // ...
    Dmstr\DoctrineAuditLog\DoctrineAuditLogBundle::class => ['all' => true],
];
```

Then run the database migrations, see [Database migrations](#database-migrations).

## Configuration

The bundle is config-less: it has no configuration node of its own. It prepends its migrations path to the `doctrine_migrations` configuration automatically, so nothing has to be registered by hand. Access to the API resource is controlled by the permission `audit:read`, which the application maps to roles, see [Permission `audit:read`](#permission-auditread).

## Opting entities in

Only entities that carry `#[Gedmo\Loggable]` are logged; within such an entity, only the fields marked with `#[Gedmo\Versioned]` are tracked:

```php
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity]
#[Gedmo\Loggable]
class Article
{
    #[ORM\Column]
    #[Gedmo\Versioned]
    private string $title;

    // ...
}
```

Every insert, update and remove of such an entity writes a versioned row to `dmstr_log_entries`. Entities without the attribute are not logged.

## API resource

| | |
|---|---|
| Path | `/api/admin/log_entries` |
| Operations | `Get`, `GetCollection` (read-only) |
| OpenAPI tag | "System" |
| Security | `is_granted('audit:read')` on the resource |

## Permission `audit:read`

The `LogEntry` resource is secured on resource level with

```php
security: "is_granted('audit:read')"
```

The permission string is available as a constant:

```php
Dmstr\DoctrineAuditLog\Security\AuditPermissions::READ // 'audit:read'
```

The bundle announces the permission to the permission voter of `hrzg/za7-core` with a container tag in `config/services.yaml`:

```yaml
Dmstr\DoctrineAuditLog\Security\AuditPermissions:
    tags:
        - { name: za7_core.permission, permission: 'audit:read' }
```

The tag is a plain string: the bundle does not depend on za7-core and ships no permission map. The application decides which roles may read the audit log by mapping the permission in its za7-core configuration:

```yaml
za7_core:
    permissions:
        audit:read: ["<role that may read the audit log>"]
```

A user who holds one of the mapped roles is granted `audit:read`; everybody else gets 403.

## Running without za7-core

Without za7-core, the `za7_core.permission` tag is inert: nothing reads it, and no voter supports the attribute `audit:read`. Under Symfony's default access decision strategy (`allow_if_all_abstain: false`) a request is denied when all voters abstain, so the resource answers 403 to everyone, administrators included. Such an application needs its own voter that supports `audit:read` and decides who is granted it.

This denial follows from Symfony's default strategy and is not a guarantee of the bundle: an application that sets `allow_if_all_abstain: true` changes the outcome.

## Migrating from 0.3.x

In 0.3.x, the resource required the role `ROLE_ADMIN`. Since 0.4.0 it requires the permission `audit:read`, and the bundle itself grants it to nobody. Without a voter that grants `audit:read`, every request gets 403, administrators included.

1. Deploy the za7-core release that contains the permission voter.
2. Map `audit:read` in `za7_core.permissions` to the role or roles that may read the audit log, as shown above.
3. Roll out this bundle only together with that za7-core release.

Applications without za7-core need their own voter that grants `audit:read`, see [Running without za7-core](#running-without-za7-core). PHP >= 8.5 is required as well (0.3.x required PHP >= 8.4).

## Database migrations

The bundle prepends its own migrations path to the `doctrine_migrations` configuration (see `src/DoctrineAuditLogBundle.php`):

- namespace `Dmstr\DoctrineAuditLog\Migrations`
- directory `migrations/` of the bundle

Run them in the application as usual:

```bash
bin/console doctrine:migrations:migrate
```

| Migration | Effect |
|---|---|
| `Version20260516000001` | creates the log table |
| `Version20260713120000` | renames the former `ext_log_entries` to `dmstr_log_entries` |

The migrations use the portable DBAL Schema API and run on MySQL/MariaDB and PostgreSQL.

## Tests

The bundle ships a CLI-only Docker Compose test runner; no host PHP, no kernel and no database server are required:

```bash
docker compose run --rm php
```

This installs the dependencies and runs `vendor/bin/phpunit`. The same suite runs in CI via the GitHub Action in `.github/workflows/tests.yml`.

The suite covers, among others, the migration portability, the security expression of the `LogEntry` resource (by reflection on the resource attribute and by evaluating it with a test voter fixture and an example permission map), and the registration of the `za7_core.permission` tag with `audit:read`. The security tests use the dev dependencies `symfony/security-core` and `symfony/expression-language`.

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## License

MIT © diemeisterei GmbH
