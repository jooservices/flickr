# JOOservices Flickr Repository Instructions

This repository is a PHP 8.5+ package named `jooservices/flickr`.

## Rules

- Inspect source before non-trivial changes.
- Follow `jooservices/dto` style for DTOs.
- Use `jooservices/client` for HTTP transport.
- Do not add Laravel package code: no service provider, facade, routes, migrations, config publishing, or Artisan commands.
- Keep raw API fallback working for unknown Flickr methods.
- Keep upload and replace separate from normal REST API calls.
- Run tests and lints before claiming completion.
- If behavior is unclear, stop and ask.

## Quality Commands

- `composer test`
- `composer lint`
- `composer lint:all`
- `composer check`
