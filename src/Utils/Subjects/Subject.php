<?php
namespace BiteIT\Utils;

class Subject
{
    public  $companyCode,
            $taxNumber,
            $companyName,
            $isCompany = false,
            $street,
            $streetHn,
            $streetOn,
            $streetOnly,
            $zip,
            $city;

    /**
     * @param \stdClass $info
     * @return Subject
     */
    public static function createFromResponse(\stdClass $info){
        $subject = new static();
        $subject->companyCode = $info->__number;
        $subject->taxNumber = $info->__taxNumber;
        $subject->companyName = $info->__company;
        $subject->isCompany = $info->__fyz != 1;
        $subject->street = $info->__street;
        $subject->city = $info->__city;
        $subject->zip = $info->__zip;
        $subject->streetHn = $info->__street_hn ?? null;
        $subject->streetOn = $info->__street_on ?? null;
        $subject->streetOnly = $info->__street_only ?? null;
        return $subject;
    }
}