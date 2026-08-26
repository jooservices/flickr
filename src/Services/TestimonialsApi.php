<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;

/** Generated from resources/api-surface.php. Do not edit by hand. */
final class TestimonialsApi extends AbstractApiService
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function addTestimonial(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.testimonials.addTestimonial', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function approveTestimonial(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.testimonials.approveTestimonial', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function deleteTestimonial(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.testimonials.deleteTestimonial', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function editTestimonial(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.testimonials.editTestimonial', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getAllTestimonialsAbout(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.testimonials.getAllTestimonialsAbout', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getAllTestimonialsAboutBy(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.testimonials.getAllTestimonialsAboutBy', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getAllTestimonialsBy(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.testimonials.getAllTestimonialsBy', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getPendingTestimonialsAbout(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.testimonials.getPendingTestimonialsAbout', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getPendingTestimonialsAboutBy(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.testimonials.getPendingTestimonialsAboutBy', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getPendingTestimonialsBy(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.testimonials.getPendingTestimonialsBy', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getTestimonialsAbout(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.testimonials.getTestimonialsAbout', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getTestimonialsAboutBy(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.testimonials.getTestimonialsAboutBy', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getTestimonialsBy(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.testimonials.getTestimonialsBy', $parameters, $options);
    }
}
