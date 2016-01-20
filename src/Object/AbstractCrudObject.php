<?php

/*
 * This file is part of the diimpp/salesforce-rest-api package.
 *
 * (c) Dmitri Perunov
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Diimpp\Salesforce\Object;

use Diimpp\Salesforce\Api;

abstract class AbstractCrudObject extends AbstractObject
{
    /**
     * @var string
     */
    const FIELD_ID = 'id';

    /**
     * @var Api instance of the Api used by this object
     */
    protected $api;

    /**
     * @param Api $api The Api instance this object should use to make calls
     */
    public function __construct(Api $api = null)
    {
        $this->api = static::assureApi($api);
    }

    /**
     * @return string
     */
    abstract protected function getEndpoint();

    /**
     * @return Api
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * @param Api $api The Api instance this object should use to make calls
     */
    public function setApi(Api $api)
    {
        $this->api = static::assureApi($api);
    }

    /**
     * @param Api|null $instance
     *
     * @return Api
     *
     * @throws \InvalidArgumentException
     */
    protected static function assureApi(Api $instance = null)
    {
        $instance = $instance ?: Api::instance();
        if (!$instance) {
            throw new \InvalidArgumentException(
                'An Api instance must be provided as argument or '.
                'set as instance in the \Diimpp\Salesforce\Api');
        }

        return $instance;
    }

    /**
     * @return string
     *
     * @throws \LogicException
     */
    protected function assureId()
    {
        if (!$this->data[static::FIELD_ID]) {
            throw new \Exception('Field "'.static::FIELD_ID.'" is required.');
        }

        return (string) $this->data[static::FIELD_ID];
    }

    /**
     * @return string
     */
    protected function getNodePath()
    {
        return $this->assureEndpoint().'/'.$this->assureId();
    }

    /**
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function assureEndpoint()
    {
        if (!$this->getEndpoint()) {
            throw new \InvalidArgumentException('$endpoint must be given');
        }

        return $this->getEndpoint();
    }

    /**
     * Read object data from the api.
     *
     * @param array $params Additional request parameters
     *
     * @return $this
     */
    public function read(array $params = [])
    {
        $response = $this->getApi()->call(
            $this->getNodePath(),
            'GET',
            $params);

        $data = is_string($response) ? new \SimpleXMLElement($response) : $response;

        $data = $this->xml2array($data);
        $this->setDataWithoutValidation($data);

        return $this;
    }
}
