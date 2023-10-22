<?php

namespace Mov\BlueskyApi;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BlueskyApi {

    const APP_BSKY_FEED_POST = "app.bsky.feed.post";

    private HttpClientInterface $httpClient;

    private array $session;

    public function __construct()
    {
        $this->httpClient = HttpClient::create([
                'headers' => [
                    'Content-Type' => 'application/json; charset=utf-8',
                    'Accept' => 'application/json'
                ],
            ]
        );
    }

    /**
     * @param string $handle
     * @param string $password
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function authenticate(string $handle, string $password): void
    {
        $payload = [
            'identifier' => $handle,
            'password' => $password
        ];

        $response = $this->httpClient->request(
            'POST',
            'https://bsky.social/xrpc/com.atproto.server.createSession',
            [
                'body' => json_encode($payload)
            ]
        );


        $content = json_decode($response->getContent(), true);

        $this->session = $content ?? [];
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function post(string $text, array $embed = [], array $lang = ['de']): void
    {
        $post = [
            '$type' => self::APP_BSKY_FEED_POST,
            'text' => $text,
            'createdAt' => date('c'),
            'embed' => $embed,
            'langs' => $lang,
        ];

        $payload = [
            'repo' => $this->session["did"],
            'collection' => self::APP_BSKY_FEED_POST,
            'record' => $post,
        ];

        $this->httpClient->request(
            'POST',
            'https://bsky.social/xrpc/com.atproto.repo.createRecord',
            [
                'headers' => [
                    'Authorization: Bearer ' . $this->session['accessJwt'],
                ],
                'body' => json_encode($payload)
            ]
        );
    }
}