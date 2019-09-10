<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 10.09.2019
 * Time: 12:05
 */

namespace BiteIT\Utils\FileFetcher;


class ObsolescenceChecker
{
    public static function isObsolete(string $fileName, int $maxLifeTime){
        return !file_exists($fileName) || (time() > (filemtime($fileName) + $maxLifeTime));
    }
}