<?php

namespace BiteIT\Utils;

class TimedItem
{
    /** @var TimedItem */
    protected $parent;

    /** @var TimedItem[] */
    protected $children = [];

    /** @var float */
    protected $startTime;

    /** @var float */
    protected $endTime;

    /** @var string */
    public $name;

    public function __construct($name)
    {
        $this->startTime = microtime(true);
        $this->name = $name;
    }

    /**
     * @return $this
     */
    public function end()
    {
        $this->endTime = microtime(true);
        return $this;
    }

    /**
     * @param TimedItem $item
     * @return $this
     */
    public function add(TimedItem $item)
    {
        $item->setParent($this);
        $this->children[] = $item;
        return $this;
    }

    /**
     * @param int $precision
     * @return float
     */
    public function getLength($precision = 4)
    {
        return round($this->endTime - $this->startTime, 4);
    }

    /**
     * @param TimedItem $item
     * @return $this
     */
    public function setParent(TimedItem $item)
    {
        $this->parent = $item;
        return $this;
    }

    /**
     * @return TimedItem
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return TimedItem[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return bool
     */
    public function isFinished()
    {
        return isset($this->endTime);
    }
}