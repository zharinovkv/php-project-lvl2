<?php

namespace Differ\readfile;

function readFile($path)
{
    if (!file_exists($path)) {
        throw new \Exception("File \"{$path}\" not exist.");
    }

    return file_get_contents($path);
}
