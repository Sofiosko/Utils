<?php

namespace BiteIT\Utils;

trait ArrayWalkerTrait
{
    protected $walker = null;

    public function getData($key, $defaultValue = null){
        return $this->getWalker()->get($key, $defaultValue);
    }

    protected function getWalker(){
        if(isset($this->walker))
            return $this->walker;
        return $this->walker = ArrayWalker::create($this->getDataForWalker());
    }

    abstract protected function getDataForWalker();
}