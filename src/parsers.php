<?php

namespace Differ\parsers;

use Symfony\Component\Yaml\Yaml;

function parsers()
{
    return [
        'json' => fn ($content) => json_decode($content, false),
        'yaml' => fn ($content) => Yaml::parse($content, Yaml::PARSE_OBJECT_FOR_MAP)
    ];
}
