<?php

declare(strict_types=1);

namespace JOOservices\Flickr;

use JOOservices\Flickr\Contracts\Auth\FlickrAuthenticatorContract;
use JOOservices\Flickr\Contracts\Auth\FlickrTokenStoreContract;
use JOOservices\Flickr\Contracts\Services\ActivityServiceContract;
use JOOservices\Flickr\Contracts\Services\AuthOauthServiceContract;
use JOOservices\Flickr\Contracts\Services\AuthServiceContract;
use JOOservices\Flickr\Contracts\Services\BlogsServiceContract;
use JOOservices\Flickr\Contracts\Services\CamerasServiceContract;
use JOOservices\Flickr\Contracts\Services\CollectionsServiceContract;
use JOOservices\Flickr\Contracts\Services\CommonsServiceContract;
use JOOservices\Flickr\Contracts\Services\ContactsServiceContract;
use JOOservices\Flickr\Contracts\Services\FavoriteServiceContract;
use JOOservices\Flickr\Contracts\Services\GalleryServiceContract;
use JOOservices\Flickr\Contracts\Services\GroupsDiscussRepliesServiceContract;
use JOOservices\Flickr\Contracts\Services\GroupsDiscussTopicsServiceContract;
use JOOservices\Flickr\Contracts\Services\GroupServiceContract;
use JOOservices\Flickr\Contracts\Services\GroupsMembersServiceContract;
use JOOservices\Flickr\Contracts\Services\GroupsPoolsServiceContract;
use JOOservices\Flickr\Contracts\Services\InterestingnessServiceContract;
use JOOservices\Flickr\Contracts\Services\MachinetagsServiceContract;
use JOOservices\Flickr\Contracts\Services\PandaServiceContract;
use JOOservices\Flickr\Contracts\Services\PeopleServiceContract;
use JOOservices\Flickr\Contracts\Services\PhotosCommentsServiceContract;
use JOOservices\Flickr\Contracts\Services\PhotoServiceContract;
use JOOservices\Flickr\Contracts\Services\PhotosetsCommentsServiceContract;
use JOOservices\Flickr\Contracts\Services\PhotosetServiceContract;
use JOOservices\Flickr\Contracts\Services\PhotosGeoServiceContract;
use JOOservices\Flickr\Contracts\Services\PhotosLicensesServiceContract;
use JOOservices\Flickr\Contracts\Services\PhotosNotesServiceContract;
use JOOservices\Flickr\Contracts\Services\PhotosPeopleServiceContract;
use JOOservices\Flickr\Contracts\Services\PhotosSuggestionsServiceContract;
use JOOservices\Flickr\Contracts\Services\PhotosTransformServiceContract;
use JOOservices\Flickr\Contracts\Services\PhotosUploadServiceContract;
use JOOservices\Flickr\Contracts\Services\PlacesServiceContract;
use JOOservices\Flickr\Contracts\Services\PrefsServiceContract;
use JOOservices\Flickr\Contracts\Services\ProfileServiceContract;
use JOOservices\Flickr\Contracts\Services\PushServiceContract;
use JOOservices\Flickr\Contracts\Services\RawApiServiceContract;
use JOOservices\Flickr\Contracts\Services\ReflectionServiceContract;
use JOOservices\Flickr\Contracts\Services\StatsServiceContract;
use JOOservices\Flickr\Contracts\Services\TagServiceContract;
use JOOservices\Flickr\Contracts\Services\TestimonialsServiceContract;
use JOOservices\Flickr\Contracts\Services\TestServiceContract;
use JOOservices\Flickr\Contracts\Services\UploadServiceContract;
use JOOservices\Flickr\Contracts\Services\UrlsServiceContract;
use JOOservices\Flickr\DTO\Metadata\MethodInfo;
use JOOservices\Flickr\Metadata\FlickrMethodRegistry;

final class Flickr
{
    public function __construct(
        private RawApiServiceContract $raw,
        private FlickrAuthenticatorContract $auth,
        private FlickrTokenStoreContract $tokens,
        private UploadServiceContract $uploads,
        private ActivityServiceContract $activity,
        private AuthServiceContract $authApi,
        private AuthOauthServiceContract $authOauthApi,
        private BlogsServiceContract $blogs,
        private CamerasServiceContract $cameras,
        private CollectionsServiceContract $collections,
        private CommonsServiceContract $commons,
        private ContactsServiceContract $contacts,
        private FavoriteServiceContract $favorites,
        private GalleryServiceContract $galleries,
        private GroupServiceContract $groups,
        private GroupsDiscussRepliesServiceContract $groupsDiscussReplies,
        private GroupsDiscussTopicsServiceContract $groupsDiscussTopics,
        private GroupsMembersServiceContract $groupsMembers,
        private GroupsPoolsServiceContract $groupsPools,
        private InterestingnessServiceContract $interestingness,
        private MachinetagsServiceContract $machinetags,
        private PandaServiceContract $panda,
        private PeopleServiceContract $people,
        private PhotoServiceContract $photos,
        private PhotosCommentsServiceContract $photosComments,
        private PhotosGeoServiceContract $photosGeo,
        private PhotosLicensesServiceContract $photosLicenses,
        private PhotosNotesServiceContract $photosNotes,
        private PhotosPeopleServiceContract $photosPeople,
        private PhotosSuggestionsServiceContract $photosSuggestions,
        private PhotosTransformServiceContract $photosTransform,
        private PhotosUploadServiceContract $photosUpload,
        private PhotosetServiceContract $photosets,
        private PhotosetsCommentsServiceContract $photosetsComments,
        private PlacesServiceContract $places,
        private PrefsServiceContract $prefs,
        private ProfileServiceContract $profile,
        private PushServiceContract $push,
        private ReflectionServiceContract $reflection,
        private StatsServiceContract $stats,
        private TagServiceContract $tags,
        private TestServiceContract $test,
        private TestimonialsServiceContract $testimonials,
        private UrlsServiceContract $urls,
        private FlickrMethodRegistry $registry = new FlickrMethodRegistry([]),
    ) {}

    public function describe(string $method): ?MethodInfo
    {
        return $this->registry->describe($method);
    }

    public function raw(): RawApiServiceContract
    {
        return $this->raw;
    }

    public function auth(): FlickrAuthenticatorContract
    {
        return $this->auth;
    }

    public function tokens(): FlickrTokenStoreContract
    {
        return $this->tokens;
    }

    public function uploads(): UploadServiceContract
    {
        return $this->uploads;
    }

    public function activity(): ActivityServiceContract
    {
        return $this->activity;
    }

    public function authApi(): AuthServiceContract
    {
        return $this->authApi;
    }

    public function authOauthApi(): AuthOauthServiceContract
    {
        return $this->authOauthApi;
    }

    public function blogs(): BlogsServiceContract
    {
        return $this->blogs;
    }

    public function cameras(): CamerasServiceContract
    {
        return $this->cameras;
    }

    public function collections(): CollectionsServiceContract
    {
        return $this->collections;
    }

    public function commons(): CommonsServiceContract
    {
        return $this->commons;
    }

    public function contacts(): ContactsServiceContract
    {
        return $this->contacts;
    }

    public function favorites(): FavoriteServiceContract
    {
        return $this->favorites;
    }

    public function galleries(): GalleryServiceContract
    {
        return $this->galleries;
    }

    public function groups(): GroupServiceContract
    {
        return $this->groups;
    }

    public function groupsDiscussReplies(): GroupsDiscussRepliesServiceContract
    {
        return $this->groupsDiscussReplies;
    }

    public function groupsDiscussTopics(): GroupsDiscussTopicsServiceContract
    {
        return $this->groupsDiscussTopics;
    }

    public function groupsMembers(): GroupsMembersServiceContract
    {
        return $this->groupsMembers;
    }

    public function groupsPools(): GroupsPoolsServiceContract
    {
        return $this->groupsPools;
    }

    public function interestingness(): InterestingnessServiceContract
    {
        return $this->interestingness;
    }

    public function machinetags(): MachinetagsServiceContract
    {
        return $this->machinetags;
    }

    public function panda(): PandaServiceContract
    {
        return $this->panda;
    }

    public function people(): PeopleServiceContract
    {
        return $this->people;
    }

    public function photos(): PhotoServiceContract
    {
        return $this->photos;
    }

    public function photosComments(): PhotosCommentsServiceContract
    {
        return $this->photosComments;
    }

    public function photosGeo(): PhotosGeoServiceContract
    {
        return $this->photosGeo;
    }

    public function photosLicenses(): PhotosLicensesServiceContract
    {
        return $this->photosLicenses;
    }

    public function photosNotes(): PhotosNotesServiceContract
    {
        return $this->photosNotes;
    }

    public function photosPeople(): PhotosPeopleServiceContract
    {
        return $this->photosPeople;
    }

    public function photosSuggestions(): PhotosSuggestionsServiceContract
    {
        return $this->photosSuggestions;
    }

    public function photosTransform(): PhotosTransformServiceContract
    {
        return $this->photosTransform;
    }

    public function photosUpload(): PhotosUploadServiceContract
    {
        return $this->photosUpload;
    }

    public function photosets(): PhotosetServiceContract
    {
        return $this->photosets;
    }

    public function photosetsComments(): PhotosetsCommentsServiceContract
    {
        return $this->photosetsComments;
    }

    public function places(): PlacesServiceContract
    {
        return $this->places;
    }

    public function prefs(): PrefsServiceContract
    {
        return $this->prefs;
    }

    public function profile(): ProfileServiceContract
    {
        return $this->profile;
    }

    public function push(): PushServiceContract
    {
        return $this->push;
    }

    public function reflection(): ReflectionServiceContract
    {
        return $this->reflection;
    }

    public function stats(): StatsServiceContract
    {
        return $this->stats;
    }

    public function tags(): TagServiceContract
    {
        return $this->tags;
    }

    public function test(): TestServiceContract
    {
        return $this->test;
    }

    public function testimonials(): TestimonialsServiceContract
    {
        return $this->testimonials;
    }

    public function urls(): UrlsServiceContract
    {
        return $this->urls;
    }
}
