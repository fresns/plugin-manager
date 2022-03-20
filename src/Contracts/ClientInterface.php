<?php

namespace Fresns\PluginManager\Contracts;

use Psr\Http\Message\StreamInterface;

interface ClientInterface
{
    /**
     * Developer Login
     *
     * @param  string  $account
     * @param  string  $password
     * @return array
     */
    public function login(string $account, string $password): array;

    /**
     * Developer Register
     * Pending deletion function
     *
     * @param  string  $account
     * @param  string  $password
     * @param  string  $name
     * @param  string  $passwordConfirmation
     * @return array
     */
    public function register(string $account, string $name, string $password, string $passwordConfirmation): array;

    /**
     * Select the plugin version to download.
     * Pending deletion function
     *
     * @param  int  $versionId
     * @return StreamInterface
     */
    public function download(int $versionId): StreamInterface;

    /**
     * Split into plugin uploads and theme uploads
     *
     * @param  array  $options
     * @return array
     */
    public function upload(array $options): array;

    /**
     * Get the plugins released by the Plugin Marketplace. 
     * Pending deletion function
     *
     * @param  int  $page
     * @return array
     */
    public function plugins(int $page): array;
}
