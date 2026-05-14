# Code Style

Pint is the formatting authority. If Pint and another formatter disagree, Pint wins.

Keep DTOs constructor-driven and aligned with `jooservices/dto` style. Services should stay small, translate friendly method calls to raw Flickr method calls, and leave transport behind contracts.

Prefer enums or constants for Flickr domain values. Use public DTOs where they improve user-facing workflows, but keep the raw API fallback array-based so unknown future Flickr methods remain callable.
