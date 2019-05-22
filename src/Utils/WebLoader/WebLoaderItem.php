<?php

namespace BiteIT\Utils;

class WebLoaderItem
{
    protected $properties = [];
    protected $path;
    protected $type;
    protected $local;
    protected $clearPath;
    protected $fileName;

    public function __construct($path, $type, $local, $fileName = null)
    {
        $this->path = $path;
        $this->type = $type;
        $this->local = $local;
        $this->fileName = explode("?", $fileName)[0];
        if (isset($fileName) && !file_exists($this->fileName))
            throw new \Exception("{$this->fileName} does not exist");
        $this->clearPath = explode("?", $this->path)[0];

        if ($this->type == WebLoader::TYPE_CSS) {
            $this->setProperty("rel", "stylesheet");
            $this->setProperty("href", $path);
        } elseif ($this->type == WebLoader::TYPE_JS) {
            $this->setProperty("src", $path);
        }
    }

    public function getClearPath()
    {
        return $this->clearPath;
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getMTime()
    {
        if ($this->isLocal()) {
            return filemtime($this->getFileName());
        }
        return false;
    }

    public function isLocal()
    {
        return $this->local === true;
    }

    public function isMinified()
    {
        return strpos($this->path, ".min.") !== false;
    }

    public function __toString()
    {
        return $this->render();
    }

    public function render()
    {
        return ($this->type == WebLoader::TYPE_CSS ? $this->renderCss() : $this->renderJs());
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function setProperty($key, $value)
    {
        $this->properties[$key] = $value;
        return $this;
    }

    protected function renderCss()
    {
        $properties = [];
        foreach ($this->properties as $key => $value) {
            if ($value)
                $properties[] = $key . '="' . $value . '"';
            else
                $properties[] = $key;
        }
        return '<link ' . implode(' ', $properties) . ' />';
    }

    protected function renderJs()
    {
        $properties = [];
        foreach ($this->properties as $key => $value) {
            $properties[] = $key . '="' . $value . '"';
        }
        return '<script ' . implode(' ', $properties) . '></script>';
    }
}