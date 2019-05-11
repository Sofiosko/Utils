<?php

namespace BiteSHOP\Utils;


class WLCreator
{
    /** @var string Absolute path */
    protected $basePath;

    /** @var string */
    protected $baseUrl;

    /** @var string */
    protected $destinationDir;

    /** @var string */
    protected $destinationUrl;

    /** @var bool false - will not merge files and add modify time of file at the end of src */
    protected $cache = true;

    /**
     * WLCreator constructor.
     * @param $destinationDir
     * @param $destinationUrl
     * @param null $basePath
     * @param null $baseUrl
     */
    public function __construct($destinationDir, $destinationUrl, $basePath = null, $baseUrl = null)
    {
        $this->destinationDir = WebLoader::unifyPath($destinationDir);
        $this->destinationUrl = WebLoader::unifyPath($destinationUrl);

        if (isset($basePath)) {
            $this->setBasePath($basePath);
        }

        if (isset($baseUrl)) {
            $this->setBaseUrl($baseUrl);
        }
    }

    /**
     * @param bool $state
     * @return $this
     */
    public function setCache($state = true)
    {
        $this->cache = $state;
        return $this;
    }

    /**
     * @param null $basePath
     * @param null $baseUrl
     * @return WebLoader
     */
    public function getCssLoader($basePath = null, $baseUrl = null)
    {
        if (isset($basePath)) {
            $this->setBasePath($basePath);
        } elseif (!isset($this->basePath)) {
            throw new \InvalidArgumentException('Please set base path');
        }

        if (isset($baseUrl)) {
            $this->setBaseUrl($baseUrl);
        } elseif (!isset($this->baseUrl)) {
            throw new \InvalidArgumentException('Please set base url');
        }

        $loader = new WebLoader(WebLoader::TYPE_CSS, $this->basePath, $this->baseUrl);
        $loader->setCache($this->cache);
        $loader->setDestination($this->destinationDir, $this->destinationUrl);
        return $loader;
    }

    /**
     * @param null $basePath
     * @param null $baseUrl
     * @return WebLoader
     */
    public function getJsLoader($basePath = null, $baseUrl = null)
    {
        if (isset($basePath)) {
            $this->setBasePath($basePath);
        } elseif (!isset($this->basePath)) {
            throw new \InvalidArgumentException('Please set base path');
        }

        if (isset($baseUrl)) {
            $this->setBaseUrl($baseUrl);
        } elseif (!isset($this->baseUrl)) {
            throw new \InvalidArgumentException('Please set base url');
        }

        $loader = new WebLoader(WebLoader::TYPE_JS, $this->basePath, $this->baseUrl);
        $loader->setCache($this->cache);
        $loader->setDestination($this->destinationDir, $this->destinationUrl);
        return $loader;
    }

    /**
     * @param $path
     * @return $this
     */
    protected function setBasePath($path)
    {
        $path = realpath($path);
        $this->basePath = WebLoader::unifyPath($path);
        return $this;
    }

    /**
     * @param $url
     * @return $this
     */
    protected function setBaseUrl($url)
    {
        $this->baseUrl = WebLoader::unifyPath($url);
        return $this;
    }
}