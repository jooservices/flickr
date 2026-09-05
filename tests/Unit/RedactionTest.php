<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use Faker\Factory;
use JOOservices\Flickr\Auth\OAuth1Signer;
use JOOservices\Flickr\Auth\OAuthRequestParamSigner;
use JOOservices\Flickr\Client\FlickrResponseParser;
use JOOservices\Flickr\Config\FlickrEndpoints;
use JOOservices\Flickr\Dtos\Common\RawResponseData;
use JOOservices\Flickr\Enums\HttpMethod;
use JOOservices\Flickr\Exceptions\UploadException;
use JOOservices\Flickr\Support\RandomNonceGenerator;
use JOOservices\Flickr\Support\SensitiveDataRedactor;
use JOOservices\Flickr\Support\SignatureBaseStringBuilder;
use JOOservices\Flickr\Tests\Support\MutableClock;
use PHPUnit\Framework\TestCase;

final class RedactionTest extends TestCase
{
    public function testRepeatedRegistrationKeepsAnActiveSecretProtected(): void
    {
        $faker = Factory::create();
        $secret = $faker->sha256();
        $redactor = new SensitiveDataRedactor();
        for ($index = 0; $index < 500; ++$index) {
            $redactor->registerSecret($secret);
            $redactor->registerSecret($faker->sha256());
        }

        self::assertSame('[redacted]', $redactor->redactText($secret));
    }

    public function testConfiguredCredentialsSurviveRequestSecretEviction(): void
    {
        $faker = Factory::create();
        $secret = $faker->sha256();
        $redactor = new SensitiveDataRedactor([$secret]);
        for ($index = 0; $index < 600; ++$index) {
            $redactor->registerSecret($faker->sha256());
        }

        self::assertSame('[redacted]', $redactor->redactText($secret));
    }

    public function testVerifierIsRedactedInRawAndEncodedTransportDiagnostics(): void
    {
        $faker = Factory::create();
        $verifier = $faker->sha256() . '+/=';
        $redactor = new SensitiveDataRedactor();
        $signer = new OAuthRequestParamSigner(
            new OAuth1Signer(),
            new RandomNonceGenerator(),
            new MutableClock(),
            new SignatureBaseStringBuilder(),
            $redactor,
        );
        $signer->signedParameters(HttpMethod::Post, FlickrEndpoints::OAUTH_ACCESS_TOKEN, ['oauth_verifier' => $verifier], $faker->sha256(), $faker->sha256());

        self::assertSame('[redacted]', $redactor->redactText($verifier));
        self::assertSame('[redacted]', $redactor->redactText(rawurlencode($verifier)));
    }

    public function testJsonApiErrorDoesNotExposeRegisteredCredentials(): void
    {
        $secret = Factory::create()->sha256();
        $redactor = new SensitiveDataRedactor();
        $redactor->registerSecret($secret);
        $response = (new FlickrResponseParser($redactor))->parse(new RawResponseData(
            200,
            [],
            json_encode(['stat' => 'fail', 'code' => 98, 'message' => $secret], JSON_THROW_ON_ERROR),
        ));

        self::assertNotNull($response->error);
        self::assertSame('[redacted]', $response->error->message);
    }

    public function testXmlApiErrorDoesNotExposeRegisteredCredentials(): void
    {
        $secret = Factory::create()->sha256();
        $redactor = new SensitiveDataRedactor();
        $redactor->registerSecret($secret);

        try {
            (new FlickrResponseParser($redactor))->parseUploadXml(new RawResponseData(
                200,
                [],
                '<rsp stat="fail"><err code="98" msg="' . $secret . '"/></rsp>',
            ));
            self::fail('Expected an upload failure.');
        } catch (UploadException $error) {
            self::assertStringNotContainsString($secret, $error->getMessage());
            self::assertStringContainsString('[redacted]', $error->getMessage());
        }
    }
}
