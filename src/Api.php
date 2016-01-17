<?php

/*
 * This file is part of the diimpp/salesforce-rest-api package.
 *
 * (c) Dmitri Perunov
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Diimpp\Salesforce;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Api
{
    /**
     * @var string
     */
    const API_VERSION = '35.0';

    /**
     * @var string
     */
    const DEFAULT_AUTHENTICATION_DOMAIN = 'https://login.salesforce.com';

    /**
     * @var Api
     */
    protected static $instance;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $apiVersion;

    /**
     * @param Client  $httpClient
     * @param Session $session    A Salesforce API session
     */
    public function __construct(Client $httpClient, Session $session)
    {
        $this->httpClient = $httpClient;
        $this->session = $session;
        $this->setApiVersion(self::API_VERSION);
    }

    /**
     * @param string $accessToken
     * @param string $instanceUrl
     *
     * @return Api
     */
    public static function init($accessToken, $instanceUrl)
    {
        $session = new Session($accessToken, $instanceUrl);
        $api = new static(new Client(), $session);
        static::setInstance($api);

        return $api;
    }

    /**
     * @param string $clientId
     * @param string $clientSecret
     * @param string $username
     * @param string $password
     * @param string $authenticationDomain
     *
     * @return Api
     */
    public static function initWithAuthentication($clientId, $clientSecret, $username, $password, $authenticationDomain = null)
    {
        if (!$authenticationDomain) {
            $authenticationDomain = self::DEFAULT_AUTHENTICATION_DOMAIN;
        }
        $httpClient = new Client();

        $response = $httpClient->request('POST', $authenticationDomain.'/services/oauth2/token', [
            'form_params' => [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'grant_type' => 'password',
                'username' => $username,
                'password' => $password,
            ],
        ]);

        if (200 == $response->getStatusCode()) {
            $data = json_decode($response->getBody()->getContents(), true);
            $session = new Session($data['access_token'], $data['instance_url']);

            $session->setId($data['id']);
            $session->setIssuedAt($data['issued_at']);
            $session->setSignature($data['signature']);
            $session->setTokenType($data['token_type']);

            $api = new static($httpClient, $session);
            static::setInstance($api);

            return $api;
        }

        return $response->getStatusCode();
    }

    /**
     * @return Api|null
     */
    public static function instance()
    {
        return static::$instance;
    }

    /**
     * @param Api $instance
     */
    public static function setInstance(Api $instance)
    {
        static::$instance = $instance;
    }

    /**
     * @return string
     */
    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    /**
     * @param string $version
     *
     * @return Api
     */
    public function setApiVersion($version)
    {
        $this->apiVersion = $version;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthenticationDomain()
    {
        return $this->authenticationDomain;
    }

    /**
     * @param string $domain
     *
     * @return Api
     */
    public function setAuthenticationDomain($domain)
    {
        $this->authenticationDomain = $domain;

        return $this;
    }

    /**
     * Make Salesforce REST api calls.
     *
     * @param string $path   API endpoint
     * @param string $method API request type
     * @param array  $params Assoc of request parameters
     *
     * @return mixed
     */
    public function call($path, $method = 'GET', $params = [])
    {
        $headers = [
            'headers' => [
                'X-SFDC-Session' => $this->session->getAccessToken(),
            ],
        ];

        $params = array_merge_recursive($headers, $params);

        try {
            $response = $this
                ->httpClient
                ->request(
                    $method,
                    $this->session->getInstanceUrl().'/services/async/'.$this->getApiVersion().'/'.$path,
                    $params
                );
        } catch (RequestException $e) {
            return $e->getResponse()->getBody().PHP_EOL;
        }

        return $response->getBody()->getContents();
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @return Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }
}
