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

class Session
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @var string
     */
    protected $instanceUrl;

    /**
     * @var string
     */
    protected $tokenType;

    /**
     * @var string
     */
    protected $signature;

    /**
     * @var string
     */
    protected $issuedAt;

    /**
     * @param string $accessToken
     * @param string $instanceUrl
     */
    public function __construct($accessToken, $instanceUrl)
    {
        $this->accessToken = $accessToken;
        $this->instanceUrl = $instanceUrl;
    }

    /**
     * @param string $id
     *
     * @return Session
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $instanceUrl
     *
     * @return Session
     */
    public function setInstanceUrl($instanceUrl)
    {
        $this->instanceUrl = $instanceUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getInstanceUrl()
    {
        return $this->instanceUrl;
    }

    /**
     * @param string $issuedAt
     *
     * @return Session
     */
    public function setIssuedAt($issuedAt)
    {
        $this->issuedAt = $issuedAt;
    }

    /**
     * @return string
     */
    public function getIssuedAt()
    {
        return $this->issuedAt;
    }

    /**
     * @param string $signature
     *
     * @return Session
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @param string $tokenType
     *
     * @return Session
     */
    public function setTokenType($tokenType)
    {
        $this->tokenType = $tokenType;

        return $this;
    }

    /**
     * @return string
     */
    public function getTokenType()
    {
        return $this->tokenType;
    }

    /**
     * @param string $accessToken
     *
     * @return Session
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }
}
