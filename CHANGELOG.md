<!-- file generated with AI assistance: Claude Code - 2026-10-28 07:10:00 UTC -->

# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html). While the major version is 0, a minor release may contain breaking changes; they are marked as BREAKING.

## [0.4.0] - 2026-10-28

### Changed (BREAKING)

- PHP >= 8.5 is required (was >= 8.4).
- The API Platform resource `LogEntry` (`/api/admin/log_entries`) now requires the permission `audit:read` (`security: "is_granted('audit:read')"`) instead of a fixed role check; previously only `ROLE_ADMIN` could read the audit log. The operations `Get` and `GetCollection` carry no security of their own. Without a voter that grants `audit:read`, every request gets 403, administrators included: no voter supports the attribute, and Symfony's default access decision strategy denies when all voters abstain. An application that sets `allow_if_all_abstain` changes that outcome; the denial follows from Symfony's default strategy and is not a guarantee of the bundle.

### Added

- `Dmstr\DoctrineAuditLog\Security\AuditPermissions` with the constant `READ = 'audit:read'`.
- Registration of the permission for the za7-core permission voter via the container tag `{ name: za7_core.permission, permission: 'audit:read' }` in `config/services.yaml`. The bundle does not depend on za7-core; without it the tag is inert. The bundle has no role map and no configuration node; the application maps the permission under `za7_core.permissions`.
- `symfony/yaml ^7.4` declared as runtime dependency; `symfony/security-core ^7.4` and `symfony/expression-language ^7.4` as dev dependencies.
- Tests: reflection test of the resource security expression, evaluation of the expression with a test voter fixture and an example permission map (allowed and denied), and a test that the bundle registers exactly one `za7_core.permission` tag with `audit:read`.

### Migration

1. Deploy the za7-core release that contains the permission voter.
2. Map `audit:read` in `za7_core.permissions` to the role or roles that may read the audit log.
3. Roll out this bundle only together with that za7-core release.

Applications without za7-core need their own voter that grants `audit:read`.

## [0.3.0] - 2026-07-13

### Changed (BREAKING)

- The table `ext_log_entries` is renamed to `dmstr_log_entries`; a portable rename migration is included. Consuming applications must run `doctrine:migrations:migrate`.

### Fixed

- The Gedmo Loggable listener was silently never registered: Symfony 7 dropped support for the `doctrine.event_subscriber` tag. The listener is now tagged per event via `doctrine.event_listener` (`onFlush`, `loadClassMetadata`, `postPersist`).
- Entities declaring `#[Gedmo\Loggable]` as a PHP attribute were not recognized; Gedmo's `AttributeReader` is now wired explicitly.
- Gedmo's default log entry class is not mapped in consuming applications; the bundle's listener now defaults it to the bundle's `LogEntry` entity.

## [0.2.1] - 2026-07-01

### Fixed

- The initial migration uses the portable DBAL Schema API and runs on PostgreSQL.

## [0.2.0] - 2026-06-14

### Added

- PHPUnit unit tests.
- CLI-only Docker Compose test runner (`docker compose run --rm php`).
- GitHub Action running the test suite.

### Changed

- OpenAPI tag of the `LogEntry` resource is "System" (replaces "ZA7 Core").
- PHP >= 8.4 is required.

## [0.1.1] - 2026-06-11

### Added

- OpenAPI tag "ZA7 Core" on the `LogEntry` resource.

## [0.1.0] - 2026-06-10

### Added

- Initial release: `LogEntry` entity, Gedmo Loggable listener wiring, read-only API Platform admin resource at `/api/admin/log_entries`.

[0.4.0]: https://github.com/dmstr/doctrine-audit-log-bundle/compare/0.3.0...0.4.0
[0.3.0]: https://github.com/dmstr/doctrine-audit-log-bundle/compare/0.2.1...0.3.0
[0.2.1]: https://github.com/dmstr/doctrine-audit-log-bundle/compare/0.2.0...0.2.1
[0.2.0]: https://github.com/dmstr/doctrine-audit-log-bundle/compare/0.1.1...0.2.0
[0.1.1]: https://github.com/dmstr/doctrine-audit-log-bundle/compare/0.1.0...0.1.1
[0.1.0]: https://github.com/dmstr/doctrine-audit-log-bundle/releases/tag/0.1.0
