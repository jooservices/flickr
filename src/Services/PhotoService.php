<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use InvalidArgumentException;
use JOOservices\Flickr\Contracts\Services\PhotoServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\DTO\Common\PaginationOptionsData;
use JOOservices\Flickr\DTO\Common\RequestOptionsData;
use JOOservices\Flickr\DTO\Photos\SearchPhotosData;

final class PhotoService extends AbstractRawService implements PhotoServiceContract
{
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
     * @return iterable<ApiResponseData>
     */
    public function searchPages(
        SearchPhotosData $data,
        ?PaginationOptionsData $pagination = null,
        ?RequestOptionsData $requestOptions = null,
    ): iterable {
        $pagination ??= new PaginationOptionsData;
        $page = $pagination->startPage;
        $pagesRead = 0;

        while ($pagination->maxPages === null || $pagesRead < $pagination->maxPages) {
            $parameters = $this->searchParameters($data, $page, $pagination->perPage);
            $response = $this->raw->call('flickr.photos.search', $parameters, $requestOptions);

            yield $response;

            $pagesRead++;
            $items = $this->photoItems($response);

            if ($pagination->stopWhenEmpty && $items === []) {
                break;
            }

            if ($response->pagination === null || $page >= $response->pagination->pages) {
                break;
            }

            $page++;
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function searchParameters(SearchPhotosData $data, ?int $page = null, ?int $perPage = null): array
    {
        return array_merge($data->extraParameters, [
            'text' => $data->text,
            'tags' => $data->tags,
            'user_id' => $data->userId,
            'extras' => $data->extras,
            'page' => $page ?? $data->page,
            'per_page' => $perPage ?? $data->perPage,
            'sort' => $data->sort,
            'tag_mode' => $data->tagMode,
            'safe_search' => $data->safeSearch,
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function photoItems(ApiResponseData $response): array
    {
        $photos = $response->data['photos']['photo'] ?? null;

        return is_array($photos) ? array_values($photos) : [];
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

    private function requireId(string $id, string $name): string
    {
        if (trim($id) === '') {
            throw new InvalidArgumentException("Flickr {$name} id is required.");
        }

        return $id;
    }

    /**
     * @param  list<string>  $tags
     */
    private function tags(array $tags): string
    {
        $tags = array_values(array_filter(array_map('trim', $tags), static fn (string $tag): bool => $tag !== ''));

        if ($tags === []) {
            throw new InvalidArgumentException('At least one tag is required.');
        }

        return implode(' ', $tags);
    }
}
