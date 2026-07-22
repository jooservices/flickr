<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Contracts\Services;

use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\DTO\Common\RequestOptionsData;
use JOOservices\Flickr\DTO\Upload\ReplacePhotoData;
use JOOservices\Flickr\DTO\Upload\UploadPhotoData;
use JOOservices\Flickr\DTO\Upload\UploadResultData;
use JOOservices\Flickr\DTO\Upload\UploadTicketData;
use JOOservices\Flickr\Upload\TicketPoller;

interface UploadServiceContract
{
    public function upload(UploadPhotoData $data): UploadResultData;

    public function replace(ReplacePhotoData $data): UploadResultData;

    /**
     * @param  list<string>  $ticketIds
     */
    public function checkTickets(array $ticketIds, ?RequestOptionsData $options = null): ApiResponseData;

    /**
     * @param  list<string>  $ticketIds
     * @return list<UploadTicketData>
     */
    public function checkTicketsData(array $ticketIds, ?RequestOptionsData $options = null): array;

    public function ticketPoller(): TicketPoller;
}
