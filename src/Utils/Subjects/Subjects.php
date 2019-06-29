<?php

namespace BiteIT\Utils;

class Subjects
{
    const COUNTRY_CZ = 'cz';
    const COUNTRY_SK = 'sk';

    const SUBJECTS_API_URL = 'http://subjects.biteit.cz/api';

    protected $license;
    protected $cacheTime = 86400;
    protected $cacheFolder;

    public function __construct($license)
    {
        $this->license = $license;
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
     * @param string $companyCode
     * @param string $countryCode
     * @return Subject|null
     * @throws \Exception
     */
    public function getCompanyInfo(string $companyCode, $countryCode = Subjects::COUNTRY_CZ)
    {
        $res = $this->callApi('get-info', [
            'cc' => $countryCode,
            'number' => $companyCode,
            'key' => $this->license
        ]);

        if ($res) {
            $res = json_decode($res);
            if (isset($res->info)) {
                $res->subject = Subject::createFromResponse($res->info);
            }
            return $res;
        }

        return null;
    }

    /**
     * @param string $taxNumber
     * @param string $countryCode
     * @return bool
     * @throws \Exception
     */
    public function validateTaxNumber(string $taxNumber, $countryCode = Subjects::COUNTRY_CZ)
    {
        $res = $this->callApi('validate-tax-number', [
            'cc' => $countryCode,
            'number' => $taxNumber,
            'key' => $this->license
        ]);

        if ($res) {
            return boolval(json_decode($res)->success);
        }

        return null;
    }

    /**
     * @param string $companyCode
     * @param string $countryCode
     * @return bool
     * @throws \Exception
     */
    public function validateCompanyCode(string $companyCode, $countryCode = Subjects::COUNTRY_CZ)
    {
        $res = $this->callApi('validate-company-code', [
            'cc' => $countryCode,
            'number' => $companyCode,
            'key' => $this->license
        ]);

        if ($res) {
            return boolval(json_decode($res)->success);
        }

        return null;

    }

    /**
     * @return int|null
     */
    public function getCacheTime(): int
    {
        return $this->cacheTime;
    }

    /**
     * @param int|null $cacheTime
     */
    public function setCacheTime(int $cacheTime)
    {
        $this->cacheTime = $cacheTime;
    }

    /**
     * @param string $endpoint
     * @param array $params
     * @param bool $cache
     * @return false|string
     */
    protected function callApi(string $endpoint, array $params, $cache = true)
    {
        $url = static::SUBJECTS_API_URL . '/' . $endpoint . '?' . http_build_query($params);
        $file = realpath($this->cacheFolder) . '/response-' . md5($url) . '.json';

        if ($cache) {
            if (!isset($this->cacheFolder))
                throw new \Exception('Please set cache folder');

            if (!file_exists($this->cacheFolder))
                mkdir($this->cacheFolder, 0755, true);

            if (file_exists($file) && time() < (filemtime($file) + $this->cacheTime)) {
                return file_get_contents($file);
            }
        }

        $response = file_get_contents($url);
        if ($cache)
            file_put_contents($file, $response);
        return $response;
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
    public function setCacheFolder(string $cacheFolder)
    {
        $this->cacheFolder = $cacheFolder;
    }
}