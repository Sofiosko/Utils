<?php
namespace BiteIT\Utils;

class Subject
{
    public  $companyCode,
            $taxNumber,
            $companyName,
            $isCompany = false,
            $street,
            $zip,
            $city,

            $hasValidCompanyCode = false,
            $hasValidTaxNumber = false;
}