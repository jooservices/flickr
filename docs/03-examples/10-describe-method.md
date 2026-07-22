# Describe Method Metadata

```php
$info = $flickr->describe('flickr.photos.search');

if ($info !== null) {
    echo $info->name;
    echo $info->httpMethod->value;
    echo $info->docsUrl;
}
```

Unregistered method names return `null`. Raw calls still work for unknown/future Flickr methods; failed calls may include a close-name suggestion in the exception message.
