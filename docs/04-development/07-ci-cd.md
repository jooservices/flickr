# CI/CD

GitHub Actions runs on `master` and `develop` pushes and pull requests. Normal CI sets `FLICKR_REAL_TESTS=false`; real Flickr credentials must never be required for pull request validation.

## Default branch and protection

- **Default branch:** `master` (same as `jooservices/dto`)
- **Integration branch:** `develop` — feature and fix PRs target this branch
- **Release branch:** `master` — release and hotfix PRs target this branch

The repository ruleset **`develop & master`** protects both branches and requires:

- pull requests (no direct pushes)
- linear history (no merge commits on protected branches)
- up-to-date branches before merge (`strict_required_status_checks_policy: true`)
- these GitHub Actions checks:
  - `Security Checks`
  - `Lint - Pint`
  - `Lint - PHPCS`
  - `Lint - PHPStan`
  - `Lint - PHPMD`
  - `Lint - PHP-CS-Fixer`
  - `Lint - Registry`
  - `Lint - API Index`
  - `Tests & Coverage`

Optional workflows such as `scorecard.yml`, `secret-scanning.yml`, `semantic-pr.yml`, and `registry-drift.yml` may run on some events but are not part of the required ruleset.

Organization admins can bypass the ruleset when needed (for example, emergency backmerges).

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

`composer ci` adds `composer test:coverage` and `composer verify:smoke`. CI enforces a minimum **95%** statement coverage threshold and runs the consumer smoke test as a lint-matrix job.

## Required secrets

Configure these repository secrets before the first release workflow run:

- `CODECOV_TOKEN`
- `PACKAGIST_USERNAME`
- `PACKAGIST_TOKEN`
