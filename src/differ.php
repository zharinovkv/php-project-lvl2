<?php

namespace Differ\differ;

use function Differ\parser\getContent;
use function Differ\parser\getInnerRepresentation;
use function Differ\parser\toString;
use function Differ\parser\getBeforeAndAfter;

use const Differ\parser\KEYS;

const ASSETS = 'assets/';

function genDiff($path_before, $path_after)
{
    $paths = getPathsToFiles($path_before, $path_after);

    $content = getContent($paths);

    [$before, $after] = getBeforeAndAfter($content);

    $ast = getInnerRepresentation($before, $after);

    $str = toString($ast);

    return $str;
}

function getPathsToFiles(...$paths)
{
    $pathsToFiles = array_map(function ($path) {
        return !(bool) substr_count($path, '/') ? $path = '{ASSETS}{$path}' : $path;
    }, $paths);
    return array_combine(KEYS, $pathsToFiles);
}
