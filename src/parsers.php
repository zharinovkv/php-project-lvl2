<?php

namespace Differ\parsers;

use Symfony\Component\Yaml\Yaml;

function readFile($path)
{
    $fullPath = createPathToFile($path);
    return parseFile($fullPath);
}

function createPathToFile($path)
{
    $pathToFile = !(bool) substr_count($path, '/') ? "./{$path}" : $path;
    if (!file_exists($pathToFile)) {
        throw new \Exception("File \"{$pathToFile}\" not exist.");
    }
    return $pathToFile;
}

function parseFile($path)
{
    $parsers = [
        'json' => function ($path) {
            return json_decode(file_get_contents($path), false);
        },
        'yaml' => function ($path) {
            return Yaml::parseFile($path, Yaml::PARSE_OBJECT_FOR_MAP);
        }
    ];

    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    if (!in_array($ext, array_keys($parsers))) {
        throw new \Exception("Extention \"{$ext}\" not supported.");
    }

    return $parsers[$ext]($path);
}
