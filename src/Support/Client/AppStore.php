<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Support\Client;

use Fresns\PluginManager\Contracts\ClientInterface;
use Fresns\PluginManager\Traits\HasGuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\StreamInterface;

class AppStore implements ClientInterface
{
    use HasGuzzleClient;

    /**
     * Developer Login.
     *
     * @param  string  $account
     * @param  string  $password
     * @return array
     *
     * @throws GuzzleException
     */
    public function login(string $account, string $password): array
    {
        return $this->httpPostJson('/api/app-store/login', [
            'email' => $account,
            'password' => $password,
        ]);
    }

    /**
     * Developer Register
     * Pending deletion function.
     *
     * @param  string  $account
     * @param  string  $password
     * @param  string  $name
     * @param  string  $passwordConfirmation
     * @return array
     *
     * @throws GuzzleException
     */
    public function register(string $account, string $name, string $password, string $passwordConfirmation): array
    {
        return $this->httpPostJson('/api/app-store/register', [
            'name' => $name,
            'email' => $account,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
        ]);
    }

    /**
     * Select the plugin version to download.
     * Pending deletion function.
     *
     * @param  int  $versionId
     * @return StreamInterface
     *
     * @throws GuzzleException
     */
    public function download(int $versionId): StreamInterface
    {
        try {
            return $this->client()->request('POST', ltrim('/api/app-store/plugins/download/'.$versionId, '/'))->getBody();
        } catch (ClientException $e) {
            $response = $e->getResponse();
            if ($message = data_get(json_decode($response->getBody()->getContents(), true), 'message')) {
                throw new \Exception($message, $e->getCode());
            }
            if ($message = $response->getReasonPhrase()) {
                throw new \Exception($message, $e->getCode());
            }
            throw $e;
        }
    }

    /**
     * Plugin Upload
     * Split into plugin uploads and theme uploads.
     *
     * @param  array  $options
     * @return array
     *
     * @throws GuzzleException
     */
    public function upload(array $options): array
    {
        return $this->request('/api/app-store/plugins', 'POST', $options);
    }

    /**
     * Get the plugins released by the Plugin Marketplace.
     * Pending deletion function.
     *
     * @param  int  $page
     * @return array
     *
     * @throws GuzzleException
     */
    public function plugins(int $page): array
    {
        return $this->httpGet('/api/app-store/plugins', [
            'page' => $page,
            'status' => 'release',
        ]);
    }
}
