<?php
namespace BiteIT\Utils;

class Subjects
{
    const COUNTRY_CZ = 'cz';
    const COUNTRY_SK = 'sk';

    protected $license;

    public function __construct($license)
    {
        $this->license = $license;
    }

    public function getCompanyInfo(string $companyCode, $countryCode = Subjects::COUNTRY_CZ){
        if($countryCode == static::COUNTRY_CZ){
            $info = $this->_getCZCompanyInfo($companyCode);
        } else {
            $info = $this->_getSKCompanyInfo($companyCode);
        }
    }

    public function validateTaxNumber(string $taxNumber, $countryCode = Subjects::COUNTRY_CZ){
    }

    public function validateCompanyCode(string $companyCode, $countryCode = Subjects::COUNTRY_CZ){

    }

    protected function _getCZCompanyInfo(string $companyCode){

    }

    protected function _getSKCompanyInfo(string $companyCode){

    }
}