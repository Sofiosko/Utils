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
    protected $itemsType = null;
    protected $basePath = null;
    protected $cache = null;
    protected $displayPath = null;
    protected static $minizableCssExtensions = [
        "css"
    ];
    protected static $tempFolder = 'temp/webtemp';
    protected $maxFileAge = 86400;
    protected $jsMinifier = null;

    public function __construct($type, $basePath, $cache = false, $displayPath = null){
        $basePath = realpath($basePath);
        $this->itemsType = $type;
        $this->basePath = (substr($basePath, -1) == '/' ? $basePath : $basePath.'/');
        $this->cache = $cache;
        $this->displayPath = (substr($displayPath, -1) == '/' ? $displayPath : $displayPath.'/');
        $this->jsMinifier = new JS();
    }

    /**
     * @param $fileName
     * @return WebLoaderItem
     * @throws \Exception
     */
    public function addLocal($fileName){
        $fileName = trim($fileName, '/');
        if($fileName){
            if(file_exists($this->basePath.$fileName)){
                $index = count($this->items);
                if(!$this->cache)
                    $fileName .= '?ts='.filemtime($this->basePath.$fileName);
                $this->items[$index] = new WebLoaderItem($this->displayPath.$fileName, $this->itemsType, true, $this->basePath.$fileName);
                return $this->items[$index];
            }
            else
                throw new \Exception("$fileName was not found in {$this->basePath}");
        } else
            throw new \Exception("Please specify file name");
    }

    /**
     * @param $url
     * @return WebLoaderItem
     * @throws \Exception
     */
    public function addRemote($url){
        if($url){
            $index = count($this->items);
            $this->items[$index] = new WebLoaderItem($url, $this->itemsType, false);
            return $this->items[$index];
        }
        else
            throw new \Exception("Please specify url");
    }

    /**
     * @return string
     */
    public function render(){
        $html = '';
        if($this->cache && $this->itemsType == static::TYPE_CSS) {
            $itemsToMinimize = [];
            foreach ($this->items as $item) {
                if ($item->isLocal()) {
                    $itemsToMinimize[] = $item;
                } else
                    $html .= $item->render();
            }
            $html .= $this->_getMinimizedCss($itemsToMinimize)->render();
        } elseif($this->cache && $this->itemsType == static::TYPE_JS) {
            $itemsToMinimize = [];
            foreach($this->items as $item){
                if($item->isLocal() && !$item->isMinified()){
                    $itemsToMinimize[] = $item;
                } else
                    $html .= $item->render();
            }
            $html .= $this->_getMinimizedJs($itemsToMinimize)->render();
        } else {
            foreach($this->items as $item){
                $html .= $item->render();
            }
        }
        $this->items = [];

        return $html;
    }

    /**
     * @param $seconds
     * @param null $minutes
     * @param null $hours
     * @return $this
     */
    public function setMaxFileAge($seconds, $minutes = null, $hours = null){
        if(isset($hours))
            $seconds = $hours * 3600;
        elseif(isset($minutes))
            $seconds = $minutes * 60;
        $this->maxFileAge = $seconds;
        return $this;
    }

    /**
     * @param WebLoaderItem[] $items
     * @return WebLoaderItem
     */
    protected function _getMinimizedCss($items){
        $tempFolder = $this->_getPathToWebTemp();

        $baseHash = $this->_getFilesBaseHash($items);
        $itemsHash = $this->_getFilesHash($items);
        $itemsHashName = "$baseHash-$itemsHash";

        $name = 'style-'.$itemsHashName.'.css';
        $fileName = $tempFolder.$name;
        $displayName = $this->displayPath.''.static::$tempFolder.'/'.$name;
        if(!file_exists($fileName) || filemtime($fileName) <= (time() - $this->maxFileAge)){
            $this->_removeAllOldCSSFiles($baseHash);
            $cssContent = $this->_minimizeAndMergeCss($items);
            file_put_contents($fileName, $cssContent);
        }
        $webLoaderItem = new WebLoaderItem($displayName, static::TYPE_CSS, true);
        return $webLoaderItem;
    }

    /**
     * @param WebLoaderItem[] $items
     * @return WebLoaderItem
     */
    protected function _getMinimizedJs($items){
        $tempFolder = $this->_getPathToWebTemp();

        foreach($items as $item){
            if($item->getFileName()){
                $this->jsMinifier->add($item->getFileName());
            }
        }

        $baseHash = $this->_getFilesBaseHash($items);
        $itemsHash = $this->_getFilesHash($items);
        $itemsHashName = "$baseHash-$itemsHash";

        $name = 'js-'.$itemsHashName.'.min.js';
        $fileName = $tempFolder.$name;
        $displayName = $this->displayPath.''.static::$tempFolder.'/'.$name;
        if(!file_exists($fileName) || filemtime($fileName) <= (time() - (1 * $this->maxFileAge))){
            $this->_removeAllOldJSFiles($baseHash);
            $this->jsMinifier->minify($fileName);
        }
        $webLoaderItem = new WebLoaderItem($displayName, static::TYPE_JS, true);
        return $webLoaderItem;
    }

    /**
     * @param WebLoaderItem[] $items
     * @return string
     */
    protected function _getFilesBaseHash($items){
        $itemsKey = "";
        foreach($items as $item){
            if($item->getFileName()){
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
    protected function _getFilesHash($items){
        $itemsKey = "";
        foreach($items as $item){
            if($item->getFileName()){
                $itemsKey .= $item->getFileName();
                if($item->isLocal()){
                    $itemsKey .= '?t='.$item->getMTime();
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
    protected function _minimizeAndMergeCss($items){
        $cssContent = '';
        foreach($items as $item){
            $content = file_get_contents($item->getClearPath());
            $cssContent .= $this->minimizeCss($content);
        }
        return $cssContent;
    }

    /**
     * @param $buffer
     * @return mixed|null|string|string[]
     */
    public function minimizeCss($buffer){
        // Remove comments
        $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
        // Remove space after colons
        $buffer = str_replace(': ', ':', $buffer);
        // Remove whitespace
        $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
        return $buffer;
    }

    protected function _removeAllOldCSSFiles($baseHash){
        $files = glob($this->_getPathToWebTemp()."style-$baseHash-*.css");
        foreach($files as $file){
            @unlink($file);
        }
        return $this;
    }

    protected function _removeAllOldJSFiles($baseHash){
        $files = glob($this->_getPathToWebTemp()."js-$baseHash-*.js");
        foreach($files as $file){
            @unlink($file);
        }
        return $this;
    }

    /**
     * @return string
     */
    protected function _getPathToWebTemp(){
        $tempFolder = $this->basePath.'/'.static::$tempFolder.'/';
        if(!file_exists($tempFolder))
            mkdir($tempFolder, 0755, true);

        $tempFolder = realpath($tempFolder).'/';
        return $tempFolder;
    }
}