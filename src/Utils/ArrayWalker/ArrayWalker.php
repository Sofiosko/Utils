<?php

namespace BiteIT\Utils;

class ArrayWalker implements \Iterator, \ArrayAccess, \Countable
{
    protected array $array;
    protected int $position = 0;

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
    public static function create(array $array): ArrayWalker
    {
        return new static($array);
    }

    /**
     * @param $key
     * @param null $defaultValue
     * @return mixed|null
     */
    public function get($key, $defaultValue = null): mixed
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
    protected function fetch(array $keys, $currentData = null): mixed
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
    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->array[] = $value;
        } else {
            $this->array[$offset] = $value;
        }
    }

    #[\ReturnTypeWillChange]
    public function offsetExists($offset): bool
    {
        return isset($this->array[$offset]);
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($offset): void
    {
        unset($this->array[$offset]);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->array[$offset] ?? null;
    }

    #[\ReturnTypeWillChange]
    public function rewind(): void
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
    public function next(): void
    {
        ++$this->position;
    }

    #[\ReturnTypeWillChange]
    public function valid(): bool
    {
        return isset(array_keys($this->array)[$this->position]);
    }

    #[\ReturnTypeWillChange]
    public function count(): int
    {
        return count($this->array);
    }
}
