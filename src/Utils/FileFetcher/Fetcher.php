<?php
namespace BiteIT\Utils\FileFetcher;

class Fetcher
{
    protected $cacheTime = 86400;
    protected $cacheFolder;

    public function getFileFromURL($sourceUrl){

    }

    public function downloadFile($sourceUrl, $destinationFileName)
    {
        if($this->shouldDownload($sourceUrl)) {
            $contents = $this->fetchFromUrl($sourceUrl);
            if(!$contents)
                return false;

            file_put_contents($destinationFileName, $contents);
            return true;
        }
        return false;
    }

    protected function fetchFromCache($sourceUrl){

    }

    protected function fetchFromUrl($sourceUrl){
        return file_get_contents($sourceUrl);
    }

    protected function shouldDownload(string $destinationFileName){
        if($this->shouldCache()){
            return ObsolescenceChecker::isObsolete($destinationFileName, $this->cacheTime);
        } else {
            return true;
        }
    }

    protected function shouldCache(){
        return isset($this->cacheFolder) && file_exists($this->cacheFolder) && $this->cacheTime > 0;
    }

    /**
     * @param $cacheFolder
     * @param null $cacheTime
     * @return $this
     */
    public function setCache($cacheFolder, $cacheTime = null)
    {
        if (isset($cacheTime))
            $this->cacheTime = $cacheTime;

        $this->cacheFolder = $cacheFolder;
        return $this;
    }

    /**
     * @return int
     */
    public function getCacheTime(): int
    {
        return $this->cacheTime;
    }

    /**
     * @param int $cacheTime
     */
    public function setCacheTime(int $cacheTime): void
    {
        $this->cacheTime = $cacheTime;
    }

    /**
     * @return string
     */
    public function getCacheFolder(): string
    {
        return $this->cacheFolder;
    }

    /**
     * @param string $cacheFolder
     */
    public function setCacheFolder(string $cacheFolder): void
    {
        $this->cacheFolder = $cacheFolder;
    }
}