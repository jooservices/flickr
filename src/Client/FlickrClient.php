<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Client;

use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\Contracts\Auth\FlickrSignerContract;
use JOOservices\Flickr\Contracts\Auth\FlickrTokenStoreContract;
use JOOservices\Flickr\Contracts\Client\FlickrClientContract;
use JOOservices\Flickr\Contracts\Client\FlickrTransportContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\DTO\Common\RequestOptionsData;
use JOOservices\Flickr\Enums\ResponseFormat;
use JOOservices\Flickr\Exceptions\ApiException;
use JOOservices\Flickr\Exceptions\AuthenticationException;
use JOOservices\Flickr\Metadata\FlickrMethodRegistry;
use JOOservices\Flickr\Support\ParameterNormalizer;

final class FlickrClient implements FlickrClientContract
{
    public function __construct(
        private FlickrConfig $config,
        private FlickrTransportContract $transport,
        private FlickrSignerContract $signer,
        private FlickrTokenStoreContract $tokens,
        private FlickrMethodRegistry $registry,
        private FlickrResponseParser $parser = new FlickrResponseParser,
        private ParameterNormalizer $normalizer = new ParameterNormalizer,
    ) {}

    public function call(string $method, array $parameters = [], ?RequestOptionsData $options = null): ApiResponseData
    {
        $options ??= new RequestOptionsData;
        $definition = $this->registry->find($method);
        $parameters = $this->normalizer->normalize($parameters);
        $parameters['method'] = $method;
        $parameters['api_key'] = $this->config->apiKey;
        $parameters['format'] = $this->config->responseFormat->value;

        if ($this->config->responseFormat === ResponseFormat::Json) {
            $parameters['nojsoncallback'] = 1;
        }

        if ($definition->requiresAuth || $options->authenticated) {
            $token = $this->tokens->get();

            if ($token === null) {
                throw new AuthenticationException("Flickr method {$method} requires an OAuth access token.");
            }

            $parameters = array_merge($parameters, $this->signer->sign(
                $definition->httpMethod->value,
                $this->config->restEndpoint,
                $parameters,
                $token->oauthToken,
                $token->oauthTokenSecret,
            ));
        }

        $transportOptions = $definition->httpMethod->value === 'POST'
            ? ['form_params' => $parameters]
            : ['query' => $parameters];

        $raw = $this->transport->request($definition->httpMethod->value, $this->config->restEndpoint, $transportOptions);
        $response = $this->parser->parseApi($raw);

        if (! $response->ok && $options->throwOnApiError) {
            throw new ApiException($response->error->message);
        }

        return $response;
    }
}
