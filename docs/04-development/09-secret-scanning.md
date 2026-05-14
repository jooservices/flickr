# Secret Scanning

Do not commit Flickr API keys, shared secrets, OAuth request tokens, OAuth access tokens, private user ids tied to credentials, or real response snapshots containing credentials.

Examples and real integration tests must read secrets from environment variables. Normal CI must keep `FLICKR_REAL_TESTS=false`.

Before release or PR handoff, inspect staged changes for accidental credentials:

```bash
git diff --cached
git status --short
```

If a secret is committed, treat it as compromised and rotate it. Do not hide the incident with a normal cleanup commit.
