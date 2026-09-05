# Audit remediation

This change addresses the runtime and CI defects identified against commit
`3cffe21` in the 2026-09-04 audit. It does not certify full historical or
architectural compliance.

| Finding | Implementation |
| --- | --- |
| Upload signature drift | Multipart field content is transmitted unchanged. |
| Credential exposure | Configuration credentials survive request-history eviction; active entries refresh; raw/encoded verifiers and JSON/XML error text are redacted. |
| Cache collision | Failed JSON key serialization throws before cache access. |
| Invalid polling outcome | API failure envelopes raise typed exceptions before ticket interpretation. |
| Multipart contract | String conversion rewinds; close/detach update capabilities and ownership correctly. |
| OAuth HTTP failures | Non-success responses are rejected before token fields are consumed; 429 preserves retry information. |
| Sonar enforcement | Analysis waits for a gate result in the required CI job; bot analysis is no longer skipped. |
| Generated drift | Verification compares all generated output without writing files. |
| Test/static-analysis gaps | New regressions use Faker; validated resource inputs no longer need PHPStan ignores. |
| Commit policy | New PR commits have author/committer validation; merge subjects are no longer ignored. |
| README | ClientBuilder imports resolve against the installed dependency and are tested. |
| PHP constraint | Composer and project instructions require `^8.5`. |

Redaction now uses a replacement map instead of scanning the body separately
for every tracked secret. Polling rechecks the deadline after an HTTP call
and caps the subsequent sleep to the remaining time. Its clock still has
seconds resolution, and HTTP timeouts remain configured in client v4.

## Remaining scope

- Historical GitHub-committed merges still violate workspace identity and
  subject requirements. Repair would rewrite shared history and is not part
  of this forward change.
- Existing PHPMD exclusions and the UploadService constructor suppression
  remain. Some cover the documented explicit facade, static factories and
  boolean DTO fields; removing all of them requires an API/design review.
  They are not newly approved exceptions, and the project's literal
  no-ignore compliance finding remains open.
- Legacy tests still contain fabricated literals. The new regression data
  uses Faker; a complete legacy test-data migration remains open.
- Sonar credentials for fork/Dependabot execution must be available through
  the appropriate trusted configuration; absence fails the gate rather than
  bypassing it. This change does not expose secrets to untrusted PR code via
  a privileged workflow trigger.

No Flickr network mutations are performed by the regression suite. Tests use
fake responses, Faker-generated values, and disposable generator copies.
