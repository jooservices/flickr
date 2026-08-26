<?php

declare(strict_types=1);

namespace JOOservices\Flickr;

use JOOservices\Flickr\Api\Api;
use JOOservices\Flickr\Auth\OAuth1Authenticator;
use JOOservices\Flickr\Services\ActivityApi;
use JOOservices\Flickr\Services\BlogsApi;
use JOOservices\Flickr\Services\CamerasApi;
use JOOservices\Flickr\Services\CollectionsApi;
use JOOservices\Flickr\Services\CommonsApi;
use JOOservices\Flickr\Services\ContactsApi;
use JOOservices\Flickr\Services\FavoritesApi;
use JOOservices\Flickr\Services\GalleriesApi;
use JOOservices\Flickr\Services\GroupsApi;
use JOOservices\Flickr\Services\GroupsDiscussRepliesApi;
use JOOservices\Flickr\Services\GroupsDiscussTopicsApi;
use JOOservices\Flickr\Services\GroupsMembersApi;
use JOOservices\Flickr\Services\GroupsPoolsApi;
use JOOservices\Flickr\Services\InterestingnessApi;
use JOOservices\Flickr\Services\LegacyAuthApi;
use JOOservices\Flickr\Services\MachinetagsApi;
use JOOservices\Flickr\Services\PandaApi;
use JOOservices\Flickr\Services\PeopleApi;
use JOOservices\Flickr\Services\PhotosApi;
use JOOservices\Flickr\Services\PhotosCommentsApi;
use JOOservices\Flickr\Services\PhotosGeoApi;
use JOOservices\Flickr\Services\PhotosLicensesApi;
use JOOservices\Flickr\Services\PhotosNotesApi;
use JOOservices\Flickr\Services\PhotosPeopleApi;
use JOOservices\Flickr\Services\PhotosSuggestionsApi;
use JOOservices\Flickr\Services\PhotosTransformApi;
use JOOservices\Flickr\Services\PhotosetsApi;
use JOOservices\Flickr\Services\PhotosetsCommentsApi;
use JOOservices\Flickr\Services\PlacesApi;
use JOOservices\Flickr\Services\PrefsApi;
use JOOservices\Flickr\Services\ProfileApi;
use JOOservices\Flickr\Services\PushApi;
use JOOservices\Flickr\Services\ReflectionApi;
use JOOservices\Flickr\Services\StatsApi;
use JOOservices\Flickr\Services\TagsApi;
use JOOservices\Flickr\Services\TestApi;
use JOOservices\Flickr\Services\TestimonialsApi;
use JOOservices\Flickr\Services\UrlsApi;
use JOOservices\Flickr\Upload\UploadService;

/** Generated facade: explicit universal gateway plus domain accessors. No magic dispatch. */
final class Flickr
{
    /** @var array<string, object> */
    private array $resolved = [];

    public function __construct(
        private readonly Api $api,
        private readonly OAuth1Authenticator $oauthAuthenticator,
        private readonly UploadService $uploadsService,
    ) {
    }

    public function api(): Api
    {
        return $this->api;
    }

    public function oauth(): OAuth1Authenticator
    {
        return $this->oauthAuthenticator;
    }

    public function uploads(): UploadService
    {
        return $this->uploadsService;
    }

    public function activity(): \JOOservices\Flickr\Services\ActivityApi
    {
        /** @var ActivityApi $instance */
        $instance = $this->resolved['activity'] ??= new ActivityApi($this->api);

        return $instance;
    }
    public function legacyAuth(): \JOOservices\Flickr\Services\LegacyAuthApi
    {
        /** @var LegacyAuthApi $instance */
        $instance = $this->resolved['legacyAuth'] ??= new LegacyAuthApi($this->api);

        return $instance;
    }
    public function blogs(): \JOOservices\Flickr\Services\BlogsApi
    {
        /** @var BlogsApi $instance */
        $instance = $this->resolved['blogs'] ??= new BlogsApi($this->api);

        return $instance;
    }
    public function cameras(): \JOOservices\Flickr\Services\CamerasApi
    {
        /** @var CamerasApi $instance */
        $instance = $this->resolved['cameras'] ??= new CamerasApi($this->api);

        return $instance;
    }
    public function collections(): \JOOservices\Flickr\Services\CollectionsApi
    {
        /** @var CollectionsApi $instance */
        $instance = $this->resolved['collections'] ??= new CollectionsApi($this->api);

        return $instance;
    }
    public function commons(): \JOOservices\Flickr\Services\CommonsApi
    {
        /** @var CommonsApi $instance */
        $instance = $this->resolved['commons'] ??= new CommonsApi($this->api);

        return $instance;
    }
    public function contacts(): \JOOservices\Flickr\Services\ContactsApi
    {
        /** @var ContactsApi $instance */
        $instance = $this->resolved['contacts'] ??= new ContactsApi($this->api);

        return $instance;
    }
    public function favorites(): \JOOservices\Flickr\Services\FavoritesApi
    {
        /** @var FavoritesApi $instance */
        $instance = $this->resolved['favorites'] ??= new FavoritesApi($this->api);

        return $instance;
    }
    public function galleries(): \JOOservices\Flickr\Services\GalleriesApi
    {
        /** @var GalleriesApi $instance */
        $instance = $this->resolved['galleries'] ??= new GalleriesApi($this->api);

        return $instance;
    }
    public function groups(): \JOOservices\Flickr\Services\GroupsApi
    {
        /** @var GroupsApi $instance */
        $instance = $this->resolved['groups'] ??= new GroupsApi($this->api);

        return $instance;
    }
    public function groupsDiscussReplies(): \JOOservices\Flickr\Services\GroupsDiscussRepliesApi
    {
        /** @var GroupsDiscussRepliesApi $instance */
        $instance = $this->resolved['groupsDiscussReplies'] ??= new GroupsDiscussRepliesApi($this->api);

        return $instance;
    }
    public function groupsDiscussTopics(): \JOOservices\Flickr\Services\GroupsDiscussTopicsApi
    {
        /** @var GroupsDiscussTopicsApi $instance */
        $instance = $this->resolved['groupsDiscussTopics'] ??= new GroupsDiscussTopicsApi($this->api);

        return $instance;
    }
    public function groupsMembers(): \JOOservices\Flickr\Services\GroupsMembersApi
    {
        /** @var GroupsMembersApi $instance */
        $instance = $this->resolved['groupsMembers'] ??= new GroupsMembersApi($this->api);

        return $instance;
    }
    public function groupsPools(): \JOOservices\Flickr\Services\GroupsPoolsApi
    {
        /** @var GroupsPoolsApi $instance */
        $instance = $this->resolved['groupsPools'] ??= new GroupsPoolsApi($this->api);

        return $instance;
    }
    public function interestingness(): \JOOservices\Flickr\Services\InterestingnessApi
    {
        /** @var InterestingnessApi $instance */
        $instance = $this->resolved['interestingness'] ??= new InterestingnessApi($this->api);

        return $instance;
    }
    public function machinetags(): \JOOservices\Flickr\Services\MachinetagsApi
    {
        /** @var MachinetagsApi $instance */
        $instance = $this->resolved['machinetags'] ??= new MachinetagsApi($this->api);

        return $instance;
    }
    public function panda(): \JOOservices\Flickr\Services\PandaApi
    {
        /** @var PandaApi $instance */
        $instance = $this->resolved['panda'] ??= new PandaApi($this->api);

        return $instance;
    }
    public function people(): \JOOservices\Flickr\Services\PeopleApi
    {
        /** @var PeopleApi $instance */
        $instance = $this->resolved['people'] ??= new PeopleApi($this->api);

        return $instance;
    }
    public function photos(): \JOOservices\Flickr\Services\PhotosApi
    {
        /** @var PhotosApi $instance */
        $instance = $this->resolved['photos'] ??= new PhotosApi($this->api);

        return $instance;
    }
    public function photosComments(): \JOOservices\Flickr\Services\PhotosCommentsApi
    {
        /** @var PhotosCommentsApi $instance */
        $instance = $this->resolved['photosComments'] ??= new PhotosCommentsApi($this->api);

        return $instance;
    }
    public function photosGeo(): \JOOservices\Flickr\Services\PhotosGeoApi
    {
        /** @var PhotosGeoApi $instance */
        $instance = $this->resolved['photosGeo'] ??= new PhotosGeoApi($this->api);

        return $instance;
    }
    public function photosLicenses(): \JOOservices\Flickr\Services\PhotosLicensesApi
    {
        /** @var PhotosLicensesApi $instance */
        $instance = $this->resolved['photosLicenses'] ??= new PhotosLicensesApi($this->api);

        return $instance;
    }
    public function photosNotes(): \JOOservices\Flickr\Services\PhotosNotesApi
    {
        /** @var PhotosNotesApi $instance */
        $instance = $this->resolved['photosNotes'] ??= new PhotosNotesApi($this->api);

        return $instance;
    }
    public function photosPeople(): \JOOservices\Flickr\Services\PhotosPeopleApi
    {
        /** @var PhotosPeopleApi $instance */
        $instance = $this->resolved['photosPeople'] ??= new PhotosPeopleApi($this->api);

        return $instance;
    }
    public function photosSuggestions(): \JOOservices\Flickr\Services\PhotosSuggestionsApi
    {
        /** @var PhotosSuggestionsApi $instance */
        $instance = $this->resolved['photosSuggestions'] ??= new PhotosSuggestionsApi($this->api);

        return $instance;
    }
    public function photosTransform(): \JOOservices\Flickr\Services\PhotosTransformApi
    {
        /** @var PhotosTransformApi $instance */
        $instance = $this->resolved['photosTransform'] ??= new PhotosTransformApi($this->api);

        return $instance;
    }
    public function photosets(): \JOOservices\Flickr\Services\PhotosetsApi
    {
        /** @var PhotosetsApi $instance */
        $instance = $this->resolved['photosets'] ??= new PhotosetsApi($this->api);

        return $instance;
    }
    public function photosetsComments(): \JOOservices\Flickr\Services\PhotosetsCommentsApi
    {
        /** @var PhotosetsCommentsApi $instance */
        $instance = $this->resolved['photosetsComments'] ??= new PhotosetsCommentsApi($this->api);

        return $instance;
    }
    public function places(): \JOOservices\Flickr\Services\PlacesApi
    {
        /** @var PlacesApi $instance */
        $instance = $this->resolved['places'] ??= new PlacesApi($this->api);

        return $instance;
    }
    public function prefs(): \JOOservices\Flickr\Services\PrefsApi
    {
        /** @var PrefsApi $instance */
        $instance = $this->resolved['prefs'] ??= new PrefsApi($this->api);

        return $instance;
    }
    public function profile(): \JOOservices\Flickr\Services\ProfileApi
    {
        /** @var ProfileApi $instance */
        $instance = $this->resolved['profile'] ??= new ProfileApi($this->api);

        return $instance;
    }
    public function push(): \JOOservices\Flickr\Services\PushApi
    {
        /** @var PushApi $instance */
        $instance = $this->resolved['push'] ??= new PushApi($this->api);

        return $instance;
    }
    public function reflection(): \JOOservices\Flickr\Services\ReflectionApi
    {
        /** @var ReflectionApi $instance */
        $instance = $this->resolved['reflection'] ??= new ReflectionApi($this->api);

        return $instance;
    }
    public function stats(): \JOOservices\Flickr\Services\StatsApi
    {
        /** @var StatsApi $instance */
        $instance = $this->resolved['stats'] ??= new StatsApi($this->api);

        return $instance;
    }
    public function tags(): \JOOservices\Flickr\Services\TagsApi
    {
        /** @var TagsApi $instance */
        $instance = $this->resolved['tags'] ??= new TagsApi($this->api);

        return $instance;
    }
    public function test(): \JOOservices\Flickr\Services\TestApi
    {
        /** @var TestApi $instance */
        $instance = $this->resolved['test'] ??= new TestApi($this->api);

        return $instance;
    }
    public function testimonials(): \JOOservices\Flickr\Services\TestimonialsApi
    {
        /** @var TestimonialsApi $instance */
        $instance = $this->resolved['testimonials'] ??= new TestimonialsApi($this->api);

        return $instance;
    }
    public function urls(): \JOOservices\Flickr\Services\UrlsApi
    {
        /** @var UrlsApi $instance */
        $instance = $this->resolved['urls'] ??= new UrlsApi($this->api);

        return $instance;
    }
}
