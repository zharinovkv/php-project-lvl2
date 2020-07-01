<?php

namespace Differ\parsers;

use Symfony\Component\Yaml\Yaml;

function selectParser($extention)
{
    switch ($extention) {
        case 'json':
            return fn ($content) => json_decode($content, false);
        case 'yaml':
            return fn ($content) => Yaml::parse($content, Yaml::PARSE_OBJECT_FOR_MAP);
        default:
            throw new \Exception("Extention \"{$extention}\" not supported.");
    }
}
