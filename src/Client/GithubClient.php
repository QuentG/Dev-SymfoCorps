<?php

namespace App\Client;

use App\Dto\GithubDto;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GithubClient
{
    private HttpClientInterface $client;
    private SerializerInterface $serializer;

    private ?string $username = null;

    public function __construct(string $url, string $accessToken, SerializerInterface $serializer)
    {
        $this->client = HttpClient::createForBaseUri($url, [
            'headers' => [
                'Accept' => 'application/vnd.github.v3+json, application/json',
                'Authorization' => 'token ' . $accessToken
            ]
        ]);
        $this->serializer = $serializer;
    }

    private function authenticate(): void
    {
        if ($this->username) {
            return;
        }

        try {
            $response = $this->client->request(Request::METHOD_GET, '/user');
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException('Error . ' . $e->getMessage());
        }

        $this->username = $response->toArray(false)['login'];
    }

    public function getMyInformations(): object
    {
        try {
            $response = $this->client->request(Request::METHOD_GET, '/user');
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException('Error . ' . $e->getMessage());
        }

        return $this->serializer->denormalize($response->toArray(false), GithubDto::class);
    }

    public function getMyRepositories(): array
    {
        $this->authenticate();

        try {
            $response = $this->client->request(Request::METHOD_GET, sprintf('/users/%s/repos', $this->username));
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException('Error . ' . $e->getMessage());
        }

        return $response->toArray(false);
    }
}