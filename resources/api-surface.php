<?php

declare(strict_types=1);

/* Frozen API surface: facade accessor → service class → covered registry
 * prefixes/methods. Reviewed together with resources/method-registry.php. */

return [
    'services' => [
        'activity' => ['prefixes' => ['flickr.activity.']],
        'legacyAuth' => ['prefixes' => ['flickr.auth.']],
        'blogs' => ['prefixes' => ['flickr.blogs.']],
        'cameras' => ['prefixes' => ['flickr.cameras.']],
        'collections' => ['prefixes' => ['flickr.collections.']],
        'commons' => ['prefixes' => ['flickr.commons.']],
        'contacts' => ['prefixes' => ['flickr.contacts.']],
        'favorites' => ['prefixes' => ['flickr.favorites.']],
        'galleries' => ['prefixes' => ['flickr.galleries.']],
        'groups' => [
            'prefixes' => ['flickr.groups.'],
            'exclude_prefixes' => ['flickr.groups.discuss.replies.', 'flickr.groups.discuss.topics.', 'flickr.groups.members.', 'flickr.groups.pools.'],
        ],
        'groupsDiscussReplies' => ['prefixes' => ['flickr.groups.discuss.replies.']],
        'groupsDiscussTopics' => ['prefixes' => ['flickr.groups.discuss.topics.']],
        'groupsMembers' => ['prefixes' => ['flickr.groups.members.']],
        'groupsPools' => ['prefixes' => ['flickr.groups.pools.']],
        'interestingness' => ['prefixes' => ['flickr.interestingness.']],
        'machinetags' => ['prefixes' => ['flickr.machinetags.']],
        'panda' => ['prefixes' => ['flickr.panda.']],
        'people' => ['prefixes' => ['flickr.people.']],
        'photos' => [
            'prefixes' => ['flickr.photos.'],
            'exclude_prefixes' => ['flickr.photos.comments.', 'flickr.photos.geo.', 'flickr.photos.licenses.', 'flickr.photos.notes.', 'flickr.photos.people.', 'flickr.photos.suggestions.', 'flickr.photos.transform.'],
            'exclude_methods' => ['flickr.photos.upload.checkTickets'],
            'hand_written' => true,
            'typed' => ['search', 'getInfo', 'getSizes', 'getExif', 'getRecent'],
        ],
        'photosComments' => ['prefixes' => ['flickr.photos.comments.']],
        'photosGeo' => ['prefixes' => ['flickr.photos.geo.']],
        'photosLicenses' => ['prefixes' => ['flickr.photos.licenses.']],
        'photosNotes' => ['prefixes' => ['flickr.photos.notes.']],
        'photosPeople' => ['prefixes' => ['flickr.photos.people.']],
        'photosSuggestions' => ['prefixes' => ['flickr.photos.suggestions.']],
        'photosTransform' => ['prefixes' => ['flickr.photos.transform.']],
        'photosets' => ['prefixes' => ['flickr.photosets.'], 'exclude_prefixes' => ['flickr.photosets.comments.']],
        'photosetsComments' => ['prefixes' => ['flickr.photosets.comments.']],
        'places' => ['prefixes' => ['flickr.places.']],
        'prefs' => ['prefixes' => ['flickr.prefs.']],
        'profile' => ['prefixes' => ['flickr.profile.']],
        'push' => ['prefixes' => ['flickr.push.']],
        'reflection' => ['prefixes' => ['flickr.reflection.']],
        'stats' => ['prefixes' => ['flickr.stats.']],
        'tags' => ['prefixes' => ['flickr.tags.']],
        'test' => ['prefixes' => ['flickr.test.']],
        'testimonials' => ['prefixes' => ['flickr.testimonials.']],
        'urls' => ['prefixes' => ['flickr.urls.']],
        'uploads' => ['prefixes' => [], 'methods' => ['flickr.photos.upload.checkTickets'], 'hand_written' => true, 'facade_accessor' => false],
    ],
];
