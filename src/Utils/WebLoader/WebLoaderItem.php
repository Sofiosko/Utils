<?php

namespace BiteIT\Utils;

class WebLoaderItem
{
    protected $properties = [];
    protected $displayPath;
    protected $type;
    protected $local;
    protected $fileName;
    protected $clearPath;

    public function __construct($displayPath, $type, $local, $fileName = null)
    {
        $this->displayPath = $displayPath;
        $this->type = $type;
        $this->local = $local;
        $this->fileName = $fileName;
        $this->clearPath = isset($this->fileName) ? explode("?", $this->fileName)[0] : explode("?", $this->displayPath)[0];

        if ($local && !file_exists($this->clearPath)) {
            throw new \Exception("File {$this->clearPath} does not exist");
        }

        if ($this->type == WebLoader::TYPE_CSS) {
            $this->setProperty("rel", "stylesheet");
            $this->setProperty("href", $displayPath);
        } elseif ($this->type == WebLoader::TYPE_JS) {
            $this->setProperty("src", $displayPath);
        }
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function getDisplayPath()
    {
        return $this->displayPath;
    }

    public function getClearPath()
    {
        return $this->clearPath;
    }

    public function getMTime()
    {
        if ($this->isLocal()) {
            return filemtime($this->getClearPath());
        }
        return false;
    }

    public function isLocal()
    {
        return $this->local === true;
    }

    public function isMinified()
    {
        return strpos($this->displayPath, ".min.") !== false;
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