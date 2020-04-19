<?php

namespace Differ\differ;

use function Differ\parser\parser;
use function Differ\parser\toAst;
use function Differ\parser\render;
use const Differ\parser\keys as keys;

const ASSETS = 'assets/';

function genDiff($path_before, $path_after)
{
    $paths = getPathsToFiles($path_before, $path_after);

    $content = parser($paths);
    $ast = toAst($content);
    $str = render($ast);

    return $str;
}

function getPathsToFiles(...$paths)
{
    $pathsToFiles = array_map(function ($path) {
        return !(bool) substr_count($path, '/') ? $path = '{ASSETS}{$path}' : $path;
    }, $paths);
    //$keys = ['path_before', 'path_after'];
    return array_combine(keys, $pathsToFiles);
}
