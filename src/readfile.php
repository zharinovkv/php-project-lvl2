<?php

namespace Differ\readfile;

function readFile($path)
{
    $fullPath = createPathToFile($path);
    return file_get_contents($fullPath);
}

function createPathToFile($path)
{
    $pathToFile = realpath($path);

    if (!file_exists($pathToFile)) {
        throw new \Exception("File \"{$pathToFile}\" not exist.");
    }

    return $pathToFile;
}

function getExtention($path)
{
    $extention = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    if (empty($extention)) {
        throw new \Exception("File \"{$path}\" does not contain the extention.");
    }

    return $extention;
}
