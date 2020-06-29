<?php

namespace Differ\parsers;

use Symfony\Component\Yaml\Yaml;

const PARSERS = [
    'json' => '\\Differ\parsers\json',
    'yaml' => '\\Differ\parsers\yaml'
];

function json($content)
{
    return json_decode($content, false);
}

function yaml($content)
{
    return Yaml::parse($content, Yaml::PARSE_OBJECT_FOR_MAP);
}
