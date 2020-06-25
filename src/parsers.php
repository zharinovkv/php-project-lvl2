<?php

namespace Differ\parsers;

use Symfony\Component\Yaml\Yaml;

function parseData($content, $ext)
{
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

function getExtention($path)
{
    $extention = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    if (empty($extention)) {
        throw new \Exception("File \"{$path}\" does not contain the extention.");
    }

    return $extention;
}

