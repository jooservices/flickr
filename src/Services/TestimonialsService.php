<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\TestimonialsServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class TestimonialsService extends AbstractRawService implements TestimonialsServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function addTestimonial(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.testimonials.addTestimonial', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function approveTestimonial(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.testimonials.approveTestimonial', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function deleteTestimonial(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.testimonials.deleteTestimonial', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function editTestimonial(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.testimonials.editTestimonial', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getAllTestimonialsAbout(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.testimonials.getAllTestimonialsAbout', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getAllTestimonialsAboutBy(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.testimonials.getAllTestimonialsAboutBy', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getAllTestimonialsBy(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.testimonials.getAllTestimonialsBy', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPendingTestimonialsAbout(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.testimonials.getPendingTestimonialsAbout', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPendingTestimonialsAboutBy(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.testimonials.getPendingTestimonialsAboutBy', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPendingTestimonialsBy(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.testimonials.getPendingTestimonialsBy', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getTestimonialsAbout(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.testimonials.getTestimonialsAbout', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getTestimonialsAboutBy(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.testimonials.getTestimonialsAboutBy', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getTestimonialsBy(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.testimonials.getTestimonialsBy', $parameters);
    }
}
