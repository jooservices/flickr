<?php

declare(strict_types=1);

namespace JOOservices\Flickr;

use JOOservices\Flickr\Auth\InMemoryTokenStore;
use JOOservices\Flickr\Auth\OAuth1Authenticator;
use JOOservices\Flickr\Auth\OAuth1Signer;
use JOOservices\Flickr\Client\FlickrClient;
use JOOservices\Flickr\Client\FlickrUploadClient;
use JOOservices\Flickr\Client\JooClientTransport;
use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\Contracts\Auth\FlickrTokenStoreContract;
use JOOservices\Flickr\Contracts\Client\FlickrTransportContract;
use JOOservices\Flickr\Metadata\FlickrMethodRegistry;
use JOOservices\Flickr\Services\ActivityService;
use JOOservices\Flickr\Services\AuthOauthService;
use JOOservices\Flickr\Services\AuthService;
use JOOservices\Flickr\Services\BlogsService;
use JOOservices\Flickr\Services\CamerasService;
use JOOservices\Flickr\Services\CollectionsService;
use JOOservices\Flickr\Services\CommonsService;
use JOOservices\Flickr\Services\ContactsService;
use JOOservices\Flickr\Services\FavoriteService;
use JOOservices\Flickr\Services\GalleryService;
use JOOservices\Flickr\Services\GroupsDiscussRepliesService;
use JOOservices\Flickr\Services\GroupsDiscussTopicsService;
use JOOservices\Flickr\Services\GroupService;
use JOOservices\Flickr\Services\GroupsMembersService;
use JOOservices\Flickr\Services\GroupsPoolsService;
use JOOservices\Flickr\Services\InterestingnessService;
use JOOservices\Flickr\Services\MachinetagsService;
use JOOservices\Flickr\Services\PandaService;
use JOOservices\Flickr\Services\PeopleService;
use JOOservices\Flickr\Services\PhotosCommentsService;
use JOOservices\Flickr\Services\PhotoService;
use JOOservices\Flickr\Services\PhotosetsCommentsService;
use JOOservices\Flickr\Services\PhotosetService;
use JOOservices\Flickr\Services\PhotosGeoService;
use JOOservices\Flickr\Services\PhotosLicensesService;
use JOOservices\Flickr\Services\PhotosNotesService;
use JOOservices\Flickr\Services\PhotosPeopleService;
use JOOservices\Flickr\Services\PhotosSuggestionsService;
use JOOservices\Flickr\Services\PhotosTransformService;
use JOOservices\Flickr\Services\PhotosUploadService;
use JOOservices\Flickr\Services\PlacesService;
use JOOservices\Flickr\Services\PrefsService;
use JOOservices\Flickr\Services\ProfileService;
use JOOservices\Flickr\Services\PushService;
use JOOservices\Flickr\Services\RawApiService;
use JOOservices\Flickr\Services\ReflectionService;
use JOOservices\Flickr\Services\StatsService;
use JOOservices\Flickr\Services\TagService;
use JOOservices\Flickr\Services\TestimonialsService;
use JOOservices\Flickr\Services\TestService;
use JOOservices\Flickr\Services\UploadService;
use JOOservices\Flickr\Services\UrlsService;

final class FlickrFactory
{
    public static function make(
        FlickrConfig $config,
        ?FlickrTokenStoreContract $tokenStore = null,
        ?FlickrTransportContract $transport = null,
    ): Flickr {
        $transport ??= JooClientTransport::fromConfig($config);
        $tokenStore ??= new InMemoryTokenStore;
        $registry = FlickrMethodRegistry::default();
        $signer = new OAuth1Signer($config);
        $client = new FlickrClient($config, $transport, $signer, $tokenStore, $registry);
        $uploadClient = new FlickrUploadClient($config, $transport, $signer, $tokenStore);
        $raw = new RawApiService($client);

        return new Flickr(
            raw: $raw,
            auth: new OAuth1Authenticator($config, $signer, $transport),
            tokens: $tokenStore,
            uploads: new UploadService($uploadClient, $raw),
            activity: new ActivityService($raw),
            authApi: new AuthService($raw),
            authOauthApi: new AuthOauthService($raw),
            blogs: new BlogsService($raw),
            cameras: new CamerasService($raw),
            collections: new CollectionsService($raw),
            commons: new CommonsService($raw),
            contacts: new ContactsService($raw),
            favorites: new FavoriteService($raw),
            galleries: new GalleryService($raw),
            groups: new GroupService($raw),
            groupsDiscussReplies: new GroupsDiscussRepliesService($raw),
            groupsDiscussTopics: new GroupsDiscussTopicsService($raw),
            groupsMembers: new GroupsMembersService($raw),
            groupsPools: new GroupsPoolsService($raw),
            interestingness: new InterestingnessService($raw),
            machinetags: new MachinetagsService($raw),
            panda: new PandaService($raw),
            people: new PeopleService($raw),
            photos: new PhotoService($raw),
            photosComments: new PhotosCommentsService($raw),
            photosGeo: new PhotosGeoService($raw),
            photosLicenses: new PhotosLicensesService($raw),
            photosNotes: new PhotosNotesService($raw),
            photosPeople: new PhotosPeopleService($raw),
            photosSuggestions: new PhotosSuggestionsService($raw),
            photosTransform: new PhotosTransformService($raw),
            photosUpload: new PhotosUploadService($raw),
            photosets: new PhotosetService($raw),
            photosetsComments: new PhotosetsCommentsService($raw),
            places: new PlacesService($raw),
            prefs: new PrefsService($raw),
            profile: new ProfileService($raw),
            push: new PushService($raw),
            reflection: new ReflectionService($raw),
            stats: new StatsService($raw),
            tags: new TagService($raw),
            test: new TestService($raw),
            testimonials: new TestimonialsService($raw),
            urls: new UrlsService($raw),
        );
    }
}
