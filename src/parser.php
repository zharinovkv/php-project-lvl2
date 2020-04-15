<?php

namespace Differ\parsers;

use Symfony\Component\Yaml\Yaml;

function parser($paths)
{
    $ext = pathinfo($paths['path_before'], PATHINFO_EXTENSION);

    $json = function ($path) {
        return json_decode(file_get_contents($path), true);
    };

    $yaml = function ($path) {
        return Yaml::parseFile($path);
    };

    return array_map(${$ext}, $paths);
}
