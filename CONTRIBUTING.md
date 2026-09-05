# Contributing

## Ground rules

1. Read [`README.md`](README.md) and [`AGENTS.md`](AGENTS.md) before proposing changes. Architecture and scope live there — not in archived rebuild specs.
2. Branch from `develop`; PR into `develop`. Conventional Commits only (`feat:`, `fix:`, ...). Subject starts with an uppercase letter.
3. All development runs in Docker:

```bash
make install   # build php:8.5 image + composer install
make shell     # explore
make ci        # validate + verify + lint + coverage(>=85%) + tests
```

4. Install local git hooks after `make install` (CaptainHook via Docker). Never use `--no-verify`:

```bash
tools/install-git-hooks
```

Hooks enforce Conventional Commits (`commit-msg`), lint (`pre-commit`), and tests (`pre-push`).

## Generated code

`src/Flickr.php`, `src/Services/*Api.php` (except `PhotosApi`, `UploadService`) and
`docs/api-index.md` are generated from frozen manifests:

```bash
composer generate:api-index   # regenerates from resources/*.php
```

Never hand-edit generated files — change the manifests/templates and commit the regenerated diff.
`composer verify:api-surface` checks every generated wrapper, the facade and
the index without rewriting them; stale or missing output fails verification.
Registry changes require updating provenance (`retrieval date`, hash) and reviewing each touched
method against its official Flickr docs page.

## Tests

Every behaviour change ships with tests: Arch, Unit, Integration suites are strict (no risky/warning/
deprecation). Coverage must stay ≥ 85% statements. Real-network tests need explicit
`FLICKR_REAL_TESTS=1` plus credentials and never run destructive calls without disposable-account approval.

Use Faker for fabricated credentials and business data. Fixed protocol vectors,
boundary values and expected wire syntax remain explicit test inputs.

Every new commit must resolve both author and committer to
`Viet Vu <jooservices@gmail.com>`. GitHub's generated merge committer does not
satisfy that workspace requirement. The PR commit check prevents new head
commits with other identities; maintainers must also preserve the identity
when completing a protected-branch merge. Do not rewrite shared history to
repair historical violations as part of an ordinary fix PR.
