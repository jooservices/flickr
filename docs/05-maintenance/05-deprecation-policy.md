# Deprecation Policy

This package follows semantic versioning.

## How deprecations work

1. Mark the API with `@deprecated` in PHPDoc, naming the replacement.
2. Document the deprecation in `CHANGELOG.md` under the release that introduces it.
3. Keep the deprecated API through the remainder of the current major version unless it is unsafe.
4. Remove deprecated APIs only in the next major version (for example, remove a v2 deprecation in v3.0.0).

## Soft vs hard breaks

Prefer a deprecation warning period inside a major line when the old API can remain correct.

Ship a major bump without a prior deprecation only when:

- correctness or security requires the change immediately, or
- keeping the old shape would force an incorrect or unsafe default.

Every breaking change in a major release must include a changelog entry, a migration note, and a replacement pattern.
