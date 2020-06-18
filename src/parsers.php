<?php

namespace Differ\parsers;

use Symfony\Component\Yaml\Yaml;



function parseData($content, $path)
{
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    $parsers = [
        'json' => function ($content) {
            return json_decode($content, false);
        },
        'yaml' => function ($content) {
            return Yaml::parse($content, Yaml::PARSE_OBJECT_FOR_MAP);
        }
    ];

    if (!in_array($ext, array_keys($parsers))) {
        throw new \Exception("Extention \"{$ext}\" not supported.");
    }

    return $parsers[$ext]($content);
}
