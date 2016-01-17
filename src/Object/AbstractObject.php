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

abstract class AbstractObject
{
    /**
     * Key-value storage.
     *
     * @var mixed[]
     */
    protected $data = [];

    /**
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        } else {
            throw new \InvalidArgumentException($name.' is not a field of '.get_class($this));
        }
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return array_key_exists($name, $this->data);
    }

    /**
     * @param array
     *
     * @return $this
     */
    public function setData(array $data)
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }

        return $this;
    }

    /**
     * Like setData but will skip field validation.
     *
     * @param array
     *
     * @return $this
     */
    public function setDataWithoutValidation(array $data)
    {
        foreach ($data as $key => $value) {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function exportValue($value)
    {
        switch (true) {
        case $value === null:
            break;
        case $value instanceof self:
            $value = $value->exportData();
            break;
        case is_array($value):
            foreach ($value as $key => $sub_value) {
                if ($sub_value === null) {
                    unset($value[$key]);
                } else {
                    $value[$key] = $this->exportValue($sub_value);
                }
            }
            break;
        }

        return $value;
    }

    /**
     * @return array
     */
    public function exportData()
    {
        return $this->exportValue($this->data);
    }

    /**
     * Xml to array conversion.
     *
     * @param $xml
     *
     * @return array
     */
    public function xml2array($xml)
    {
        $json = json_encode($xml);
        $array = json_decode($json, true);

        return $array;
    }

    /**
     * @return string
     */
    public static function className()
    {
        return get_called_class();
    }
}
