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

class JobBatchResult extends AbstractCrudObject
{
    /**
     * @var JobBatch
     */
    protected $jobBatch;

    public function __construct(JobBatch $jobBatch, Api $api = null)
    {
        $this->jobBatch = $jobBatch;
        parent::__construct($api);
    }

    /**
     * @return string
     */
    protected function getEndpoint()
    {
        return 'result';
    }

    public function read(array $params = [])
    {
        $response = $this->getApi()->call(
            $this->jobBatch->getJob()->getEndpoint().'/'.$this->jobBatch->getJob()->id.'/'.$this->jobBatch->getEndpoint().'/'.$this->jobBatch->assureId().'/'.$this->getEndpoint(),
            'GET'
        );
        $data = is_string($response) ? new \SimpleXMLElement($response) : $response;

        $data = $this->xml2array($data);
        $this->setDataWithoutValidation($data);

        return $this;
    }

    public function retrieveData()
    {
        if (!(isset($this->data['result']) && $this->data['result'])) {
            throw new \LogicException('Please call "read" first');
        }

        if (!empty($this->result)) {
            if (is_string($this->result)) {
                $response = $this->getApi()->call(
                    $this->jobBatch->getJob()->getEndpoint().'/'.$this->jobBatch->getJob()->id.'/'.$this->jobBatch->getEndpoint().'/'.$this->jobBatch->assureId().'/'.$this->getEndpoint().'/'.$this->result,
                    'GET'
                );

                return $response;
            } elseif (is_array($this->result)) {
                foreach ($this->result as $resultId) {
                    $response = $this->getApi()->call(
                        $this->jobBatch->getJob()->getEndpoint().'/'.$this->jobBatch->getJob()->id.'/'.$this->jobBatch->getEndpoint().'/'.$this->jobBatch->assureId().'/'.$this->getEndpoint().'/'.$resultId,
                        'GET'
                    );
                }

                return $response;
            }
        }
    }
}
