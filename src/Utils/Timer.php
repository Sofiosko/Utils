<?php

namespace BiteSHOP\Utils;

class Timer
{
    /** @var Timer */
    protected static $instance;

    /** @var bool */
    protected static $enabled = true;

    /**
     * Holds reference to last started or ended item
     *
     * @var TimedItem|null
     */
    protected $lastProcess = null;

    /**
     * Contains tree list of items
     *
     * @var TimedItem[]
     */
    protected $children = [];

    /**
     * Contains list of items by name as key
     *
     * @var TimedItem[]
     */
    protected $processList = [];

    /**
     * @param $processName
     * @return TimedItem|false
     */
    public static function start($processName)
    {
        if(!static::$enabled)
            return false;

        $timer = static::getInstance();
        $item = $timer->createProcess($processName);

        if (isset($timer->lastProcess)) {
            if ($timer->lastProcess->isFinished()) {
                // if last process is finished we put it into his parent or into root
                $parent = $timer->lastProcess->getParent();
                if (isset($parent)) {
                    $parent->add($item);
                } else {
                    $timer->add($item);
                }
            } else {
                // if last process is not finished we nest it
                $timer->lastProcess->add($item);
            }
        } else {
            // first process goes to root
            $timer->add($item);
        }

        $timer->processList[$processName] = $item;
        $timer->lastProcess = $item;
        return $item;
    }

    /**
     * @param $processName
     * @return TimedItem|false
     */
    public static function end($processName)
    {
        if(!static::$enabled)
            return false;

        $timer = static::getInstance();
        $item = $timer->processList[$processName];
        $item->end();
        $timer->lastProcess = $item;
        unset($timer->processList[$processName]);
        return $item;
    }

    /**
     * @param bool $state
     */
    public static function enable($state = true){
        static::$enabled = $state;
    }

    /**
     * @param TimedItem $item
     * @return $this
     */
    public function add(TimedItem $item)
    {
        $this->children[] = $item;
        return $this;
    }

    /**
     * @param $processName
     * @return TimedItem
     */
    public function createProcess($processName)
    {
        $item = new TimedItem($processName);
        return $item;
    }

    /**
     * @return TimedItem[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return string
     */
    public function renderList()
    {
        return $this->renderBranch($this->getChildren());
    }

    /**
     * @param TimedItem[] $processes
     * @return string
     */
    protected function renderBranch($processes)
    {
        ob_start();
        ?>
        <ul class="process-tree">
            <?php
            foreach ($processes as $process) {
                ?>
                <li class="branch">
                    <span class="name"><?php echo $process->name ?> (<?php echo $process->getLength(4); ?>)</span>
                    <?php
                    if (count($process->getChildren())) {
                        echo $this->renderBranch($process->getChildren());
                    }
                    ?>
                </li>
                <?php
            }
            ?>
        </ul>
        <?php
        return ob_get_clean();
    }

    /**
     * @return Timer
     */
    public static function getInstance()
    {
        return static::$instance instanceof self ? static::$instance : static::$instance = new self();
    }
}