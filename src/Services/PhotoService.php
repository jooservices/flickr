<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use InvalidArgumentException;
use JOOservices\Flickr\Contracts\Services\PhotoServiceContract;
use JOOservices\Flickr\Contracts\Services\RawApiServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\DTO\Common\PaginationOptionsData;
use JOOservices\Flickr\DTO\Common\RequestOptionsData;
use JOOservices\Flickr\DTO\Photos\PhotoData;
use JOOservices\Flickr\DTO\Photos\PhotoExifData;
use JOOservices\Flickr\DTO\Photos\PhotoInfoData;
use JOOservices\Flickr\DTO\Photos\PhotoSizeData;
use JOOservices\Flickr\DTO\Photos\SearchPhotosData;
use JOOservices\Flickr\Enums\SortOrder;
use JOOservices\Flickr\Hydrators\PhotoHydrator;
use JOOservices\Flickr\Pagination\Paginator;
use JOOservices\Flickr\Support\ListNormalizer;

final class PhotoService extends AbstractRawService implements PhotoServiceContract
{
    public function __construct(
        RawApiServiceContract $raw,
        private PhotoHydrator $hydrator = new PhotoHydrator,
        private Paginator $paginator = new Paginator,
    ) {
        parent::__construct($raw);
    }

    public function addTags(string $photoId, array $tags): ApiResponseData
    {
        return $this->callRaw('flickr.photos.addTags', [
            'photo_id' => $this->requireId($photoId, 'photo'),
            'tags' => $this->tags($tags),
        ]);
    }

    public function delete(string $photoId): ApiResponseData
    {
        return $this->callRaw('flickr.photos.delete', ['photo_id' => $this->requireId($photoId, 'photo')]);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getAllContexts(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.getAllContexts', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getContactsPhotos(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.getContactsPhotos', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getContactsPublicPhotos(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.getContactsPublicPhotos', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getContext(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.getContext', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getCounts(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.getCounts', $parameters);
    }

    public function getExif(string $photoId): ApiResponseData
    {
        return $this->callRaw('flickr.photos.getExif', ['photo_id' => $this->requireId($photoId, 'photo')]);
    }

    public function getExifData(string $photoId): PhotoExifData
    {
        return $this->hydrator->exif($this->getExif($photoId));
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getFavorites(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.getFavorites', $parameters);
    }

    public function getInfo(string $photoId): ApiResponseData
    {
        return $this->callRaw('flickr.photos.getInfo', ['photo_id' => $this->requireId($photoId, 'photo')]);
    }

    public function getInfoData(string $photoId): PhotoInfoData
    {
        return $this->hydrator->photoInfo($this->getInfo($photoId));
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getNotInSet(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.getNotInSet', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPerms(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.getPerms', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPopular(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.getPopular', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getRecent(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.getRecent', $parameters);
    }

    public function getSizes(string $photoId): ApiResponseData
    {
        return $this->callRaw('flickr.photos.getSizes', ['photo_id' => $this->requireId($photoId, 'photo')]);
    }

    /**
     * @return list<PhotoSizeData>
     */
    public function getSizesData(string $photoId): array
    {
        return $this->hydrator->sizes($this->getSizes($photoId));
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getUntagged(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.getUntagged', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getWithGeoData(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.getWithGeoData', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getWithoutGeoData(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.getWithoutGeoData', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function recentlyUpdated(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.recentlyUpdated', $parameters);
    }

    public function removeTag(string $tagId): ApiResponseData
    {
        return $this->callRaw('flickr.photos.removeTag', ['tag_id' => $this->requireId($tagId, 'tag')]);
    }

    public function search(SearchPhotosData $data): ApiResponseData
    {
        return $this->callRaw('flickr.photos.search', $this->searchParameters($data));
    }

    /**
     * @return list<PhotoData>
     */
    public function searchData(SearchPhotosData $data): array
    {
        return $this->hydrator->photos($this->search($data));
    }

    /**
     * @return iterable<ApiResponseData>
     */
    public function searchPages(
        SearchPhotosData $data,
        ?PaginationOptionsData $pagination = null,
        ?RequestOptionsData $requestOptions = null,
    ): iterable {
        return $this->paginator->pages(
            fn (int $page, ?int $perPage): ApiResponseData => $this->raw->call(
                'flickr.photos.search',
                $this->searchParameters($data, $page, $perPage),
                $requestOptions,
            ),
            $pagination,
            fn (ApiResponseData $response): bool => ($response->data['photos']['photo'] ?? []) === [],
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function searchParameters(SearchPhotosData $data, ?int $page = null, ?int $perPage = null): array
    {
        return array_merge([
            'text' => $data->text,
            'tags' => $data->tags,
            'user_id' => $data->userId,
            'extras' => $data->extras,
            'page' => $page ?? $data->page,
            'per_page' => $perPage ?? $data->perPage,
            'sort' => $data->sort instanceof SortOrder ? $data->sort->value : $data->sort,
            'tag_mode' => $data->tagMode,
            'safe_search' => $data->safeSearch,
            'license' => $data->license?->value,
            'privacy_filter' => $data->privacyFilter?->value,
        ], $data->extraParameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function setContentType(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.setContentType', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function setDates(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.setDates', $parameters);
    }

    public function setMeta(string $photoId, string $title, ?string $description = null): ApiResponseData
    {
        if (trim($title) === '') {
            throw new InvalidArgumentException('Photo title is required.');
        }

        return $this->callRaw('flickr.photos.setMeta', [
            'photo_id' => $this->requireId($photoId, 'photo'),
            'title' => $title,
            'description' => $description,
        ]);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function setPerms(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.setPerms', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function setSafetyLevel(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.setSafetyLevel', $parameters);
    }

    public function setTags(string $photoId, array $tags): ApiResponseData
    {
        return $this->callRaw('flickr.photos.setTags', [
            'photo_id' => $this->requireId($photoId, 'photo'),
            'tags' => $this->tags($tags),
        ]);
    }

    /**
     * @param  list<string>  $tags
     */
    private function tags(array $tags): string
    {
        return implode(' ', ListNormalizer::requireNonEmptyTrimmedList($tags, 'tag'));
    }
}
