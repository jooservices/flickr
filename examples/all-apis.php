<?php

declare(strict_types=1);

use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\DTO\Auth\AccessTokenData;
use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\DTO\Photos\SearchPhotosData;
use JOOservices\Flickr\Flickr;
use JOOservices\Flickr\FlickrFactory;
use JOOservices\Flickr\Metadata\FlickrMethodDefinition;

require __DIR__.'/../vendor/autoload.php';

/**
 * Run this file without flags to print a wrapper catalog for every official
 * Flickr REST method. Add --live to execute the safe public read-only examples.
 *
 * Upload and replace intentionally stay separate in upload-photo.php and
 * replace-photo.php because they use the Flickr upload endpoint, not REST.
 */
if (realpath((string) ($_SERVER['SCRIPT_FILENAME'] ?? '')) === __FILE__) {
    flickr_all_apis_main($argv);
}

/**
 * @param  list<string>  $argv
 */
function flickr_all_apis_main(array $argv): void
{
    $live = in_array('--live', $argv, true);
    $onlyMethod = flickr_cli_option($argv, '--method=');
    $definitions = flickr_all_api_definitions();

    if ($onlyMethod !== null) {
        $definitions = array_filter(
            $definitions,
            static fn (FlickrMethodDefinition $definition): bool => $definition->name === $onlyMethod
        );
    }

    if ($live) {
        $flickr = FlickrFactory::make(new FlickrConfig(
            flickr_required_env('FLICKR_API_KEY'),
            flickr_required_env('FLICKR_API_SECRET')
        ));
        $accessToken = getenv('FLICKR_ACCESS_TOKEN') ?: '';
        $accessTokenSecret = getenv('FLICKR_ACCESS_TOKEN_SECRET') ?: '';

        if ($accessToken !== '' && $accessTokenSecret !== '') {
            $flickr->tokens()->put(new AccessTokenData($accessToken, $accessTokenSecret));
        }

        flickr_print_live_report($flickr, $definitions);

        return;
    }

    flickr_print_catalog($definitions);
}

/**
 * @return array<string, FlickrMethodDefinition>
 */
function flickr_all_api_definitions(): array
{
    /** @var array<string, FlickrMethodDefinition> $definitions */
    $definitions = require __DIR__.'/../src/Metadata/methods.php';

    return $definitions;
}

/**
 * @param  array<string, FlickrMethodDefinition>  $definitions
 */
function flickr_print_catalog(array $definitions): void
{
    echo 'JOOservices Flickr API example catalog'.PHP_EOL;
    echo 'Official REST wrappers: '.count($definitions).PHP_EOL;
    echo 'Live mode: FLICKR_API_KEY=... FLICKR_API_SECRET=... php examples/all-apis.php --live'.PHP_EOL.PHP_EOL;

    foreach ($definitions as $definition) {
        $auth = $definition->requiresAuth
            ? 'OAuth '.($definition->authPermission?->value ?? 'required')
            : 'public';

        echo $definition->name.PHP_EOL;
        echo '  docs: '.$definition->docsUrl.PHP_EOL;
        echo '  call: '.flickr_wrapper_example($definition->name).PHP_EOL;
        echo '  http: '.$definition->httpMethod->value.', auth: '.$auth.PHP_EOL.PHP_EOL;
    }

    echo 'Binary upload example: php examples/upload-photo.php /path/to/photo.jpg'.PHP_EOL;
    echo 'Binary replace example: php examples/replace-photo.php /path/to/photo.jpg PHOTO_ID'.PHP_EOL;
}

/**
 * @param  array<string, FlickrMethodDefinition>  $definitions
 */
function flickr_print_live_report(Flickr $flickr, array $definitions): void
{
    $context = flickr_discover_public_context($flickr);
    $samples = flickr_live_samples($context);
    $results = [];

    foreach ($definitions as $definition) {
        if (str_starts_with($definition->name, 'flickr.photos.upload.')) {
            $results[] = flickr_skipped($definition, 'upload API ignored for live run');

            continue;
        }

        if ($definition->requiresAuth && ! flickr_has_access_token()) {
            $permission = $definition->authPermission?->value ?? 'OAuth';
            $results[] = flickr_skipped($definition, 'requires '.$permission.' token');

            continue;
        }

        if ($definition->authPermission?->value === 'write' || $definition->authPermission?->value === 'delete') {
            $results[] = flickr_skipped($definition, 'mutation requires explicit disposable fixture');

            continue;
        }

        if (! isset($samples[$definition->name])) {
            $results[] = flickr_skipped($definition, 'needs user-specific fixture data');

            continue;
        }

        $started = microtime(true);

        try {
            $response = $samples[$definition->name]($flickr);
            $results[] = [
                'method' => $definition->name,
                'status' => $response->ok ? 'ok' : 'api-fail',
                'duration_ms' => flickr_duration_ms($started),
                'error' => $response->error?->message,
                'data_keys' => array_slice(array_keys($response->data), 0, 8),
            ];
        } catch (Throwable $throwable) {
            $results[] = [
                'method' => $definition->name,
                'status' => 'exception',
                'duration_ms' => flickr_duration_ms($started),
                'error' => $throwable->getMessage(),
                'data_keys' => [],
            ];
        }
    }

    flickr_echo_results($results, $context);
}

/**
 * @return array<string, mixed>
 */
function flickr_discover_public_context(Flickr $flickr): array
{
    $search = $flickr->photos()->search(SearchPhotosData::from([
        'text' => 'sunset',
        'extras' => ['owner_name', 'url_m', 'license', 'geo'],
        'perPage' => 1,
    ]));
    $photo = $search->data['photos']['photo'][0] ?? [];
    $owner = (string) ($photo['owner'] ?? '');
    $photoId = (string) ($photo['id'] ?? '');
    $place = $flickr->places()->find(['query' => 'Ho Chi Minh City']);
    $placeData = $place->data['places']['place'][0] ?? [];
    $groups = $flickr->groups()->search(['text' => 'landscape', 'per_page' => 1]);
    $group = $groups->data['groups']['group'][0] ?? [];
    $photosets = $owner !== '' ? $flickr->photosets()->getList($owner) : null;
    $photoset = $photosets?->data['photosets']['photoset'][0] ?? [];
    $pandas = $flickr->panda()->getList();
    $panda = $pandas->data['pandas']['panda'][0]['_content'] ?? 'ling ling';

    return [
        'photo_id' => $photoId,
        'owner_id' => $owner,
        'group_id' => (string) ($group['nsid'] ?? ''),
        'place_id' => (string) ($placeData['place_id'] ?? ''),
        'photoset_id' => (string) ($photoset['id'] ?? ''),
        'panda_name' => (string) $panda,
    ];
}

/**
 * @param  array<string, mixed>  $context
 * @return array<string, Closure(Flickr): ApiResponseData>
 */
function flickr_live_samples(array $context): array
{
    $photoId = (string) $context['photo_id'];
    $ownerId = (string) $context['owner_id'];
    $groupId = (string) $context['group_id'];
    $placeId = (string) $context['place_id'];
    $photosetId = (string) $context['photoset_id'];
    $pandaName = (string) $context['panda_name'];

    return array_filter([
        'flickr.activity.userComments' => static fn (Flickr $flickr) => $flickr->activity()->userComments(['per_page' => 1]),
        'flickr.activity.userPhotos' => static fn (Flickr $flickr) => $flickr->activity()->userPhotos(['per_page' => 1]),
        'flickr.blogs.getServices' => static fn (Flickr $flickr) => $flickr->blogs()->getServices(),
        'flickr.blogs.getList' => static fn (Flickr $flickr) => $flickr->blogs()->getList(),
        'flickr.cameras.getBrandModels' => static fn (Flickr $flickr) => $flickr->cameras()->getBrandModels(['brand' => 'Canon']),
        'flickr.cameras.getBrands' => static fn (Flickr $flickr) => $flickr->cameras()->getBrands(),
        'flickr.collections.getTree' => $ownerId !== '' ? static fn (Flickr $flickr) => $flickr->collections()->getTree(['user_id' => $ownerId]) : null,
        'flickr.commons.getInstitutions' => static fn (Flickr $flickr) => $flickr->commons()->getInstitutions(),
        'flickr.contacts.getPublicList' => $ownerId !== '' ? static fn (Flickr $flickr) => $flickr->contacts()->getPublicList(['user_id' => $ownerId]) : null,
        'flickr.contacts.getList' => static fn (Flickr $flickr) => $flickr->contacts()->getList(['filter' => 'all']),
        'flickr.contacts.getListRecentlyUploaded' => static fn (Flickr $flickr) => $flickr->contacts()->getListRecentlyUploaded(['date_lastupload' => time() - 3600]),
        'flickr.contacts.getTaggingSuggestions' => static fn (Flickr $flickr) => $flickr->contacts()->getTaggingSuggestions(),
        'flickr.favorites.getList' => $ownerId !== '' ? static fn (Flickr $flickr) => $flickr->favorites()->getList(['user_id' => $ownerId, 'per_page' => 1]) : null,
        'flickr.favorites.getPublicList' => $ownerId !== '' ? static fn (Flickr $flickr) => $flickr->favorites()->getPublicList(['user_id' => $ownerId, 'per_page' => 1]) : null,
        'flickr.galleries.getList' => $ownerId !== '' ? static fn (Flickr $flickr) => $flickr->galleries()->getList(['user_id' => $ownerId, 'per_page' => 1]) : null,
        'flickr.galleries.getListForPhoto' => $photoId !== '' ? static fn (Flickr $flickr) => $flickr->galleries()->getListForPhoto(['photo_id' => $photoId, 'per_page' => 1]) : null,
        'flickr.groups.getInfo' => $groupId !== '' ? static fn (Flickr $flickr) => $flickr->groups()->getInfo(['group_id' => $groupId]) : null,
        'flickr.groups.pools.getPhotos' => $groupId !== '' ? static fn (Flickr $flickr) => $flickr->groupsPools()->getPhotos(['group_id' => $groupId, 'per_page' => 1]) : null,
        'flickr.groups.members.getList' => $groupId !== '' ? static fn (Flickr $flickr) => $flickr->groupsMembers()->getList(['group_id' => $groupId, 'per_page' => 1]) : null,
        'flickr.groups.pools.getGroups' => static fn (Flickr $flickr) => $flickr->groupsPools()->getGroups(['per_page' => 1]),
        'flickr.groups.search' => static fn (Flickr $flickr) => $flickr->groups()->search(['text' => 'landscape', 'per_page' => 1]),
        'flickr.interestingness.getList' => static fn (Flickr $flickr) => $flickr->interestingness()->getList(['per_page' => 1]),
        'flickr.machinetags.getNamespaces' => static fn (Flickr $flickr) => $flickr->machinetags()->getNamespaces(['per_page' => 1]),
        'flickr.machinetags.getPairs' => static fn (Flickr $flickr) => $flickr->machinetags()->getPairs(['per_page' => 1]),
        'flickr.machinetags.getPredicates' => static fn (Flickr $flickr) => $flickr->machinetags()->getPredicates(['per_page' => 1]),
        'flickr.machinetags.getRecentValues' => static fn (Flickr $flickr) => $flickr->machinetags()->getRecentValues(['per_page' => 1]),
        'flickr.machinetags.getValues' => static fn (Flickr $flickr) => $flickr->machinetags()->getValues(['namespace' => 'flickr', 'predicate' => 'place', 'per_page' => 1]),
        'flickr.panda.getList' => static fn (Flickr $flickr) => $flickr->panda()->getList(),
        'flickr.panda.getPhotos' => static fn (Flickr $flickr) => $flickr->panda()->getPhotos(['panda_name' => $pandaName, 'per_page' => 1]),
        'flickr.people.getInfo' => $ownerId !== '' ? static fn (Flickr $flickr) => $flickr->people()->getInfo($ownerId) : null,
        'flickr.people.getGroups' => $ownerId !== '' ? static fn (Flickr $flickr) => $flickr->people()->getGroups(['user_id' => $ownerId]) : null,
        'flickr.people.getLimits' => static fn (Flickr $flickr) => $flickr->people()->getLimits(),
        'flickr.people.getPhotos' => $ownerId !== '' ? static fn (Flickr $flickr) => $flickr->people()->getPhotos(['user_id' => $ownerId, 'per_page' => 1]) : null,
        'flickr.people.getPhotosOf' => $ownerId !== '' ? static fn (Flickr $flickr) => $flickr->people()->getPhotosOf(['user_id' => $ownerId, 'per_page' => 1]) : null,
        'flickr.people.getPublicGroups' => $ownerId !== '' ? static fn (Flickr $flickr) => $flickr->people()->getPublicGroups(['user_id' => $ownerId]) : null,
        'flickr.people.getPublicPhotos' => $ownerId !== '' ? static fn (Flickr $flickr) => $flickr->people()->getPublicPhotos(['user_id' => $ownerId, 'per_page' => 1]) : null,
        'flickr.people.getUploadStatus' => static fn (Flickr $flickr) => $flickr->people()->getUploadStatus(),
        'flickr.photos.comments.getList' => $photoId !== '' ? static fn (Flickr $flickr) => $flickr->photosComments()->getList(['photo_id' => $photoId]) : null,
        'flickr.photos.comments.getRecentForContacts' => static fn (Flickr $flickr) => $flickr->photosComments()->getRecentForContacts(['per_page' => 1]),
        'flickr.photos.getAllContexts' => $photoId !== '' ? static fn (Flickr $flickr) => $flickr->photos()->getAllContexts(['photo_id' => $photoId]) : null,
        'flickr.photos.getContactsPhotos' => static fn (Flickr $flickr) => $flickr->photos()->getContactsPhotos(['count' => 1]),
        'flickr.photos.getContactsPublicPhotos' => $ownerId !== '' ? static fn (Flickr $flickr) => $flickr->photos()->getContactsPublicPhotos(['user_id' => $ownerId, 'count' => 1]) : null,
        'flickr.photos.getContext' => $photoId !== '' ? static fn (Flickr $flickr) => $flickr->photos()->getContext(['photo_id' => $photoId]) : null,
        'flickr.photos.getCounts' => static fn (Flickr $flickr) => $flickr->photos()->getCounts([
            'dates' => gmdate('Y-m-d', strtotime('-1 day')).','.gmdate('Y-m-d'),
        ]),
        'flickr.photos.getExif' => $photoId !== '' ? static fn (Flickr $flickr) => $flickr->photos()->getExif($photoId) : null,
        'flickr.photos.getFavorites' => $photoId !== '' ? static fn (Flickr $flickr) => $flickr->photos()->getFavorites(['photo_id' => $photoId, 'per_page' => 1]) : null,
        'flickr.photos.getInfo' => $photoId !== '' ? static fn (Flickr $flickr) => $flickr->photos()->getInfo($photoId) : null,
        'flickr.photos.getNotInSet' => static fn (Flickr $flickr) => $flickr->photos()->getNotInSet(['per_page' => 1]),
        'flickr.photos.getPerms' => $photoId !== '' ? static fn (Flickr $flickr) => $flickr->photos()->getPerms(['photo_id' => $photoId]) : null,
        'flickr.photos.getPopular' => $ownerId !== '' ? static fn (Flickr $flickr) => $flickr->photos()->getPopular(['user_id' => $ownerId, 'per_page' => 1]) : null,
        'flickr.photos.getRecent' => static fn (Flickr $flickr) => $flickr->photos()->getRecent(['per_page' => 1]),
        'flickr.photos.getSizes' => $photoId !== '' ? static fn (Flickr $flickr) => $flickr->photos()->getSizes($photoId) : null,
        'flickr.photos.getUntagged' => static fn (Flickr $flickr) => $flickr->photos()->getUntagged(['per_page' => 1]),
        'flickr.photos.getWithGeoData' => static fn (Flickr $flickr) => $flickr->photos()->getWithGeoData(['per_page' => 1]),
        'flickr.photos.getWithoutGeoData' => static fn (Flickr $flickr) => $flickr->photos()->getWithoutGeoData(['per_page' => 1]),
        'flickr.photos.licenses.getAvailable' => $photoId !== '' ? static fn (Flickr $flickr) => $flickr->photosLicenses()->getAvailable(['photo_id' => $photoId]) : null,
        'flickr.photos.licenses.getInfo' => static fn (Flickr $flickr) => $flickr->photosLicenses()->getInfo(),
        'flickr.photos.licenses.getLicenseHistory' => $photoId !== '' ? static fn (Flickr $flickr) => $flickr->photosLicenses()->getLicenseHistory(['photo_id' => $photoId]) : null,
        'flickr.photos.people.getList' => $photoId !== '' ? static fn (Flickr $flickr) => $flickr->photosPeople()->getList(['photo_id' => $photoId]) : null,
        'flickr.photos.recentlyUpdated' => static fn (Flickr $flickr) => $flickr->photos()->recentlyUpdated(['min_date' => time() - 86400, 'per_page' => 1]),
        'flickr.photos.suggestions.getList' => static fn (Flickr $flickr) => $flickr->photosSuggestions()->getList(['per_page' => 1]),
        'flickr.photos.search' => static fn (Flickr $flickr) => $flickr->photos()->search(SearchPhotosData::from(['text' => 'sunset', 'perPage' => 1])),
        'flickr.photosets.comments.getList' => $photosetId !== '' ? static fn (Flickr $flickr) => $flickr->photosetsComments()->getList(['photoset_id' => $photosetId]) : null,
        'flickr.photosets.getInfo' => $photosetId !== '' ? static fn (Flickr $flickr) => $flickr->photosets()->getInfo($photosetId) : null,
        'flickr.photosets.getList' => $ownerId !== '' ? static fn (Flickr $flickr) => $flickr->photosets()->getList($ownerId) : null,
        'flickr.photosets.getPhotos' => $photosetId !== '' ? static fn (Flickr $flickr) => $flickr->photosets()->getPhotos($photosetId, [], 1, 1) : null,
        'flickr.places.find' => static fn (Flickr $flickr) => $flickr->places()->find(['query' => 'Ho Chi Minh City']),
        'flickr.places.findByLatLon' => static fn (Flickr $flickr) => $flickr->places()->findByLatLon(['lat' => 10.7769, 'lon' => 106.7009]),
        'flickr.places.getChildrenWithPhotosPublic' => $placeId !== '' ? static fn (Flickr $flickr) => $flickr->places()->getChildrenWithPhotosPublic(['place_id' => $placeId]) : null,
        'flickr.places.getInfo' => $placeId !== '' ? static fn (Flickr $flickr) => $flickr->places()->getInfo(['place_id' => $placeId]) : null,
        'flickr.places.getPlaceTypes' => static fn (Flickr $flickr) => $flickr->places()->getPlaceTypes(),
        'flickr.places.getShapeHistory' => $placeId !== '' ? static fn (Flickr $flickr) => $flickr->places()->getShapeHistory(['place_id' => $placeId]) : null,
        'flickr.places.getTopPlacesList' => static fn (Flickr $flickr) => $flickr->places()->getTopPlacesList(['place_type_id' => 7]),
        'flickr.places.placesForBoundingBox' => static fn (Flickr $flickr) => $flickr->places()->placesForBoundingBox(['bbox' => '-122.43,37.77,-122.41,37.79', 'place_type_id' => 22]),
        'flickr.places.placesForTags' => static fn (Flickr $flickr) => $flickr->places()->placesForTags(['tags' => 'sunset', 'place_type_id' => 7]),
        'flickr.places.placesForContacts' => static fn (Flickr $flickr) => $flickr->places()->placesForContacts(['place_type_id' => 7]),
        'flickr.places.placesForUser' => static fn (Flickr $flickr) => $flickr->places()->placesForUser(['place_type_id' => 7]),
        'flickr.places.resolvePlaceId' => $placeId !== '' ? static fn (Flickr $flickr) => $flickr->places()->resolvePlaceId(['place_id' => $placeId]) : null,
        'flickr.places.tagsForPlace' => $placeId !== '' ? static fn (Flickr $flickr) => $flickr->places()->tagsForPlace(['place_id' => $placeId]) : null,
        'flickr.profile.getProfile' => $ownerId !== '' ? static fn (Flickr $flickr) => $flickr->profile()->getProfile(['user_id' => $ownerId]) : null,
        'flickr.prefs.getContentType' => static fn (Flickr $flickr) => $flickr->prefs()->getContentType(),
        'flickr.prefs.getGeoPerms' => static fn (Flickr $flickr) => $flickr->prefs()->getGeoPerms(),
        'flickr.prefs.getHidden' => static fn (Flickr $flickr) => $flickr->prefs()->getHidden(),
        'flickr.prefs.getPrivacy' => static fn (Flickr $flickr) => $flickr->prefs()->getPrivacy(),
        'flickr.prefs.getSafetyLevel' => static fn (Flickr $flickr) => $flickr->prefs()->getSafetyLevel(),
        'flickr.push.getSubscriptions' => static fn (Flickr $flickr) => $flickr->push()->getSubscriptions(),
        'flickr.push.getTopics' => static fn (Flickr $flickr) => $flickr->push()->getTopics(),
        'flickr.reflection.getMethodInfo' => static fn (Flickr $flickr) => $flickr->reflection()->getMethodInfo(['method_name' => 'flickr.photos.search']),
        'flickr.reflection.getMethods' => static fn (Flickr $flickr) => $flickr->reflection()->getMethods(),
        'flickr.stats.getCSVFiles' => static fn (Flickr $flickr) => $flickr->stats()->getCSVFiles(),
        'flickr.stats.getPopularPhotos' => static fn (Flickr $flickr) => $flickr->stats()->getPopularPhotos(['date' => gmdate('Y-m-d', strtotime('-1 day')), 'per_page' => 1]),
        'flickr.stats.getTotalViews' => static fn (Flickr $flickr) => $flickr->stats()->getTotalViews(['date' => gmdate('Y-m-d', strtotime('-1 day'))]),
        'flickr.tags.getClusterPhotos' => static fn (Flickr $flickr) => $flickr->tags()->getClusterPhotos(['tag' => 'sunset', 'cluster_id' => 'landscape', 'per_page' => 1]),
        'flickr.tags.getClusters' => static fn (Flickr $flickr) => $flickr->tags()->getClusters(['tag' => 'sunset']),
        'flickr.tags.getHotList' => static fn (Flickr $flickr) => $flickr->tags()->getHotList(['count' => 1]),
        'flickr.tags.getListPhoto' => $photoId !== '' ? static fn (Flickr $flickr) => $flickr->tags()->getListPhoto(['photo_id' => $photoId]) : null,
        'flickr.tags.getListUser' => $ownerId !== '' ? static fn (Flickr $flickr) => $flickr->tags()->getListUser(['user_id' => $ownerId]) : null,
        'flickr.tags.getListUserPopular' => $ownerId !== '' ? static fn (Flickr $flickr) => $flickr->tags()->getListUserPopular(['user_id' => $ownerId, 'count' => 1]) : null,
        'flickr.tags.getMostFrequentlyUsed' => static fn (Flickr $flickr) => $flickr->tags()->getMostFrequentlyUsed(),
        'flickr.tags.getRelated' => static fn (Flickr $flickr) => $flickr->tags()->getRelated(['tag' => 'sunset']),
        'flickr.test.echo' => static fn (Flickr $flickr) => $flickr->test()->echo(['hello' => 'world']),
        'flickr.test.login' => static fn (Flickr $flickr) => $flickr->test()->login(),
        'flickr.test.null' => static fn (Flickr $flickr) => $flickr->test()->null(),
        'flickr.urls.getGroup' => $groupId !== '' ? static fn (Flickr $flickr) => $flickr->urls()->getGroup(['group_id' => $groupId]) : null,
        'flickr.urls.getUserPhotos' => $ownerId !== '' ? static fn (Flickr $flickr) => $flickr->urls()->getUserPhotos(['user_id' => $ownerId]) : null,
        'flickr.urls.getUserProfile' => $ownerId !== '' ? static fn (Flickr $flickr) => $flickr->urls()->getUserProfile(['user_id' => $ownerId]) : null,
        'flickr.urls.lookupGroup' => $groupId !== '' ? static fn (Flickr $flickr) => $flickr->urls()->lookupGroup(['url' => 'https://www.flickr.com/groups/'.$groupId.'/']) : null,
        'flickr.urls.lookupUser' => $ownerId !== '' ? static fn (Flickr $flickr) => $flickr->urls()->lookupUser(['url' => 'https://www.flickr.com/photos/'.$ownerId.'/']) : null,
    ], static fn (?Closure $sample): bool => $sample !== null);
}

/**
 * @return array{method: string, status: string, duration_ms: int, error: string, data_keys: list<string>}
 */
function flickr_skipped(FlickrMethodDefinition $definition, string $reason): array
{
    return [
        'method' => $definition->name,
        'status' => 'skipped',
        'duration_ms' => 0,
        'error' => $reason,
        'data_keys' => [],
    ];
}

/**
 * @param  list<array{method: string, status: string, duration_ms: int, error: ?string, data_keys: list<string>}>  $results
 * @param  array<string, mixed>  $context
 */
function flickr_echo_results(array $results, array $context): void
{
    $counts = array_count_values(array_column($results, 'status'));

    echo 'JOOservices Flickr live API example report'.PHP_EOL;
    echo 'Discovered public context:'.PHP_EOL;
    echo '  photo_id: '.($context['photo_id'] ?: 'none').PHP_EOL;
    echo '  owner_id: '.($context['owner_id'] ?: 'none').PHP_EOL;
    echo '  group_id: '.($context['group_id'] ?: 'none').PHP_EOL;
    echo '  place_id: '.($context['place_id'] ?: 'none').PHP_EOL;
    echo '  photoset_id: '.($context['photoset_id'] ?: 'none').PHP_EOL.PHP_EOL;
    echo 'Summary:'.PHP_EOL;
    echo '  ok: '.($counts['ok'] ?? 0).PHP_EOL;
    echo '  api-fail: '.($counts['api-fail'] ?? 0).PHP_EOL;
    echo '  exception: '.($counts['exception'] ?? 0).PHP_EOL;
    echo '  skipped: '.($counts['skipped'] ?? 0).PHP_EOL.PHP_EOL;

    foreach ($results as $result) {
        $details = $result['status'];

        if ($result['duration_ms'] > 0) {
            $details .= ' '.$result['duration_ms'].'ms';
        }

        if ($result['error'] !== null && $result['error'] !== '') {
            $details .= ' - '.$result['error'];
        }

        if ($result['data_keys'] !== []) {
            $details .= ' - data: '.implode(', ', $result['data_keys']);
        }

        echo $result['method'].': '.$details.PHP_EOL;
    }
}

function flickr_wrapper_example(string $method): string
{
    return match ($method) {
        'flickr.photos.addTags' => '$flickr->photos()->addTags("PHOTO_ID", ["php", "flickr"]);',
        'flickr.photos.delete' => '$flickr->photos()->delete("PHOTO_ID");',
        'flickr.photos.getExif' => '$flickr->photos()->getExif("PHOTO_ID");',
        'flickr.photos.getInfo' => '$flickr->photos()->getInfo("PHOTO_ID");',
        'flickr.photos.getSizes' => '$flickr->photos()->getSizes("PHOTO_ID");',
        'flickr.photos.removeTag' => '$flickr->photos()->removeTag("TAG_ID");',
        'flickr.photos.search' => '$flickr->photos()->search(SearchPhotosData::from(["text" => "sunset"]));',
        'flickr.photos.setMeta' => '$flickr->photos()->setMeta("PHOTO_ID", "Title", "Description");',
        'flickr.photos.setTags' => '$flickr->photos()->setTags("PHOTO_ID", ["php", "flickr"]);',
        'flickr.people.getInfo' => '$flickr->people()->getInfo("USER_ID");',
        'flickr.people.getUploadStatus' => '$flickr->people()->getUploadStatus();',
        'flickr.photosets.addPhoto' => '$flickr->photosets()->addPhoto("PHOTOSET_ID", "PHOTO_ID");',
        'flickr.photosets.create' => '$flickr->photosets()->create(CreatePhotosetData::from([... ]));',
        'flickr.photosets.getInfo' => '$flickr->photosets()->getInfo("PHOTOSET_ID");',
        'flickr.photosets.getList' => '$flickr->photosets()->getList("USER_ID");',
        'flickr.photosets.getPhotos' => '$flickr->photosets()->getPhotos("PHOTOSET_ID", ["url_m"], 1, 20);',
        'flickr.photosets.removePhoto' => '$flickr->photosets()->removePhoto("PHOTOSET_ID", "PHOTO_ID");',
        default => sprintf(
            '$flickr->%s()->%s([...]);',
            flickr_service_accessor($method),
            flickr_wrapper_method($method)
        ),
    };
}

function flickr_service_accessor(string $method): string
{
    $parts = explode('.', $method);
    $category = $parts[1] ?? '';
    $nested = [
        'auth' => ['oauth'],
        'groups' => ['members', 'pools'],
        'photos' => ['comments', 'geo', 'licenses', 'notes', 'people', 'suggestions', 'transform', 'upload'],
        'photosets' => ['comments'],
    ];

    if (in_array($parts[2] ?? '', $nested[$category] ?? [], true)) {
        $category .= '.'.$parts[2];
    }

    if ($category === 'groups' && ($parts[2] ?? '') === 'discuss') {
        $category = 'groups.discuss.'.($parts[3] ?? 'topics');
    }

    if ($category === 'auth') {
        return 'authApi';
    }

    if ($category === 'auth.oauth') {
        return 'authOauthApi';
    }

    return lcfirst(str_replace(' ', '', ucwords(str_replace('.', ' ', $category))));
}

function flickr_wrapper_method(string $method): string
{
    $parts = explode('.', $method);

    return (string) end($parts);
}

/**
 * @param  list<string>  $argv
 */
function flickr_cli_option(array $argv, string $prefix): ?string
{
    foreach ($argv as $argument) {
        if (str_starts_with($argument, $prefix)) {
            return substr($argument, strlen($prefix));
        }
    }

    return null;
}

function flickr_required_env(string $key): string
{
    $value = getenv($key);

    if (! is_string($value) || trim($value) === '') {
        fwrite(STDERR, $key.' is required.'.PHP_EOL);
        exit(1);
    }

    return $value;
}

function flickr_has_access_token(): bool
{
    return (getenv('FLICKR_ACCESS_TOKEN') ?: '') !== ''
        && (getenv('FLICKR_ACCESS_TOKEN_SECRET') ?: '') !== '';
}

function flickr_duration_ms(float $started): int
{
    return (int) round((microtime(true) - $started) * 1000);
}
