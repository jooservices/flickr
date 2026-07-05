# Security Policy

## Reporting a Vulnerability

If you discover a security vulnerability within this project, please **do not** open a public issue. Instead, report it directly to the maintainers by sending an email to [admin@jooservices.com](mailto:admin@jooservices.com) or using GitHub's private vulnerability reporting feature.

Please include the following details in your report:
- A description of the vulnerability.
- Steps to reproduce the issue (including any proof-of-concept scripts or API payloads).
- The potential impact of the vulnerability.

We will acknowledge receipt of your vulnerability report within 48 hours and work to provide a prompt resolution.

## Plaintext Token Storage warning

By default, the `FileTokenStore` class writes OAuth access tokens to disk in plaintext JSON format.
While this SDK hardens permissions on the written files to `0600` (readable/writable only by the owner process), please note:
- Plaintext storage on disk is not secure against local attackers with root access or backups access.
- For production environments with high security requirements, we strongly recommend implementing a custom `FlickrTokenStoreContract` that uses application-level encryption at rest, or utilizing the `EncryptedTokenStore` decorator with a 32-byte libsodium key, or the `InMemoryTokenStore` paired with a secure secrets manager.
