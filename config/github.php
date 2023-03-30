<?php

declare(strict_types=1);

/*
 * This file is part of Laravel GitHub.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the connections below you wish to use as
    | your default connection for all work. Of course, you may use many
    | connections at once using the manager class.
    |
    */

    'default' => env('GITHUB_CONNECTION', 'none'),

    /*
    |--------------------------------------------------------------------------
    | GitHub Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the connections setup for your application. Example
    | configuration has been included, but you may add as many connections as
    | you would like. Note that the 5 supported authentication methods are:
    | "application", "jwt", "none", "password", and "token".
    |
    */

    'connections' => [

        'main' => [
            'token'      => env('GITHUB_TOKEN', 'your-token'),
            'method'     => 'token',
            'cache'      => true,
            // 'backoff'    => false,
            // 'version'    => 'v3',
            // 'enterprise' => false,
        ],

        'app' => [
            'clientId'     => env('GITHUB_CLIENT_ID', 'your-client-id'),
            'clientSecret' => env('GITHUB_CLIENT_SECRET', 'your-client-secret'),
            'method'       => 'application',
            'cache'        => true,
            // 'backoff'      => false,
            // 'version'      => 'v3',
            // 'enterprise'   => false,
        ],

        'jwt' => [
            'token'        => env('GITHUB_TOKEN', 'your-jwt-token'),
            'method'       => 'jwt',
            'cache'        => true,
            // 'backoff'      => false,
            // 'version'      => 'v3',
            // 'enterprise'   => false,
        ],

        'other' => [
            'username'   => env('GITHUB_USERNAME', 'your-username'),
            'password'   => env('GITHUB_PASSWORD', 'your-password'),
            'method'     => 'password',
            'cache'      => true,
            // 'backoff'    => false,
            // 'version'    => 'v3',
            // 'enterprise' => false,
        ],

        'none' => [
            'method'     => 'none',
            'cache'      => true,
            // 'backoff'    => false,
            // 'version'    => 'v3',
            // 'enterprise' => false,
        ],

        'private' => [
            'appId'      => env('GITHUB_PRIVATE_APP_ID', 'your-app-id'),
            'keyPath'    => env('GITHUB_PRIVATE_KEY_PATH', 'your-private-key-path'),
            'method'     => 'private',
            'cache'      => true,
            // 'backoff'    => false,
            // 'version'    => 'v3',
            // 'enterprise' => false,
        ],

    ],

];
