<?php

namespace Differ\readfile;

function readFile($path)
{
    $fullPath = createPathToFile($path);
    $content['content'] = file_get_contents($fullPath);
    $content['ext'] = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    return $content;
}

function createPathToFile($path)
{
    $pathToFile = substr_count($path, '/') === 0 ? "./{$path}" : $path;

    if (!file_exists($pathToFile)) {
        throw new \Exception("File \"{$pathToFile}\" not exist.");
    }

    return $pathToFile;
}
