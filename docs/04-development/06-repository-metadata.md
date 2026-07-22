# Repository Metadata

GitHub About metadata should identify this as a framework-agnostic PHP Flickr SDK.

Suggested description:

```text
Framework-agnostic PHP 8.5+ SDK for Flickr REST API, OAuth 1.0a, upload, replace, typed response helpers, and raw fallback.
```

Suggested topics:

```text
php php85 sdk flickr flickr-api oauth oauth1 upload dto jooservices framework-agnostic api-client
```

Manual update command:

```bash
gh repo edit jooservices/flickr \
  --description "Framework-agnostic PHP 8.5+ SDK for Flickr REST API, OAuth 1.0a, upload, replace, typed response helpers, and raw fallback." \
  --add-topic php \
  --add-topic php85 \
  --add-topic flickr \
  --add-topic flickr-api \
  --add-topic sdk \
  --add-topic oauth \
  --add-topic oauth1 \
  --add-topic upload \
  --add-topic dto \
  --add-topic jooservices \
  --add-topic framework-agnostic \
  --add-topic api-client
```

Do not set a homepage unless there is a real docs site or Packagist page.

## Branch protection (ruleset)

The repository uses a GitHub ruleset named **`develop & master`**, aligned with `jooservices/dto`:

| Setting | Value |
| --- | --- |
| Protected branches | `develop`, `master` |
| Default branch | `master` |
| Direct pushes | blocked (PR required) |
| Required reviews | none (0 approvals) |
| Linear history | required |
| Strict status checks | yes (branch must be up to date) |

View or edit the ruleset at: `https://github.com/jooservices/flickr/rules`

### Differences from `jooservices/dto`

| Item | `jooservices/dto` | `jooservices/flickr` |
| --- | --- | --- |
| Default branch | `master` | `master` |
| Protected branches | `develop`, `master` | `develop`, `master` |
| `GitGuardian Security Checks` | required | not required (not installed on this repo) |
| `Lint - AI Instructions` | required | not applicable (no instruction sync script) |
| `Lint - Registry` | not applicable | required |
| `Lint - API Index` | not applicable | required |

### Stale `main` branch

A legacy `main` branch exists on the remote but is **not** the default branch and is not protected. It is behind `master`. Prefer deleting it after confirming no open PRs target it:

```bash
gh api --method DELETE repos/jooservices/flickr/git/refs/heads/main
```
