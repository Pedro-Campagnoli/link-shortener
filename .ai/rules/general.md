---
paths:
  - '**'
  - docker-compose.yml
---

# General

## Backend only — the front-end is a separate project
React, Inertia, Vite, Tailwind and npm were deliberately removed from this repo (2026-08-10). There is no package.json, no `resources/js`, and no root Blade view. The front-end is a separate project that talks to this API over HTTP.

Consequences: do not add npm dependencies, Blade views or `@vite` directives here. `DevCommands::except('vite')` in AppServiceProvider keeps `composer run dev` from starting Vite. Cross-origin access is controlled by `FRONTEND_URL` in `.env`, read by `config/cors.php`.

## Postgres runs in Docker; the test database is created by an inline config
Only the database is containerised — PHP runs on the host via `composer run dev`. Start it with `docker compose up -d --wait` (also part of `composer setup`).

Two traps:
- Postgres 18+ needs the volume mounted at `/var/lib/postgresql`, NOT `/var/lib/postgresql/data`. The old path makes the container crash-loop on boot.
- `link_shortener_testing` (used by phpunit.xml) is created by the inline `configs:` block, which only runs on first volume init. After `docker compose down -v` it is recreated; if you ever create the volume by other means, create that database by hand or tests will fail to connect.
