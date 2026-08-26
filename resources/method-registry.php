<?php

declare(strict_types=1);

/* Frozen baseline extracted from the archived SDK registry.
 * Maintainers must review each record against
 * https://www.flickr.com/services/api/{method}.html before release. */

return  [
    'meta'
     => [
         'index_url' => 'https://www.flickr.com/services/api/',
         'retrieved_at' => '2026-08-25',
         'names_sha256' => 'ba0d069010e4b962a3994ef86944446ae49c2a2523dfa2f08add939658f1d610',
     ],
    'methods'
     => [
         'flickr.activity.userComments'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.activity.userComments.html',
          ],
         'flickr.activity.userPhotos'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.activity.userPhotos.html',
          ],
         'flickr.auth.checkToken'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => false,
              'available' => false,
              'deprecation_reason' => 'Legacy pre-OAuth API requires obsolete frob/api_sig signing.',
              'docs_url' => 'https://www.flickr.com/services/api/flickr.auth.checkToken.html',
          ],
         'flickr.auth.getFrob'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => false,
              'available' => false,
              'deprecation_reason' => 'Legacy pre-OAuth API requires obsolete frob/api_sig signing.',
              'docs_url' => 'https://www.flickr.com/services/api/flickr.auth.getFrob.html',
          ],
         'flickr.auth.getFullToken'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => false,
              'available' => false,
              'deprecation_reason' => 'Legacy pre-OAuth API requires obsolete frob/api_sig signing.',
              'docs_url' => 'https://www.flickr.com/services/api/flickr.auth.getFullToken.html',
          ],
         'flickr.auth.getToken'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => false,
              'available' => false,
              'deprecation_reason' => 'Legacy pre-OAuth API requires obsolete frob/api_sig signing.',
              'docs_url' => 'https://www.flickr.com/services/api/flickr.auth.getToken.html',
          ],
         'flickr.auth.oauth.checkToken'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => false,
              'available' => false,
              'deprecation_reason' => 'Deprecated legacy OAuth endpoint retained as metadata only.',
              'docs_url' => 'https://www.flickr.com/services/api/flickr.auth.oauth.checkToken.html',
          ],
         'flickr.auth.oauth.getAccessToken'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => false,
              'available' => false,
              'deprecation_reason' => 'Deprecated legacy OAuth endpoint retained as metadata only.',
              'docs_url' => 'https://www.flickr.com/services/api/flickr.auth.oauth.getAccessToken.html',
          ],
         'flickr.blogs.getList'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.blogs.getList.html',
          ],
         'flickr.blogs.getServices'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.blogs.getServices.html',
          ],
         'flickr.blogs.postPhoto'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.blogs.postPhoto.html',
          ],
         'flickr.cameras.getBrandModels'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.cameras.getBrandModels.html',
          ],
         'flickr.cameras.getBrands'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.cameras.getBrands.html',
          ],
         'flickr.collections.getInfo'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.collections.getInfo.html',
          ],
         'flickr.collections.getTree'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.collections.getTree.html',
          ],
         'flickr.commons.getInstitutions'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.commons.getInstitutions.html',
          ],
         'flickr.contacts.getList'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.contacts.getList.html',
          ],
         'flickr.contacts.getListRecentlyUploaded'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.contacts.getListRecentlyUploaded.html',
          ],
         'flickr.contacts.getPublicList'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.contacts.getPublicList.html',
          ],
         'flickr.contacts.getTaggingSuggestions'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.contacts.getTaggingSuggestions.html',
          ],
         'flickr.favorites.add'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.favorites.add.html',
          ],
         'flickr.favorites.getContext'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.favorites.getContext.html',
          ],
         'flickr.favorites.getList'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.favorites.getList.html',
          ],
         'flickr.favorites.getPublicList'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.favorites.getPublicList.html',
          ],
         'flickr.favorites.remove'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.favorites.remove.html',
          ],
         'flickr.galleries.addPhoto'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.galleries.addPhoto.html',
          ],
         'flickr.galleries.create'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.galleries.create.html',
          ],
         'flickr.galleries.editMeta'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.galleries.editMeta.html',
          ],
         'flickr.galleries.editPhoto'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.galleries.editPhoto.html',
          ],
         'flickr.galleries.editPhotos'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.galleries.editPhotos.html',
          ],
         'flickr.galleries.getInfo'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.galleries.getInfo.html',
          ],
         'flickr.galleries.getList'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.galleries.getList.html',
          ],
         'flickr.galleries.getListForPhoto'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.galleries.getListForPhoto.html',
          ],
         'flickr.galleries.getPhotos'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.galleries.getPhotos.html',
          ],
         'flickr.galleries.removePhoto'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.galleries.removePhoto.html',
          ],
         'flickr.groups.discuss.replies.add'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.groups.discuss.replies.add.html',
          ],
         'flickr.groups.discuss.replies.delete'
          => [
              'verb' => 'POST',
              'permission' => 'delete',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.groups.discuss.replies.delete.html',
          ],
         'flickr.groups.discuss.replies.edit'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.groups.discuss.replies.edit.html',
          ],
         'flickr.groups.discuss.replies.getInfo'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.groups.discuss.replies.getInfo.html',
          ],
         'flickr.groups.discuss.replies.getList'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.groups.discuss.replies.getList.html',
          ],
         'flickr.groups.discuss.topics.add'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.groups.discuss.topics.add.html',
          ],
         'flickr.groups.discuss.topics.getInfo'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.groups.discuss.topics.getInfo.html',
          ],
         'flickr.groups.discuss.topics.getList'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.groups.discuss.topics.getList.html',
          ],
         'flickr.groups.getInfo'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.groups.getInfo.html',
          ],
         'flickr.groups.join'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.groups.join.html',
          ],
         'flickr.groups.joinRequest'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.groups.joinRequest.html',
          ],
         'flickr.groups.leave'
          => [
              'verb' => 'POST',
              'permission' => 'delete',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.groups.leave.html',
          ],
         'flickr.groups.members.getList'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.groups.members.getList.html',
          ],
         'flickr.groups.pools.add'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.groups.pools.add.html',
          ],
         'flickr.groups.pools.getContext'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.groups.pools.getContext.html',
          ],
         'flickr.groups.pools.getGroups'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.groups.pools.getGroups.html',
          ],
         'flickr.groups.pools.getPhotos'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.groups.pools.getPhotos.html',
          ],
         'flickr.groups.pools.remove'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.groups.pools.remove.html',
          ],
         'flickr.groups.search'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.groups.search.html',
          ],
         'flickr.interestingness.getList'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.interestingness.getList.html',
          ],
         'flickr.machinetags.getNamespaces'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.machinetags.getNamespaces.html',
          ],
         'flickr.machinetags.getPairs'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.machinetags.getPairs.html',
          ],
         'flickr.machinetags.getPredicates'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.machinetags.getPredicates.html',
          ],
         'flickr.machinetags.getRecentValues'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.machinetags.getRecentValues.html',
          ],
         'flickr.machinetags.getValues'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.machinetags.getValues.html',
          ],
         'flickr.panda.getList'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => false,
              'deprecation_reason' => 'Flickr Panda (discontinued).',
              'docs_url' => 'https://www.flickr.com/services/api/flickr.panda.getList.html',
          ],
         'flickr.panda.getPhotos'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => false,
              'deprecation_reason' => 'Flickr Panda (discontinued).',
              'docs_url' => 'https://www.flickr.com/services/api/flickr.panda.getPhotos.html',
          ],
         'flickr.people.findByEmail'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.people.findByEmail.html',
          ],
         'flickr.people.findByUsername'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.people.findByUsername.html',
          ],
         'flickr.people.getGroups'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.people.getGroups.html',
          ],
         'flickr.people.getInfo'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.people.getInfo.html',
          ],
         'flickr.people.getLimits'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.people.getLimits.html',
          ],
         'flickr.people.getPhotos'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.people.getPhotos.html',
          ],
         'flickr.people.getPhotosOf'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.people.getPhotosOf.html',
          ],
         'flickr.people.getPublicGroups'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.people.getPublicGroups.html',
          ],
         'flickr.people.getPublicPhotos'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.people.getPublicPhotos.html',
          ],
         'flickr.people.getUploadStatus'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.people.getUploadStatus.html',
          ],
         'flickr.photos.addTags'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.addTags.html',
          ],
         'flickr.photos.comments.addComment'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.comments.addComment.html',
          ],
         'flickr.photos.comments.deleteComment'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.comments.deleteComment.html',
          ],
         'flickr.photos.comments.editComment'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.comments.editComment.html',
          ],
         'flickr.photos.comments.getList'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.comments.getList.html',
          ],
         'flickr.photos.comments.getRecentForContacts'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.comments.getRecentForContacts.html',
          ],
         'flickr.photos.delete'
          => [
              'verb' => 'POST',
              'permission' => 'delete',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.delete.html',
          ],
         'flickr.photos.geo.batchCorrectLocation'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.geo.batchCorrectLocation.html',
          ],
         'flickr.photos.geo.correctLocation'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.geo.correctLocation.html',
          ],
         'flickr.photos.geo.getLocation'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.geo.getLocation.html',
          ],
         'flickr.photos.geo.getPerms'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.geo.getPerms.html',
          ],
         'flickr.photos.geo.photosForLocation'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.geo.photosForLocation.html',
          ],
         'flickr.photos.geo.removeLocation'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.geo.removeLocation.html',
          ],
         'flickr.photos.geo.setContext'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.geo.setContext.html',
          ],
         'flickr.photos.geo.setLocation'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.geo.setLocation.html',
          ],
         'flickr.photos.geo.setPerms'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.geo.setPerms.html',
          ],
         'flickr.photos.getAllContexts'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.getAllContexts.html',
          ],
         'flickr.photos.getContactsPhotos'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.getContactsPhotos.html',
          ],
         'flickr.photos.getContactsPublicPhotos'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.getContactsPublicPhotos.html',
          ],
         'flickr.photos.getContext'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.getContext.html',
          ],
         'flickr.photos.getCounts'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.getCounts.html',
          ],
         'flickr.photos.getExif'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.getExif.html',
          ],
         'flickr.photos.getFavorites'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.getFavorites.html',
          ],
         'flickr.photos.getInfo'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.getInfo.html',
          ],
         'flickr.photos.getNotInSet'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.getNotInSet.html',
          ],
         'flickr.photos.getPerms'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.getPerms.html',
          ],
         'flickr.photos.getPopular'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.getPopular.html',
          ],
         'flickr.photos.getRecent'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.getRecent.html',
          ],
         'flickr.photos.getSizes'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.getSizes.html',
          ],
         'flickr.photos.getUntagged'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.getUntagged.html',
          ],
         'flickr.photos.getWithGeoData'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.getWithGeoData.html',
          ],
         'flickr.photos.getWithoutGeoData'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.getWithoutGeoData.html',
          ],
         'flickr.photos.licenses.getAvailable'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.licenses.getAvailable.html',
          ],
         'flickr.photos.licenses.getInfo'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.licenses.getInfo.html',
          ],
         'flickr.photos.licenses.getLicenseHistory'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.licenses.getLicenseHistory.html',
          ],
         'flickr.photos.licenses.setLicense'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.licenses.setLicense.html',
          ],
         'flickr.photos.notes.add'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.notes.add.html',
          ],
         'flickr.photos.notes.delete'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.notes.delete.html',
          ],
         'flickr.photos.notes.edit'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.notes.edit.html',
          ],
         'flickr.photos.people.add'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.people.add.html',
          ],
         'flickr.photos.people.delete'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.people.delete.html',
          ],
         'flickr.photos.people.deleteCoords'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.people.deleteCoords.html',
          ],
         'flickr.photos.people.editCoords'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.people.editCoords.html',
          ],
         'flickr.photos.people.getList'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.people.getList.html',
          ],
         'flickr.photos.recentlyUpdated'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.recentlyUpdated.html',
          ],
         'flickr.photos.removeTag'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.removeTag.html',
          ],
         'flickr.photos.search'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.search.html',
          ],
         'flickr.photos.setContentType'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.setContentType.html',
          ],
         'flickr.photos.setDates'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.setDates.html',
          ],
         'flickr.photos.setMeta'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.setMeta.html',
          ],
         'flickr.photos.setPerms'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.setPerms.html',
          ],
         'flickr.photos.setSafetyLevel'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.setSafetyLevel.html',
          ],
         'flickr.photos.setTags'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.setTags.html',
          ],
         'flickr.photos.suggestions.approveSuggestion'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.suggestions.approveSuggestion.html',
          ],
         'flickr.photos.suggestions.getList'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.suggestions.getList.html',
          ],
         'flickr.photos.suggestions.rejectSuggestion'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.suggestions.rejectSuggestion.html',
          ],
         'flickr.photos.suggestions.removeSuggestion'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.suggestions.removeSuggestion.html',
          ],
         'flickr.photos.suggestions.suggestLocation'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.suggestions.suggestLocation.html',
          ],
         'flickr.photos.transform.rotate'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.transform.rotate.html',
          ],
         'flickr.photos.upload.checkTickets'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photos.upload.checkTickets.html',
          ],
         'flickr.photosets.addPhoto'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photosets.addPhoto.html',
          ],
         'flickr.photosets.comments.addComment'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photosets.comments.addComment.html',
          ],
         'flickr.photosets.comments.deleteComment'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photosets.comments.deleteComment.html',
          ],
         'flickr.photosets.comments.editComment'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photosets.comments.editComment.html',
          ],
         'flickr.photosets.comments.getList'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photosets.comments.getList.html',
          ],
         'flickr.photosets.create'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photosets.create.html',
          ],
         'flickr.photosets.delete'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photosets.delete.html',
          ],
         'flickr.photosets.editMeta'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photosets.editMeta.html',
          ],
         'flickr.photosets.editPhotos'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photosets.editPhotos.html',
          ],
         'flickr.photosets.getContext'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photosets.getContext.html',
          ],
         'flickr.photosets.getInfo'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photosets.getInfo.html',
          ],
         'flickr.photosets.getList'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photosets.getList.html',
          ],
         'flickr.photosets.getPhotos'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photosets.getPhotos.html',
          ],
         'flickr.photosets.orderSets'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photosets.orderSets.html',
          ],
         'flickr.photosets.removePhoto'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photosets.removePhoto.html',
          ],
         'flickr.photosets.removePhotos'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photosets.removePhotos.html',
          ],
         'flickr.photosets.reorderPhotos'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photosets.reorderPhotos.html',
          ],
         'flickr.photosets.setPrimaryPhoto'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.photosets.setPrimaryPhoto.html',
          ],
         'flickr.places.find'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.places.find.html',
          ],
         'flickr.places.findByLatLon'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.places.findByLatLon.html',
          ],
         'flickr.places.getChildrenWithPhotosPublic'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.places.getChildrenWithPhotosPublic.html',
          ],
         'flickr.places.getInfo'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.places.getInfo.html',
          ],
         'flickr.places.getInfoByUrl'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.places.getInfoByUrl.html',
          ],
         'flickr.places.getPlaceTypes'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.places.getPlaceTypes.html',
          ],
         'flickr.places.getShapeHistory'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.places.getShapeHistory.html',
          ],
         'flickr.places.getTopPlacesList'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.places.getTopPlacesList.html',
          ],
         'flickr.places.placesForBoundingBox'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.places.placesForBoundingBox.html',
          ],
         'flickr.places.placesForContacts'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.places.placesForContacts.html',
          ],
         'flickr.places.placesForTags'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.places.placesForTags.html',
          ],
         'flickr.places.placesForUser'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.places.placesForUser.html',
          ],
         'flickr.places.resolvePlaceId'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.places.resolvePlaceId.html',
          ],
         'flickr.places.resolvePlaceURL'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.places.resolvePlaceURL.html',
          ],
         'flickr.places.tagsForPlace'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.places.tagsForPlace.html',
          ],
         'flickr.prefs.getContentType'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.prefs.getContentType.html',
          ],
         'flickr.prefs.getGeoPerms'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.prefs.getGeoPerms.html',
          ],
         'flickr.prefs.getHidden'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.prefs.getHidden.html',
          ],
         'flickr.prefs.getPrivacy'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.prefs.getPrivacy.html',
          ],
         'flickr.prefs.getSafetyLevel'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.prefs.getSafetyLevel.html',
          ],
         'flickr.profile.getProfile'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.profile.getProfile.html',
          ],
         'flickr.push.getSubscriptions'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.push.getSubscriptions.html',
          ],
         'flickr.push.getTopics'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.push.getTopics.html',
          ],
         'flickr.push.subscribe'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.push.subscribe.html',
          ],
         'flickr.push.unsubscribe'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.push.unsubscribe.html',
          ],
         'flickr.reflection.getMethodInfo'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.reflection.getMethodInfo.html',
          ],
         'flickr.reflection.getMethods'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.reflection.getMethods.html',
          ],
         'flickr.stats.getCSVFiles'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.stats.getCSVFiles.html',
          ],
         'flickr.stats.getCollectionDomains'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.stats.getCollectionDomains.html',
          ],
         'flickr.stats.getCollectionReferrers'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.stats.getCollectionReferrers.html',
          ],
         'flickr.stats.getCollectionStats'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.stats.getCollectionStats.html',
          ],
         'flickr.stats.getMostPopularPhotoDateRange'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.stats.getMostPopularPhotoDateRange.html',
          ],
         'flickr.stats.getPhotoDomains'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.stats.getPhotoDomains.html',
          ],
         'flickr.stats.getPhotoReferrers'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.stats.getPhotoReferrers.html',
          ],
         'flickr.stats.getPhotoStats'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.stats.getPhotoStats.html',
          ],
         'flickr.stats.getPhotosetDomains'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.stats.getPhotosetDomains.html',
          ],
         'flickr.stats.getPhotosetReferrers'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.stats.getPhotosetReferrers.html',
          ],
         'flickr.stats.getPhotosetStats'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.stats.getPhotosetStats.html',
          ],
         'flickr.stats.getPhotostreamDomains'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.stats.getPhotostreamDomains.html',
          ],
         'flickr.stats.getPhotostreamReferrers'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.stats.getPhotostreamReferrers.html',
          ],
         'flickr.stats.getPhotostreamStats'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.stats.getPhotostreamStats.html',
          ],
         'flickr.stats.getPopularPhotos'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.stats.getPopularPhotos.html',
          ],
         'flickr.stats.getTotalViews'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.stats.getTotalViews.html',
          ],
         'flickr.tags.getClusterPhotos'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.tags.getClusterPhotos.html',
          ],
         'flickr.tags.getClusters'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.tags.getClusters.html',
          ],
         'flickr.tags.getHotList'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.tags.getHotList.html',
          ],
         'flickr.tags.getListPhoto'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.tags.getListPhoto.html',
          ],
         'flickr.tags.getListUser'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.tags.getListUser.html',
          ],
         'flickr.tags.getListUserPopular'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.tags.getListUserPopular.html',
          ],
         'flickr.tags.getListUserRaw'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.tags.getListUserRaw.html',
          ],
         'flickr.tags.getMostFrequentlyUsed'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.tags.getMostFrequentlyUsed.html',
          ],
         'flickr.tags.getRelated'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.tags.getRelated.html',
          ],
         'flickr.test.echo'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.test.echo.html',
          ],
         'flickr.test.login'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.test.login.html',
          ],
         'flickr.test.null'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.test.null.html',
          ],
         'flickr.testimonials.addTestimonial'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.testimonials.addTestimonial.html',
          ],
         'flickr.testimonials.approveTestimonial'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.testimonials.approveTestimonial.html',
          ],
         'flickr.testimonials.deleteTestimonial'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.testimonials.deleteTestimonial.html',
          ],
         'flickr.testimonials.editTestimonial'
          => [
              'verb' => 'POST',
              'permission' => 'write',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.testimonials.editTestimonial.html',
          ],
         'flickr.testimonials.getAllTestimonialsAbout'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.testimonials.getAllTestimonialsAbout.html',
          ],
         'flickr.testimonials.getAllTestimonialsAboutBy'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.testimonials.getAllTestimonialsAboutBy.html',
          ],
         'flickr.testimonials.getAllTestimonialsBy'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.testimonials.getAllTestimonialsBy.html',
          ],
         'flickr.testimonials.getPendingTestimonialsAbout'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.testimonials.getPendingTestimonialsAbout.html',
          ],
         'flickr.testimonials.getPendingTestimonialsAboutBy'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.testimonials.getPendingTestimonialsAboutBy.html',
          ],
         'flickr.testimonials.getPendingTestimonialsBy'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.testimonials.getPendingTestimonialsBy.html',
          ],
         'flickr.testimonials.getTestimonialsAbout'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.testimonials.getTestimonialsAbout.html',
          ],
         'flickr.testimonials.getTestimonialsAboutBy'
          => [
              'verb' => 'GET',
              'permission' => 'read',
              'cacheable' => false,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.testimonials.getTestimonialsAboutBy.html',
          ],
         'flickr.testimonials.getTestimonialsBy'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.testimonials.getTestimonialsBy.html',
          ],
         'flickr.urls.getGroup'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.urls.getGroup.html',
          ],
         'flickr.urls.getUserPhotos'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.urls.getUserPhotos.html',
          ],
         'flickr.urls.getUserProfile'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.urls.getUserProfile.html',
          ],
         'flickr.urls.lookupGallery'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.urls.lookupGallery.html',
          ],
         'flickr.urls.lookupGroup'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.urls.lookupGroup.html',
          ],
         'flickr.urls.lookupUser'
          => [
              'verb' => 'GET',
              'permission' => 'none',
              'cacheable' => true,
              'available' => true,
              'deprecation_reason' => null,
              'docs_url' => 'https://www.flickr.com/services/api/flickr.urls.lookupUser.html',
          ],
     ],
];
