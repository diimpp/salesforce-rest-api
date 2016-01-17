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

class JobBatch extends AbstractCrudObject
{
    /**
     * @var Job
     */
    protected $job;

    public function __construct(Job $job, Api $api = null)
    {
        $this->job = $job;
        parent::__construct($api);
    }

    /**
     * @return string
     */
    protected function getEndpoint()
    {
        return 'batch';
    }

    public function getJob()
    {
        return $this->job;
    }

    /**
     * Create method for the object.
     *
     * @param array $params Additional parameters to include in the request
     *
     * @return $this
     *
     * @throws \LogicException
     */
    public function create(array $params = [])
    {
        if (isset($this->data[static::FIELD_ID]) && $this->data[static::FIELD_ID]) {
            throw new \LogicException('Object already has an ID');
        }

        $response = $this->getApi()->call(
            $this->job->getEndpoint().'/'.$this->job->id.'/'.$this->getEndpoint(),
            'POST',
            $params
        );

        $data = is_string($response) ? new \SimpleXMLElement($response) : $response;

        $data = $this->xml2array($data);
        $this->setDataWithoutValidation($data);

        return $this;
    }
}
