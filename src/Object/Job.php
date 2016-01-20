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

class Job extends AbstractCrudObject
{
    const OPERATION_INSERT = 'insert';
    const OPERATION_QUERY = 'query';
    const OPERATION_UPDATE = 'update';
    const OPERATION_UPSERT = 'upsert';
    const OPERATION_DELETE = 'delete';
    const OPERATION_HARD_DELETE = 'hardDelete';
    const CONCURRENCY_MODE_SERIAL = 'Serial';
    const CONCURRENCY_MODE_PARALLEL = 'Parallel';
    const CONTENT_TYPE_CSV = 'CSV';
    const CONTENT_TYPE_XML = 'XML';
    const CONTENT_TYPE_ZIP_CSV = 'ZIP_CSV';
    const CONTENT_TYPE_ZIP_XML = 'ZIP_XML';

    /**
     * @var string
     */
    protected $messageTemplate = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<jobInfo xmlns="http://www.force.com/2009/06/asyncapi/dataload" />
XML;

    /**
     * @return string
     */
    protected function getEndpoint()
    {
        return 'job';
    }

    /**
     * Create method for the object.
     *
     * @param array $params Additional parameters to include in the request
     *
     * @return Job
     *
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function create(array $params = [])
    {
        if (isset($this->data[static::FIELD_ID]) && $this->data[static::FIELD_ID]) {
            throw new \LogicException('Object already has an ID');
        }

        $message = new \SimpleXMLElement($this->messageTemplate);
        $message->operation = $params['operation'];
        $message->object = $params['object'];
        $message->contentType = $params['contentType'];
        unset($params['operation']);
        unset($params['object']);
        unset($params['contentType']);

        switch ($message->contentType) {
        case self::CONTENT_TYPE_CSV:
            $params['headers']['Content-Type'] = 'text/csv';
            break;
        case self::CONTENT_TYPE_XML:
            $params['headers']['Content-Type'] = 'application/xml';
            break;
        case self::CONTENT_TYPE_ZIP_CSV:
            $params['headers']['Content-Type'] = 'text/csv';
            $params['headers']['Content-Encoding'] = 'gzip';
            break;
        case self::CONTENT_TYPE_ZIP_XML:
            $params['headers']['Content-Type'] = 'application/xml';
            $params['headers']['Content-Encoding'] = 'gzip';
            break;
        default:
            throw new \InvalidArgumentException('Provided Content-Type is invalid');
        }

        $params['body'] = $message->asXML();

        $response = $this->getApi()->call(
            $this->getEndpoint(),
            'POST',
            $params
        );

        $data = is_string($response) ? new \SimpleXMLElement($response) : $response;

        $data = $this->xml2array($data);
        $this->setDataWithoutValidation($data);

        try {
            $this->assureId();
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('Salesforce Job creation request failed with reason: %s', printf($this->getData(), true)));
        }

        return $this;
    }

    /**
     * Close method for the object.
     *
     * @param array $params Additional parameters to include in the request
     *
     * @return Job
     *
     * @throws \LogicException
     */
    public function close(array $params = [])
    {
        if (!(isset($this->data[static::FIELD_ID]) && $this->data[static::FIELD_ID])) {
            throw new \LogicException('Object is not created');
        }

        $message = new \SimpleXMLElement($this->messageTemplate);
        $message->state = 'Closed';
        $params['body'] = $message->asXML();

        $response = $this->getApi()->call(
            $this->getEndpoint().'/'.$this->data[self::FIELD_ID],
            'POST',
            $params
        );

        $data = is_string($response) ? new \SimpleXMLElement($response) : $response;

        $data = $this->xml2array($data);
        $this->setDataWithoutValidation($data);

        return $this;
    }
}
