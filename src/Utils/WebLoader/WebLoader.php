<?php

namespace BiteSHOP\Utils;

use MatthiasMullie\Minify\JS;

/**
 * TODO vymyslet jak jiz minifkovane jska jenom pridat do minifkovaneho souboru aniz by se museli minifikovat (jelikoz ted vytvari minifkovany soubor minifier)
 * TODO hledat v url/src relativni cesty a prenaset je do webtempu (fonty)
 *
 * Class WebLoader
 * @package BiteIT
 */
class WebLoader
{
    const TYPE_CSS = 1;
    const TYPE_JS = 2;

    /** @var WebLoaderItem[] */
    protected $items = [];

    /** @var int */
    protected $itemsType;

    /** @var string Absolute path */
    protected $basePath;

    /** @var string */
    protected $baseUrl;

    /** @var bool false - will not merge files and add modify time of file at the end of src */
    protected $cache = true;

    /** @var string */
    protected $destinationDir;

    /** @var string */
    protected $destinationUrl;

    /** @var int */
    protected $maxFileAge = 86400;

    /** @var JS|null */
    protected $jsMinifier = null;

    /**
     * @var array Now is supported only minification of css files
     */
    protected static $minizableCssExtensions = [
        "css"
    ];

    /**
     * WebLoader constructor.
     * @param $type
     * @param $basePath
     * @param $baseUrl
     */
    public function __construct($type, $basePath, $baseUrl)
    {
        $basePath = realpath($basePath);
        $this->itemsType = $type;
        $this->basePath = static::unifyPath($basePath);
        $this->baseUrl = static::unifyPath($baseUrl);

        if ($type == static::TYPE_JS)
            $this->jsMinifier = new JS();
    }

    /**
     * @param $fileName
     * @return WebLoaderItem
     * @throws \Exception
     */
    public function addLocal($fileName)
    {
        $fileName = trim($fileName, '/');
        if ($fileName) {
            if (file_exists($this->basePath . $fileName)) {
                if (!$this->cache)
                    $fileName .= '?ts=' . filemtime($this->basePath . $fileName);
                return $this->items[] = new WebLoaderItem($this->baseUrl . $fileName, $this->itemsType, true, $this->basePath . $fileName);
            } else
                throw new \Exception("$fileName was not found in {$this->basePath}");
        } else
            throw new \Exception("Please specify file name");
    }

    /**
     * @param $url
     * @return WebLoaderItem
     * @throws \Exception
     */
    public function addRemote($url)
    {
        if ($url) {
            return $this->items[] = new WebLoaderItem($url, $this->itemsType, false);
        } else
            throw new \Exception("Please specify url");
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
     * @param $seconds
     * @return $this
     */
    public function setMaxFileAge($seconds)
    {
        $this->maxFileAge = $seconds;
        return $this;
    }

    /**
     * @param $dir
     * @param $url
     * @return $this
     */
    public function setDestination($dir, $url)
    {
        $this->destinationDir = static::unifyPath($dir);
        $this->destinationUrl = static::unifyPath($url);
        return $this;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function render()
    {
        if ($this->cache) {
            if (!isset($this->destinationDir))
                throw new \InvalidArgumentException('Please set destination dir');
            if (!isset($this->destinationUrl))
                throw new \InvalidArgumentException('Please set destination url');
        }

        $html = '';
        if ($this->cache && $this->itemsType == static::TYPE_CSS) {
            $itemsToMinimize = [];
            foreach ($this->items as $item) {
                // && !$item->isMinified()
                if ($item->isLocal()) {
                    $itemsToMinimize[] = $item;
                } else
                    $html .= $item->render();
            }
            $html .= $this->_getMinimizedCss($itemsToMinimize)->render();
        } elseif ($this->cache && $this->itemsType == static::TYPE_JS) {
            $itemsToMinimize = [];
            foreach ($this->items as $item) {
                // && !$item->isMinified()
                if ($item->isLocal()) {
                    $itemsToMinimize[] = $item;
                } else
                    $html .= $item->render();
            }
            $html .= $this->_getMinimizedJs($itemsToMinimize)->render();
        } else {
            foreach ($this->items as $item) {
                $html .= $item->render();
            }
        }
        $this->items = [];

        return $html;
    }

    /**
     * Creates one minified single WebLoaderItem from content of set items
     *
     * @param WebLoaderItem[] $items
     * @return WebLoaderItem
     * @throws \Exception
     */
    protected function _getMinimizedCss($items)
    {
        $tempFolder = $this->_getPathToWebTemp();

        $baseHash = $this->_getFilesBaseHash($items);
        $itemsHash = $this->_getFilesHash($items);
        $itemsHashName = "$baseHash-$itemsHash";

        $name = 'style-' . $itemsHashName . '.css';
        $fileName = $tempFolder . $name;
        $displayName = $this->destinationUrl . $name;

        if ($this->isFileObsolete($fileName)) {
            $this->_removeAllOldCSSFiles($baseHash);
            $cssContent = $this->_minimizeAndMergeCss($items);
            file_put_contents($fileName, $cssContent);
        }
        $webLoaderItem = new WebLoaderItem($displayName, static::TYPE_CSS, true);
        return $webLoaderItem;
    }

    /**
     * Creates one minified single WebLoaderItem from content of set items
     *
     * @param WebLoaderItem[] $items
     * @return WebLoaderItem
     * @throws \Exception
     */
    protected function _getMinimizedJs($items)
    {
        $tempFolder = $this->_getPathToWebTemp();

        foreach ($items as $item) {
            if ($item->getFileName()) {
                $this->jsMinifier->add($item->getFileName());
            }
        }

        $baseHash = $this->_getFilesBaseHash($items);
        $itemsHash = $this->_getFilesHash($items);
        $itemsHashName = "$baseHash-$itemsHash";

        $name = 'js-' . $itemsHashName . '.min.js';
        $fileName = $tempFolder . $name;
        $displayName = $this->destinationUrl . $name;

        if ($this->isFileObsolete($fileName)) {
            $this->_removeAllOldJSFiles($baseHash);
            $this->jsMinifier->minify($fileName);
        }
        $webLoaderItem = new WebLoaderItem($displayName, static::TYPE_JS, true);
        return $webLoaderItem;
    }

    protected function isFileObsolete($fileName)
    {
        return !file_exists($fileName) || filemtime($fileName) <= (time() - $this->maxFileAge);
    }

    /**
     * @param WebLoaderItem[] $items
     * @return string
     */
    protected function _getFilesBaseHash($items)
    {
        $itemsKey = "";
        foreach ($items as $item) {
            if ($item->getFileName()) {
                $itemsKey .= $item->getFileName();
            }
        }
        $itemsHash = md5($itemsKey);
        return $itemsHash;
    }

    /**
     * @param WebLoaderItem[] $items
     * @return string
     */
    protected function _getFilesHash($items)
    {
        $itemsKey = "";
        foreach ($items as $item) {
            if ($item->getFileName()) {
                $itemsKey .= $item->getFileName();
                if ($item->isLocal()) {
                    $itemsKey .= '?t=' . $item->getMTime();
                }
            }
        }
        $itemsHash = md5($itemsKey);
        return $itemsHash;
    }

    /**
     * @param WebLoaderItem[] $items
     * @return string
     */
    protected function _minimizeAndMergeCss($items)
    {
        $cssContent = '';
        foreach ($items as $item) {
            $content = file_get_contents($item->getClearPath());
            $cssContent .= $this->minimizeCss($content);
        }
        return $cssContent;
    }

    /**
     * @param $buffer
     * @return mixed|null|string|string[]
     */
    public function minimizeCss($buffer)
    {
        // Remove comments
        $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
        // Remove space after colons
        $buffer = str_replace(': ', ':', $buffer);
        // Remove whitespace
        $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
        return $buffer;
    }

    protected function _removeAllOldCSSFiles($baseHash)
    {
        $files = glob($this->_getPathToWebTemp() . "style-$baseHash-*.css");
        foreach ($files as $file) {
            @unlink($file);
        }
        return $this;
    }

    protected function _removeAllOldJSFiles($baseHash)
    {
        $files = glob($this->_getPathToWebTemp() . "js-$baseHash-*.js");
        foreach ($files as $file) {
            @unlink($file);
        }
        return $this;
    }

    /**
     * @return string
     */
    protected function _getPathToWebTemp()
    {
        $tempFolder = $this->destinationDir;
        if (!file_exists($tempFolder))
            mkdir($tempFolder, 0755, true);

        $tempFolder = realpath($tempFolder) . '/';
        return $tempFolder;
    }

    public static function unifyPath($path)
    {
        return (substr($path, -1) == '/' ? $path : $path . '/');
    }

    /**
     * Checks remote sources, should be used only for one time checks
     *
     * @throws \Exception
     */
    public function validateRemoteSources()
    {
        foreach ($this->items as $item){
            if(!$item->isLocal() && !$this->urlExists($item->getPath())){
                throw new \Exception('Source: '.$item->getPath().' does not exist');
            }
        }
    }

    /**
     * @param $url
     * @return bool
     */
    protected function urlExists($url)
    {
        if (function_exists('curl_init') === false) {
            $h = get_headers($url);
            $status = array();
            preg_match('/HTTP\/.* ([0-9]+) .*/', $h[0], $status);
            if(!isset($status[1]))
                return false;
            return ($status[1] == 200);
        } else {
            $ch = @curl_init($url);
            @curl_setopt($ch, CURLOPT_HEADER, TRUE);
            @curl_setopt($ch, CURLOPT_NOBODY, TRUE);
            @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
            @curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $status = array();
            preg_match('/HTTP\/.* ([0-9]+) .*/', @curl_exec($ch), $status);
            if(!isset($status[1]))
                return false;
            return ($status[1] == 200);
        }
    }
}