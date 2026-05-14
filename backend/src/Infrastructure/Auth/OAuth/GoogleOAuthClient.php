<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth\OAuth;

final class GoogleOAuthClient implements GoogleOAuthClientInterface
{
    private string $clientId;
    private string $clientSecret;
    private string $redirectUri;

    public function __construct(array $config)
    {
        $this->clientId = $config['client_id'];
        $this->clientSecret = $config['client_secret'];
        $this->redirectUri = $config['redirect_uri'];
    }

    public function getAuthorizationUrl(array $scopes = ['openid', 'email', 'profile'], string $state = ''): string
    {
        $params = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => implode(' ', $scopes),
            'access_type' => 'offline',
            'prompt' => 'select_account',
            'state' => $state,
        ]);

        return 'https://accounts.google.com/o/oauth2/v2/auth?' . $params;
    }

    public function fetchUserFromAuthorizationCode(string $code, ?string $redirectUriOverride = null): GoogleUser
    {
        $redirectUri = $redirectUriOverride ?: $this->redirectUri;

        $tokenResponse = $this->post('https://oauth2.googleapis.com/token', [
            'code' => $code,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ]);

        if (!isset($tokenResponse['access_token'])) {
            throw new \RuntimeException('Unable to obtain access token from Google');
        }

        $accessToken = $tokenResponse['access_token'];

        $userInfo = $this->get('https://www.googleapis.com/oauth2/v3/userinfo', [
            'Authorization: Bearer ' . $accessToken,
        ]);

        if (!isset($userInfo['sub'], $userInfo['email'])) {
            throw new \RuntimeException('Invalid user info received from Google');
        }

        $name = $userInfo['name'] ?? ($userInfo['given_name'] ?? '');

        return new GoogleUser($userInfo['sub'], $userInfo['email'], $name);
    }

    private function post(string $url, array $data): array
    {
        $options = [
            'http' => [
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
                'ignore_errors' => true,
            ],
        ];

        $context = stream_context_create($options);
        $result = @file_get_contents($url, false, $context);
        if ($result === false) {
            throw new \RuntimeException('HTTP request to Google token endpoint failed');
        }

        return json_decode($result, true) ?: [];
    }

    private function get(string $url, array $headers = []): array
    {
        $opts = [
            'http' => [
                'header' => implode("\r\n", $headers) . "\r\n",
                'method' => 'GET',
                'ignore_errors' => true,
            ]
        ];

        $context = stream_context_create($opts);
        $result = @file_get_contents($url, false, $context);
        if ($result === false) {
            throw new \RuntimeException('HTTP request to Google userinfo endpoint failed');
        }

        return json_decode($result, true) ?: [];
    }
}
