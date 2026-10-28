# HML gate: social publication operations (PR #12)

**Scope:** isolated MariaDB only. Do not run against `vitrine_social`, studio_app, production workers, scheduler, or Meta credentials. No social HTTP requests are required.

## Preconditions
1. Build a disposable environment from `feature/meta-publication-operations-hml` with its **own** MariaDB database, user, Redis instance (or null queue), and isolated PHP runtime. Do not point to `vitrine_mariadb`.
2. Ensure `APP_ENV=testing`, `QUEUE_CONNECTION=null`, no scheduler or queue worker, no Meta access token, and egress to social APIs blocked.
3. Create a verified snapshot/backup of the disposable database before mutation. Confirm DB hostname and database name in resolved Laravel config; abort if `vitrine_social`.
4. Disable any external side-effect integrations; do not use `composer run setup` (it automatically runs migrations).

## Commands (inside disposable app container)
```sh
php artisan about --only=environment
php artisan migrate:status
php artisan migrate --pretend --path=database/migrations/2026_10_28_000001_create_social_publication_operations_table.php
php artisan migrate --path=database/migrations/2026_10_28_000001_create_social_publication_operations_table.php
php artisan test --filter=SocialPublicationOperation
```

Review `SHOW CREATE TABLE social_publication_operations` and `SHOW CREATE TABLE content_projects`. Confirm both unique keys and foreign key `(content_project_id,client_id)` → `content_projects(id,client_id)`.

## Concurrent claim validation
In a **separate disposable MariaDB** with two independent DB connections, insert a `queued` operation with `attempts=0`; on both connections concurrently run the conditional UPDATE:
```sql
UPDATE social_publication_operations
SET status='publishing', attempts=attempts+1
WHERE id = ? AND client_id = ? AND status='queued' AND attempts=0;
```
Exactly **one** connection must report 1 affected row; the other reports 0, with `attempts=1`. Repeat with `pending_confirmation` and `published`: both must report 0. Do not send HTTP.

Also attempt:
- Duplicate logical identity with different idempotency key → rejected.
- Duplicate idempotency key on different channel → rejected.
- Valid version v2 and different channels → allowed.
- Project from different client or unowned project → rejected.
- Client A querying client B's ID using claim method → rejected.

## Rollback
Use only the disposable database. Check schema and migration ledger before and after rollback. Note that the migration adds a unique constraint on the parent `content_projects`; removing it after dropping operations must not touch project rows. No project records should be deleted.

## Evidence to collect
- Database hostname and name (mask credentials), branch SHA, MariaDB version
- Preflight confirmation of no queues/scheduler/Meta network
- Backup verification result
- Pretend SQL output, migrations, `SHOW CREATE TABLE`, test results
- Two-connection race affected-row counts
- Rollback and data-count comparison
- Egress/queue check and observed publication count = 0

**Gate:** Without this evidence the PR stays draft; no merge, no deployment, no Meta integration.
