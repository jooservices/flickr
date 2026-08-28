# Security Policy

## Supported versions

| Version | Supported |
| --- | --- |
| 4.1.x | ✅ (current) |
| 4.0.x | ✅ |
| < 4.0 | ❌ (no backward compatibility) |

## Reporting a vulnerability

Email **jooservices@gmail.com** or open a private GitHub security advisory on
`jooservices/flickr`. Do not open public issues for vulnerabilities.

Include: affected version/commit, reproduction steps, impact assessment.
You will receive an acknowledgment within 72 hours.

## Scope notes for this SDK

- Secrets (API key/secret, OAuth token secret, verifier, signature) are redacted
  from SDK exception messages; reports about leakage through any other channel
  (cache keys, fakes, logs) are in scope.
- Endpoint tampering / SSRF via configuration is out of scope by design:
  endpoints are final package constants and per-request redirects are disabled.
- Consumer-side obligations (Flickr attribution, 30-photo page limit, privacy
  removals) are documented in README; misuse reports are documentation bugs,
  not security issues.
