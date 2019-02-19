<?php

namespace Mission4\SimpleGmail;

use Google_Client;
use Google_Service_Gmail;

class Gmail
{
    protected $token;
    protected $client;

    public function __construct($config)
    {
        $this->client = new Google_Client($config);
        // $this->client->setApplicationName($config['app_name']);
        $this->client->setScopes($config['scope']);
        // $this->client->setAuthConfig($this->getAuthConfig());
        $this->client->setAccessType('offline');
        $this->client->setRedirectUri($config['redirect_uri']);

        return $this;
    }

    public function setToken($token)
    {
        $this->client->setAccessToken($token);
        $this->token = $token;
        return $this;
    }

    public function makeAccessToken($code, $callback = null)
    {
        $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
        $this->client->setAccessToken($accessToken);
        $me = $this->getProfile();
        if ($me) {
            $this->emailAddress = $me->emailAddress;
        }
        return $accessToken;
    }

    public function getClient()
    {
        return $this->client;
    }

    private function getProfile()
    {
        $service = new Google_Service_Gmail($this->client);
        return $service->users->getProfile('me');
    }

    public function newEmail()
    {
        return new SendableMessage($this->client);
    }

    public function isAccessTokenExpired($callback)
    {
        if ($this->client->isAccessTokenExpired()) {
            $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            $callback($this->client->getAccessToken());
        }
    }
}
