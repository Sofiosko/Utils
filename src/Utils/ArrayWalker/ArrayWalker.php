<?php

namespace BiteSHOP\Utils;

class ArrayWalker
{
    protected $array;

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
}