<?php

namespace Dustin\ImpEx\Api;

use Dustin\ImpEx\Api\Exception\ClientNotAvailableException;
use Dustin\ImpEx\Encapsulation\Encapsulated;
use Dustin\ImpEx\Serializer\Normalizer\EncapsulationNormalizer;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\Serializer;

abstract class ApiClient
{
    protected ?ClientInterface $client = null;

    /**
     * @var bool flag if guzzle client has been created
     */
    private $clientCreated = false;

    abstract protected function createClient(): ?ClientInterface;

    abstract protected function createResponse(ResponseInterface $response, \Exception $e = null): Response;

    /**
     * @throws ClientNotAvailableException if @see getClient() returned null
     */
    public function request(string $method, string $uri, Encapsulated $encapsulation): Response
    {
        $client = $this->getClient();

        if ($client === null) {
            throw new ClientNotAvailableException();
        }

        $serializer = new Serializer($this->getNormalizers());
        $data = $serializer->normalize($encapsulation);

        $response = null;
        $exception = null;

        try {
            /** @var ResponseInterface $response */
            $response = $client->request($method, $uri, $data);
        } catch (RequestException $e) {
            /** @var ResponseInterface $response */
            $response = $e->getResponse();
            $exception = $e;
        }

        return $this->createResponse($response, $exception);
    }

    public function recreateClient(): void
    {
        $this->client = $this->createClient();
        $this->clientCreated = true;
    }

    protected function getClient(): ?ClientInterface
    {
        if ($this->clientCreated === false) {
            $this->recreateClient();
        }

        return $this->client;
    }

    protected function getNormalizers(): array
    {
        return [new EncapsulationNormalizer()];
    }

    /**
     * Adds slash (/) to the end of an url because it's neccessary for requests with @see ClientInterface::request().
     */
    protected function normalizeUrl(string $url): string
    {
        return rtrim($url, '/').'/';
    }
}
