<?php

namespace BiteIT\Utils;

class ArrayWalker implements \Iterator, \ArrayAccess, \Countable
{
    protected $array;
    protected $position = 0;

    /**
     * ArrayWalker constructor.
     * @param array $array
     */
    public function __construct(array $array)
    {
        $this->array = $array;
    }

    /**
     * @param array $array
     * @return ArrayWalker
     */
    public static function create(array $array)
    {
        return new static($array);
    }

    /**
     * @param $key
     * @param null $defaultValue
     * @return mixed|null
     */
    public function get($key, $defaultValue = null)
    {
        $keys = explode("/", $key);
        $data = $this->fetch($keys);
        if (isset($data))
            return $data;
        return $defaultValue;
    }

    /**
     * @param array $keys
     * @param null $currentData
     * @return mixed|null
     */
    protected function fetch(array $keys, $currentData = null)
    {
        $currentData = $currentData ?? $this->array;
        $actualKey = array_values($keys)[0];
        if (isset($currentData[$actualKey])) {
            if (count($keys) == 1) {
                if ($currentData[$actualKey] === 'true')
                    $currentData[$actualKey] = true;
                elseif ($currentData[$actualKey] === 'false')
                    $currentData[$actualKey] = false;
                return $currentData[$actualKey];
            } else {
                unset($keys[array_keys($keys)[0]]);
                return $this->fetch($keys, $currentData[$actualKey]);
            }
        }
        return null;
    }

    public function toArray(): array
    {
        return (array)$this->array;
    }

    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->array[] = $value;
        } else {
            $this->array[$offset] = $value;
        }
    }

    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->array[$offset]);
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->array[$offset]);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return isset($this->array[$offset]) ? $this->array[$offset] : null;
    }

    #[\ReturnTypeWillChange]
    public function rewind()
    {
        $this->position = 0;
    }

    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->array[$this->key()];
    }

    #[\ReturnTypeWillChange]
    public function key()
    {
        return array_keys($this->array)[$this->position];
    }

    #[\ReturnTypeWillChange]
    public function next()
    {
        ++$this->position;
    }

    #[\ReturnTypeWillChange]
    public function valid()
    {
        return isset(array_keys($this->array)[$this->position]);
    }

    #[\ReturnTypeWillChange]
    public function count()
    {
        return count($this->array);
    }
}
