# Method Registry

The method registry stores metadata for all 224 methods from the current official Flickr method index: method name, auth requirement, permission, cacheability, HTTP method, and docs URL.

Unknown methods are still allowed and default to unauthenticated, non-cacheable GET calls. This preserves raw fallback support for future Flickr methods.
