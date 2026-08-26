# Contributing

## Ground rules

1. `knowledge.md` is the design authority; `plan.md` orders delivery. Read both before proposing changes.
2. Branch from `develop`; PR into `develop`. Conventional Commits only (`feat:`, `fix:`, ...).
3. All development runs in Docker:

```bash
make install   # build php:8.5 image + composer install
make shell     # explore
make ci        # validate + verify + lint + coverage(>=85%) + tests
```

## Generated code

`src/Flickr.php`, `src/Services/*Api.php` (except `PhotosApi`, `UploadService`) and
`docs/api-index.md` are generated from frozen manifests:

```bash
composer generate:api-index   # regenerates from resources/*.php
```

Never hand-edit generated files — change the manifests/templates and commit the regenerated diff.
Registry changes require updating provenance (`retrieval date`, hash) and reviewing each touched
method against its official Flickr docs page.

## Tests

Every behaviour change ships with tests: Arch, Unit, Integration suites are strict (no risky/warning/
deprecation). Coverage must stay ≥ 85% statements. Real-network tests need explicit
`FLICKR_REAL_TESTS=1` plus credentials and never run destructive calls without disposable-account approval.
