<?php

namespace BiteIT\Utils;

trait ArrayWalkerTrait
{
    protected $walker = null;

    public function getValue($key, $defaultValue = null){
        return $this->getWalker()->get($key, $defaultValue);
    }

    public function hasData(){
        return $this->getWalker()->count() > 0;
    }

    protected function getWalker(){
        if(isset($this->walker))
            return $this->walker;
        return $this->walker = ArrayWalker::create($this->getDataForWalker());
    }

    abstract protected function getDataForWalker();
}