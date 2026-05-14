# JOOservices Flickr AI Skills

This repository keeps lightweight AI contributor guidance in `AGENTS.md` and `docs/04-development/08-ai-contributor-workflow.md`.

Use this directory as the local entry point when an agent expects repo-level AI skill files. Keep it small unless the repository adopts the fuller `jooservices/dto` skill-pack structure.

Current rules:

- inspect actual source before non-trivial edits
- keep the package framework-agnostic
- use official Flickr docs for API behavior
- keep upload and replace separate from REST calls
- keep raw fallback support for unknown Flickr methods
- follow `jooservices/dto` style for DTO/Data objects
- run repository quality commands before claiming completion
