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
        throw new \Exception("File {$pathToFile} not exist.");
    }
    return $pathToFile;
}

function parseFile($path)
{
    $json = function ($path) {
        return json_decode(file_get_contents($path), false);
    };

    $yaml = function ($path) {
        return Yaml::parseFile($path, Yaml::PARSE_OBJECT_FOR_MAP);
    };

    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    return $$ext($path);
}
