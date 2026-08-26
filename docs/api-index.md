# Flickr SDK v4 API index

Generated from resources/method-registry.php + resources/api-surface.php.

## `activity()` → ActivityApi

- `userComments()` → `flickr.activity.userComments` [GET, read]
- `userPhotos()` → `flickr.activity.userPhotos` [GET, read]

## `legacyAuth()` → LegacyAuthApi

- `checkToken()` → `flickr.auth.checkToken` [GET, none, unavailable]
- `getFrob()` → `flickr.auth.getFrob` [GET, none, unavailable]
- `getFullToken()` → `flickr.auth.getFullToken` [GET, none, unavailable]
- `getToken()` → `flickr.auth.getToken` [GET, none, unavailable]
- `oauthCheckToken()` → `flickr.auth.oauth.checkToken` [GET, none, unavailable]
- `getAccessToken()` → `flickr.auth.oauth.getAccessToken` [GET, none, unavailable]

## `blogs()` → BlogsApi

- `getList()` → `flickr.blogs.getList` [GET, read]
- `getServices()` → `flickr.blogs.getServices` [GET, none]
- `postPhoto()` → `flickr.blogs.postPhoto` [POST, write]

## `cameras()` → CamerasApi

- `getBrandModels()` → `flickr.cameras.getBrandModels` [GET, none]
- `getBrands()` → `flickr.cameras.getBrands` [GET, none]

## `collections()` → CollectionsApi

- `getInfo()` → `flickr.collections.getInfo` [GET, read]
- `getTree()` → `flickr.collections.getTree` [GET, none]

## `commons()` → CommonsApi

- `getInstitutions()` → `flickr.commons.getInstitutions` [GET, none]

## `contacts()` → ContactsApi

- `getList()` → `flickr.contacts.getList` [GET, read]
- `getListRecentlyUploaded()` → `flickr.contacts.getListRecentlyUploaded` [GET, read]
- `getPublicList()` → `flickr.contacts.getPublicList` [GET, none]
- `getTaggingSuggestions()` → `flickr.contacts.getTaggingSuggestions` [GET, read]

## `favorites()` → FavoritesApi

- `add()` → `flickr.favorites.add` [POST, write]
- `getContext()` → `flickr.favorites.getContext` [GET, none]
- `getList()` → `flickr.favorites.getList` [GET, none]
- `getPublicList()` → `flickr.favorites.getPublicList` [GET, none]
- `remove()` → `flickr.favorites.remove` [POST, write]

## `galleries()` → GalleriesApi

- `addPhoto()` → `flickr.galleries.addPhoto` [POST, write]
- `create()` → `flickr.galleries.create` [POST, write]
- `editMeta()` → `flickr.galleries.editMeta` [POST, write]
- `editPhoto()` → `flickr.galleries.editPhoto` [POST, write]
- `editPhotos()` → `flickr.galleries.editPhotos` [POST, write]
- `getInfo()` → `flickr.galleries.getInfo` [GET, none]
- `getList()` → `flickr.galleries.getList` [GET, none]
- `getListForPhoto()` → `flickr.galleries.getListForPhoto` [GET, none]
- `getPhotos()` → `flickr.galleries.getPhotos` [GET, none]
- `removePhoto()` → `flickr.galleries.removePhoto` [POST, write]

## `groups()` → GroupsApi

- `getInfo()` → `flickr.groups.getInfo` [GET, none]
- `join()` → `flickr.groups.join` [POST, write]
- `joinRequest()` → `flickr.groups.joinRequest` [POST, write]
- `leave()` → `flickr.groups.leave` [POST, delete]
- `search()` → `flickr.groups.search` [GET, none]

## `groupsDiscussReplies()` → GroupsDiscussRepliesApi

- `add()` → `flickr.groups.discuss.replies.add` [POST, write]
- `delete()` → `flickr.groups.discuss.replies.delete` [POST, delete]
- `edit()` → `flickr.groups.discuss.replies.edit` [POST, write]
- `getInfo()` → `flickr.groups.discuss.replies.getInfo` [GET, none]
- `getList()` → `flickr.groups.discuss.replies.getList` [GET, none]

## `groupsDiscussTopics()` → GroupsDiscussTopicsApi

- `add()` → `flickr.groups.discuss.topics.add` [POST, write]
- `getInfo()` → `flickr.groups.discuss.topics.getInfo` [GET, none]
- `getList()` → `flickr.groups.discuss.topics.getList` [GET, none]

## `groupsMembers()` → GroupsMembersApi

- `getList()` → `flickr.groups.members.getList` [GET, read]

## `groupsPools()` → GroupsPoolsApi

- `add()` → `flickr.groups.pools.add` [POST, write]
- `getContext()` → `flickr.groups.pools.getContext` [GET, none]
- `getGroups()` → `flickr.groups.pools.getGroups` [GET, read]
- `getPhotos()` → `flickr.groups.pools.getPhotos` [GET, none]
- `remove()` → `flickr.groups.pools.remove` [POST, write]

## `interestingness()` → InterestingnessApi

- `getList()` → `flickr.interestingness.getList` [GET, none]

## `machinetags()` → MachinetagsApi

- `getNamespaces()` → `flickr.machinetags.getNamespaces` [GET, none]
- `getPairs()` → `flickr.machinetags.getPairs` [GET, none]
- `getPredicates()` → `flickr.machinetags.getPredicates` [GET, none]
- `getRecentValues()` → `flickr.machinetags.getRecentValues` [GET, none]
- `getValues()` → `flickr.machinetags.getValues` [GET, none]

## `panda()` → PandaApi

- `getList()` → `flickr.panda.getList` [GET, none, unavailable]
- `getPhotos()` → `flickr.panda.getPhotos` [GET, none, unavailable]

## `people()` → PeopleApi

- `findByEmail()` → `flickr.people.findByEmail` [GET, none]
- `findByUsername()` → `flickr.people.findByUsername` [GET, none]
- `getGroups()` → `flickr.people.getGroups` [GET, read]
- `getInfo()` → `flickr.people.getInfo` [GET, none]
- `getLimits()` → `flickr.people.getLimits` [GET, read]
- `getPhotos()` → `flickr.people.getPhotos` [GET, none]
- `getPhotosOf()` → `flickr.people.getPhotosOf` [GET, none]
- `getPublicGroups()` → `flickr.people.getPublicGroups` [GET, none]
- `getPublicPhotos()` → `flickr.people.getPublicPhotos` [GET, none]
- `getUploadStatus()` → `flickr.people.getUploadStatus` [GET, read]

## `photos()` → PhotosApi

- `addTags()` → `flickr.photos.addTags` [POST, write]
- `delete()` → `flickr.photos.delete` [POST, delete]
- `getAllContexts()` → `flickr.photos.getAllContexts` [GET, none]
- `getContactsPhotos()` → `flickr.photos.getContactsPhotos` [GET, read]
- `getContactsPublicPhotos()` → `flickr.photos.getContactsPublicPhotos` [GET, none]
- `getContext()` → `flickr.photos.getContext` [GET, none]
- `getCounts()` → `flickr.photos.getCounts` [GET, read]
- `getExif()` → `flickr.photos.getExif` [GET, none]
- `getFavorites()` → `flickr.photos.getFavorites` [GET, none]
- `getInfo()` → `flickr.photos.getInfo` [GET, none]
- `getNotInSet()` → `flickr.photos.getNotInSet` [GET, read]
- `getPerms()` → `flickr.photos.getPerms` [GET, read]
- `getPopular()` → `flickr.photos.getPopular` [GET, none]
- `getRecent()` → `flickr.photos.getRecent` [GET, none]
- `getSizes()` → `flickr.photos.getSizes` [GET, none]
- `getUntagged()` → `flickr.photos.getUntagged` [GET, read]
- `getWithGeoData()` → `flickr.photos.getWithGeoData` [GET, read]
- `getWithoutGeoData()` → `flickr.photos.getWithoutGeoData` [GET, read]
- `recentlyUpdated()` → `flickr.photos.recentlyUpdated` [GET, read]
- `removeTag()` → `flickr.photos.removeTag` [POST, write]
- `search()` → `flickr.photos.search` [GET, none]
- `setContentType()` → `flickr.photos.setContentType` [POST, write]
- `setDates()` → `flickr.photos.setDates` [POST, write]
- `setMeta()` → `flickr.photos.setMeta` [POST, write]
- `setPerms()` → `flickr.photos.setPerms` [POST, write]
- `setSafetyLevel()` → `flickr.photos.setSafetyLevel` [POST, write]
- `setTags()` → `flickr.photos.setTags` [POST, write]

## `photosComments()` → PhotosCommentsApi

- `addComment()` → `flickr.photos.comments.addComment` [POST, write]
- `deleteComment()` → `flickr.photos.comments.deleteComment` [POST, write]
- `editComment()` → `flickr.photos.comments.editComment` [POST, write]
- `getList()` → `flickr.photos.comments.getList` [GET, none]
- `getRecentForContacts()` → `flickr.photos.comments.getRecentForContacts` [GET, read]

## `photosGeo()` → PhotosGeoApi

- `batchCorrectLocation()` → `flickr.photos.geo.batchCorrectLocation` [POST, write]
- `correctLocation()` → `flickr.photos.geo.correctLocation` [POST, write]
- `getLocation()` → `flickr.photos.geo.getLocation` [GET, none]
- `getPerms()` → `flickr.photos.geo.getPerms` [GET, read]
- `photosForLocation()` → `flickr.photos.geo.photosForLocation` [GET, read]
- `removeLocation()` → `flickr.photos.geo.removeLocation` [POST, write]
- `setContext()` → `flickr.photos.geo.setContext` [POST, write]
- `setLocation()` → `flickr.photos.geo.setLocation` [POST, write]
- `setPerms()` → `flickr.photos.geo.setPerms` [POST, write]

## `photosLicenses()` → PhotosLicensesApi

- `getAvailable()` → `flickr.photos.licenses.getAvailable` [GET, none]
- `getInfo()` → `flickr.photos.licenses.getInfo` [GET, none]
- `getLicenseHistory()` → `flickr.photos.licenses.getLicenseHistory` [GET, none]
- `setLicense()` → `flickr.photos.licenses.setLicense` [POST, write]

## `photosNotes()` → PhotosNotesApi

- `add()` → `flickr.photos.notes.add` [POST, write]
- `delete()` → `flickr.photos.notes.delete` [POST, write]
- `edit()` → `flickr.photos.notes.edit` [POST, write]

## `photosPeople()` → PhotosPeopleApi

- `add()` → `flickr.photos.people.add` [POST, write]
- `delete()` → `flickr.photos.people.delete` [POST, write]
- `deleteCoords()` → `flickr.photos.people.deleteCoords` [POST, write]
- `editCoords()` → `flickr.photos.people.editCoords` [POST, write]
- `getList()` → `flickr.photos.people.getList` [GET, none]

## `photosSuggestions()` → PhotosSuggestionsApi

- `approveSuggestion()` → `flickr.photos.suggestions.approveSuggestion` [POST, write]
- `getList()` → `flickr.photos.suggestions.getList` [GET, read]
- `rejectSuggestion()` → `flickr.photos.suggestions.rejectSuggestion` [POST, write]
- `removeSuggestion()` → `flickr.photos.suggestions.removeSuggestion` [POST, write]
- `suggestLocation()` → `flickr.photos.suggestions.suggestLocation` [POST, write]

## `photosTransform()` → PhotosTransformApi

- `rotate()` → `flickr.photos.transform.rotate` [POST, write]

## `photosets()` → PhotosetsApi

- `addPhoto()` → `flickr.photosets.addPhoto` [POST, write]
- `create()` → `flickr.photosets.create` [POST, write]
- `delete()` → `flickr.photosets.delete` [POST, write]
- `editMeta()` → `flickr.photosets.editMeta` [POST, write]
- `editPhotos()` → `flickr.photosets.editPhotos` [POST, write]
- `getContext()` → `flickr.photosets.getContext` [GET, none]
- `getInfo()` → `flickr.photosets.getInfo` [GET, none]
- `getList()` → `flickr.photosets.getList` [GET, none]
- `getPhotos()` → `flickr.photosets.getPhotos` [GET, none]
- `orderSets()` → `flickr.photosets.orderSets` [POST, write]
- `removePhoto()` → `flickr.photosets.removePhoto` [POST, write]
- `removePhotos()` → `flickr.photosets.removePhotos` [POST, write]
- `reorderPhotos()` → `flickr.photosets.reorderPhotos` [POST, write]
- `setPrimaryPhoto()` → `flickr.photosets.setPrimaryPhoto` [POST, write]

## `photosetsComments()` → PhotosetsCommentsApi

- `addComment()` → `flickr.photosets.comments.addComment` [POST, write]
- `deleteComment()` → `flickr.photosets.comments.deleteComment` [POST, write]
- `editComment()` → `flickr.photosets.comments.editComment` [POST, write]
- `getList()` → `flickr.photosets.comments.getList` [GET, none]

## `places()` → PlacesApi

- `find()` → `flickr.places.find` [GET, none]
- `findByLatLon()` → `flickr.places.findByLatLon` [GET, none]
- `getChildrenWithPhotosPublic()` → `flickr.places.getChildrenWithPhotosPublic` [GET, none]
- `getInfo()` → `flickr.places.getInfo` [GET, none]
- `getInfoByUrl()` → `flickr.places.getInfoByUrl` [GET, none]
- `getPlaceTypes()` → `flickr.places.getPlaceTypes` [GET, none]
- `getShapeHistory()` → `flickr.places.getShapeHistory` [GET, none]
- `getTopPlacesList()` → `flickr.places.getTopPlacesList` [GET, none]
- `placesForBoundingBox()` → `flickr.places.placesForBoundingBox` [GET, none]
- `placesForContacts()` → `flickr.places.placesForContacts` [GET, read]
- `placesForTags()` → `flickr.places.placesForTags` [GET, none]
- `placesForUser()` → `flickr.places.placesForUser` [GET, read]
- `resolvePlaceId()` → `flickr.places.resolvePlaceId` [GET, none]
- `resolvePlaceURL()` → `flickr.places.resolvePlaceURL` [GET, none]
- `tagsForPlace()` → `flickr.places.tagsForPlace` [GET, none]

## `prefs()` → PrefsApi

- `getContentType()` → `flickr.prefs.getContentType` [GET, read]
- `getGeoPerms()` → `flickr.prefs.getGeoPerms` [GET, read]
- `getHidden()` → `flickr.prefs.getHidden` [GET, read]
- `getPrivacy()` → `flickr.prefs.getPrivacy` [GET, read]
- `getSafetyLevel()` → `flickr.prefs.getSafetyLevel` [GET, read]

## `profile()` → ProfileApi

- `getProfile()` → `flickr.profile.getProfile` [GET, none]

## `push()` → PushApi

- `getSubscriptions()` → `flickr.push.getSubscriptions` [GET, read]
- `getTopics()` → `flickr.push.getTopics` [GET, none]
- `subscribe()` → `flickr.push.subscribe` [GET, read]
- `unsubscribe()` → `flickr.push.unsubscribe` [GET, read]

## `reflection()` → ReflectionApi

- `getMethodInfo()` → `flickr.reflection.getMethodInfo` [GET, none]
- `getMethods()` → `flickr.reflection.getMethods` [GET, none]

## `stats()` → StatsApi

- `getCSVFiles()` → `flickr.stats.getCSVFiles` [GET, read]
- `getCollectionDomains()` → `flickr.stats.getCollectionDomains` [GET, read]
- `getCollectionReferrers()` → `flickr.stats.getCollectionReferrers` [GET, read]
- `getCollectionStats()` → `flickr.stats.getCollectionStats` [GET, read]
- `getMostPopularPhotoDateRange()` → `flickr.stats.getMostPopularPhotoDateRange` [GET, read]
- `getPhotoDomains()` → `flickr.stats.getPhotoDomains` [GET, read]
- `getPhotoReferrers()` → `flickr.stats.getPhotoReferrers` [GET, read]
- `getPhotoStats()` → `flickr.stats.getPhotoStats` [GET, read]
- `getPhotosetDomains()` → `flickr.stats.getPhotosetDomains` [GET, read]
- `getPhotosetReferrers()` → `flickr.stats.getPhotosetReferrers` [GET, read]
- `getPhotosetStats()` → `flickr.stats.getPhotosetStats` [GET, read]
- `getPhotostreamDomains()` → `flickr.stats.getPhotostreamDomains` [GET, read]
- `getPhotostreamReferrers()` → `flickr.stats.getPhotostreamReferrers` [GET, read]
- `getPhotostreamStats()` → `flickr.stats.getPhotostreamStats` [GET, read]
- `getPopularPhotos()` → `flickr.stats.getPopularPhotos` [GET, read]
- `getTotalViews()` → `flickr.stats.getTotalViews` [GET, read]

## `tags()` → TagsApi

- `getClusterPhotos()` → `flickr.tags.getClusterPhotos` [GET, none]
- `getClusters()` → `flickr.tags.getClusters` [GET, none]
- `getHotList()` → `flickr.tags.getHotList` [GET, none]
- `getListPhoto()` → `flickr.tags.getListPhoto` [GET, none]
- `getListUser()` → `flickr.tags.getListUser` [GET, none]
- `getListUserPopular()` → `flickr.tags.getListUserPopular` [GET, none]
- `getListUserRaw()` → `flickr.tags.getListUserRaw` [GET, none]
- `getMostFrequentlyUsed()` → `flickr.tags.getMostFrequentlyUsed` [GET, read]
- `getRelated()` → `flickr.tags.getRelated` [GET, none]

## `test()` → TestApi

- `echo()` → `flickr.test.echo` [GET, none]
- `login()` → `flickr.test.login` [GET, read]
- `null()` → `flickr.test.null` [GET, read]

## `testimonials()` → TestimonialsApi

- `addTestimonial()` → `flickr.testimonials.addTestimonial` [POST, write]
- `approveTestimonial()` → `flickr.testimonials.approveTestimonial` [POST, write]
- `deleteTestimonial()` → `flickr.testimonials.deleteTestimonial` [POST, write]
- `editTestimonial()` → `flickr.testimonials.editTestimonial` [POST, write]
- `getAllTestimonialsAbout()` → `flickr.testimonials.getAllTestimonialsAbout` [GET, read]
- `getAllTestimonialsAboutBy()` → `flickr.testimonials.getAllTestimonialsAboutBy` [GET, read]
- `getAllTestimonialsBy()` → `flickr.testimonials.getAllTestimonialsBy` [GET, read]
- `getPendingTestimonialsAbout()` → `flickr.testimonials.getPendingTestimonialsAbout` [GET, read]
- `getPendingTestimonialsAboutBy()` → `flickr.testimonials.getPendingTestimonialsAboutBy` [GET, read]
- `getPendingTestimonialsBy()` → `flickr.testimonials.getPendingTestimonialsBy` [GET, read]
- `getTestimonialsAbout()` → `flickr.testimonials.getTestimonialsAbout` [GET, none]
- `getTestimonialsAboutBy()` → `flickr.testimonials.getTestimonialsAboutBy` [GET, read]
- `getTestimonialsBy()` → `flickr.testimonials.getTestimonialsBy` [GET, none]

## `urls()` → UrlsApi

- `getGroup()` → `flickr.urls.getGroup` [GET, none]
- `getUserPhotos()` → `flickr.urls.getUserPhotos` [GET, none]
- `getUserProfile()` → `flickr.urls.getUserProfile` [GET, none]
- `lookupGallery()` → `flickr.urls.lookupGallery` [GET, none]
- `lookupGroup()` → `flickr.urls.lookupGroup` [GET, none]
- `lookupUser()` → `flickr.urls.lookupUser` [GET, none]

## `uploads()` → UploadsApi

- `checkTickets()` → `flickr.photos.upload.checkTickets` [GET, none]
