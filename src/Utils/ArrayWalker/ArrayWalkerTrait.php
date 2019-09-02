<?php

namespace BiteIT\Utils;

trait ArrayWalkerTrait
{
    protected $arrayWalker;

    abstract public function getDataContainerName();

    public function getData($key, $defaultValue = null){
        return $this->getArrayWalker()->get($key, $defaultValue);
    }

    protected function getArrayWalker(){
        if(isset($this->arrayWalker))
            return $this->arrayWalker;

        if(!isset($this->{$this->getDataContainerName()}))
            throw new \InvalidArgumentException('Property: '.$this->getDataContainerName().' does not exists');

        if(!is_array($this->{$this->getDataContainerName()}))
            throw new \InvalidArgumentException('Property: '.$this->getDataContainerName().' must be array');

        return $this->arrayWalker = ArrayWalker::create($this->{$this->getDataContainerName()});
    }
}