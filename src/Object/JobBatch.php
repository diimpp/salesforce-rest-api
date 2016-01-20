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
    const STATE_QUEUED          = 'Queued';
    const STATE_IN_PROGRESS     = 'InProgress';
    const STATE_COMPLETED       = 'Completed';
    const STATE_FAILED          = 'Failed';
    const STATE_NOT_PROCESSED   = 'Not Processed';

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
     * @return JobBatch
     *
     * @throws \LogicException
     * @throws \RuntimeException
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

        try {
            $this->assureId();
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('Salesforce JobBatch creation request failed with reason: %s', printf($this->getData(), true)));
        }

        return $this;
    }

    /**
     * Read object data from the api.
     *
     * @param array $params Additional request parameters
     *
     * @return JobBatch
     */
    public function read(array $params = [])
    {
        $response = $this->getApi()->call(
            $this->job->getEndpoint().'/'.$this->job->id.'/'.$this->getEndpoint().'/'.$this->assureId(),
            'GET',
            $params);

        $data = is_string($response) ? new \SimpleXMLElement($response) : $response;

        $data = $this->xml2array($data);
        $this->setDataWithoutValidation($data);

        return $this;
    }
}
