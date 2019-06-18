<?php
namespace BiteIT\Utils;

class Subjects
{
    const COUNTRY_CZ = 'cz';
    const COUNTRY_SK = 'sk';

    protected $license;
    protected $cacheTime = 3600;

    public function __construct($license, $cacheTime = null)
    {
        $this->license = $license;

        if(isset($cacheTime))
            $this->cacheTime = $cacheTime;
    }

    public function getCompanyInfo(string $companyCode, $countryCode = Subjects::COUNTRY_CZ){
        if($countryCode == static::COUNTRY_CZ){
            $subject = $this->_getCZCompanyInfo($companyCode);
        } else {
            $subject = $this->_getSKCompanyInfo($companyCode);
        }

        var_dump($subject);
    }

    public function validateTaxNumber(string $taxNumber, $countryCode = Subjects::COUNTRY_CZ){
    }

    public function validateCompanyCode(string $companyCode, $countryCode = Subjects::COUNTRY_CZ){

    }

    protected function _getCZCompanyInfo(string $companyCode){
        $response = new SubjectsResponse();

        $file = @file_get_contents(Helpers::ARES_URL . $companyCode);
        if ($file) $xml = @simplexml_load_string($file);
        $a = array();
        if ($xml)
        {
            $ns = $xml->getDocNamespaces();
            $data = $xml->children($ns['are']);
            $el = $data->children($ns['D'])->VBAS;
            if (strval($el->ICO) == $companyCode)
            {
                $a['ico'] = strval($el->ICO);
                $a['dic'] = strval($el->DIC);
                $a['firma'] = strval($el->OF);
                $a['ulice'] = strval($el->AA->NU);
                if (!empty($el->AA->CO) OR !empty($el->AA->CD))
                {
                    // detekování popisného a orientačního čísla
                    $a['ulice'] .= " ";
                    if (!empty($el->AA->CD)) $a['ulice'] .= strval($el->AA->CD);
                    if (!empty($el->AA->CO) AND !empty($el->AA->CD)) $a['ulice'] .= "/";
                    if (!empty($el->AA->CO)) $a['ulice'] .= strval($el->AA->CO);
                }
                // je fyzicka osoba?
                $a['fyz'] = strpos(strval($el->PF->NPF), 'yzick') !== false ? true : false;
                $a['mesto'] = strval($el->AA->N);
                $a['psc'] = strval($el->AA->PSC);
                $a['stav'] = 1;

            } else
                $a['stav'] = 'IČ firmy nebylo nalezeno. Prosím, ověřte správnost čísla a případně zadejte znovu.';
        } else
            $a['stav'] = 'Databáze ARES není dostupná';

        return $response;
    }

    protected function _getSKCompanyInfo(string $companyCode){
        $response = new SubjectsResponse();

        $html = file_get_contents(static::FINSTAT_SK_URL.$companyCode);
        if($html === false){
            $a['stav'] = 'IČ nenalezeno.';
            return $a;
        }
        $main = preg_match('/<ul class="ul-list m-b-none">(.+?)<\/ul>/sim', $html, $matches);
        if(!$matches[1]){
            $a['stav'] = 'IČ nenalezeno.';
            return $a;
        }
        preg_match_all('/<li.*?>(.+?)<\/li>/sim', $matches[1], $lines);
        $a = [];
        $a['ico'] = $companyCode;
        $a['stav'] = 1;
        foreach ($lines[1] as $line){
            preg_match('/<span*.?>(.+?)<\/span>/sim', $line, $dataMatch);
            $data = html_entity_decode(trim($dataMatch[1]));

            if( strpos($line, 'DIČ') !== false ){
                $a['dic'] = $data;
            }
            else if( stripos($line, 'sídlo') )
            {
                $d = explode("\n", $data);
                $a['firma'] = trim(strip_tags($d[0]));
                $a['fyz'] = strpos($a['firma'], 's. r. o.') === false && strpos($a['firma'], 's.r.o.') === false &&
                    strpos($a['firma'], 'a. s.') === false &&
                    $data = trim($d[1]);
                preg_match('/\d{3} \d{2} /', $data, $pscMatch);
                if($pscMatch){
                    $address = explode($pscMatch[0], $data);

                    $ad = explode("\n", $address[0]);
                    if(trim(strip_tags($ad[0])) == $a['firma']){
                        $a['ulice'] = trim($ad[1]);
                    } else {
                        $a['ulice'] = trim($address[0]);
                    }

                    $a['psc'] = $pscMatch[0];
                    $a['mesto'] = trim($address[1]);
                }
            }
            else if( stripos($line, 'forma') )
            {
                $a['fyz'] = stripos($data, 'fyzick') !== false ? 1 : 0;
            }

        }

        return $response;
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
    public function setCacheTime(int $cacheTime): void
    {
        $this->cacheTime = $cacheTime;
    }
}