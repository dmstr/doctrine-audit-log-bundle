<!-- file generated with AI assistance: Claude Code - 2026-06-09 19:27:00 UTC -->

# dmstr/doctrine-audit-log-bundle

Audit logging for Doctrine entities via Gedmo Loggable, exposed as a read-only
API Platform admin resource.

## Features (planned)

- Concrete `LogEntry` entity extending `Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry`
  with table `ext_log_entries`
- `LoggableListener` wiring via `doctrine.event_subscriber`
- `LogEntry` exposed as `#[ApiResource(routePrefix: '/admin', operations: [Get,
  GetCollection], security: "is_granted('ROLE_ADMIN')")]` →
  `/api/admin/log_entries`
- Apps annotate their entities with `#[Gedmo\Loggable]` to opt in

## License

MIT © diemeisterei GmbH
