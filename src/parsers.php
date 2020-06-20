<?php

namespace Differ\parsers;

use Symfony\Component\Yaml\Yaml;

function parseData($content)
{
    $parsers = [
        'json' => function ($content) {
            return json_decode($content, false);
        },
        'yaml' => function ($content) {
            return Yaml::parse($content, Yaml::PARSE_OBJECT_FOR_MAP);
        }
    ];

    if (!in_array($content['ext'], array_keys($parsers))) {
        throw new \Exception("Extention \"{$content['ext']}\" not supported.");
    }

    return $parsers[$content['ext']]($content['content']);
}
