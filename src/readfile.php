<?php

namespace Differ\readfile;

function readFile($path)
{
    $fullPath = createPathToFile($path);
    return file_get_contents($fullPath);
}

function createPathToFile($path)
{
    $pathToFile = substr_count($path, '/') === 0 ? "./{$path}" : $path;

    if (!file_exists($pathToFile)) {
        throw new \Exception("File \"{$pathToFile}\" not exist.");
    }

    return $pathToFile;
}
