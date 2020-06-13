<?php

namespace Differ\parsers;

use Symfony\Component\Yaml\Yaml;

function readFile($path)
{
    $fullPath = createPathToFile($path);
    $content = file_get_contents($fullPath);
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    return parseFile($content, $ext);
}

function createPathToFile($path)
{
    $pathToFile = !(bool) substr_count($path, '/') ? "./{$path}" : $path;

    if (!file_exists($pathToFile)) {
        throw new \Exception("File \"{$pathToFile}\" not exist.");
    }

    return $pathToFile;
}

function parseFile($content, $ext)
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
