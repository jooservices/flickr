# Contributing

Contributions to `jooservices/flickr` should keep the framework-agnostic Flickr SDK aligned with its architecture, quality gates, and contributor guidance.

For development details, see [AGENTS.md](AGENTS.md), [CLAUDE.md](CLAUDE.md), and [docs/04-development/](docs/04-development/).

## Git workflow summary

- `master` is the stable release branch
- `develop` is the integration branch for normal feature and fix work
- create `feature/*` and `fix/*` branches from the latest `develop`, then open the PR back into `develop`
- create `release/<version>` from the latest `develop` for release stabilization, then open the PR into `master`
- after the release PR is merged into `master`, create the release tag and merge `master` back into `develop`
- do not commit directly to `develop` or `master`

## Setup

```bash
composer install
```

## Quality gates

```bash
composer check
composer ci
```

## Testing policy

- Normal tests must not call the real Flickr API
- Real Flickr integration tests stay opt-in behind `FLICKR_REAL_TESTS=true`
