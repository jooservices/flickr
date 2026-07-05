# CI/CD

GitHub Actions runs on `master` and `develop` pushes and pull requests. Normal CI sets `FLICKR_REAL_TESTS=false`; real Flickr credentials must never be required for pull request validation.

## Workflows

| Workflow | Purpose |
| --- | --- |
| `ci.yml` | Security audit, lint matrix, tests, 95% coverage gate, Codecov upload |
| `release.yml` | Validates release on tag push; creates GitHub release and triggers Packagist |
| `semantic-pr.yml` | Enforces conventional PR titles |
| `scorecard.yml` | OpenSSF Scorecard analysis |
| `secret-scanning.yml` | Gitleaks placeholder until `GITLEAKS_LICENSE` is configured |
| `pr-labeler.yml` | Auto-labels PRs by changed paths |
| `registry-drift.yml` | Weekly registry verification |

## PR gate

```bash
composer validate --strict
composer install --prefer-dist --no-interaction --no-progress
composer check
```

`composer check` runs `composer lint:all`, `composer verify:registry`, `composer verify:api-index`, and `composer test`.

## Release gate

```bash
composer ci
```

`composer ci` adds `composer test:coverage`. CI enforces a minimum **95%** statement coverage threshold.

## Required secrets

Configure these repository secrets before the first release workflow run:

- `CODECOV_TOKEN`
- `PACKAGIST_USERNAME`
- `PACKAGIST_TOKEN`
