# AI Contributor Workflow

Start with `AGENTS.md`, inspect the real source, and keep changes scoped to this framework-agnostic SDK. Do not add Laravel service providers, facades, routes, migrations, config publishing, or Artisan commands.

For Flickr behavior, use the official Flickr docs as source of truth. For DTO/Data object style, follow `jooservices/dto` conventions without copying unrelated DTO-library features into this SDK.

Before editing, identify the owning area:

- REST methods: `src/Services`, `src/Contracts/Services`, `src/Metadata/methods.php`
- upload and replace: `src/Client/FlickrUploadClient.php`, `src/Services/UploadService.php`, upload DTOs
- transport: `src/Client/JooClientTransport.php` and client contracts
- DTOs and hydrators: `src/DTO` and `src/Hydrators`
- verification: `tests`, `tests/Fixtures`, `composer verify:registry`, and `composer verify:api-index`

If requirements conflict with repository code, official Flickr docs, package scope, or test safety, stop and ask.
