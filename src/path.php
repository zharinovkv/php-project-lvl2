<?php

namespace Differ\path;

function buildPathToFile($path)
{
    return realpath($path);
}


function getExtention($path)
{
    $extention = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    if (empty($extention)) {
        throw new \Exception("File \"{$path}\" does not contain the extention.");
    }

    return $extention;
}
