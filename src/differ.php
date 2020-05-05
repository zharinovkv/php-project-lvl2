<?php

namespace Differ\differ;

use function Differ\parser\getContent;
use function Differ\parser\toDiff;
use function Differ\parser\splitOnBeforeAndAfter;
use function Differ\parser\getAst;

use const Differ\parser\KEYS;

const ASSETS = 'assets/';

function genDiff($path_before, $path_after)
{
    $paths = getPathsToFiles($path_before, $path_after);
    $content = getContent($paths);
    [$before, $after] = splitOnBeforeAndAfter($content);
    $ast = getAst($before, $after);
//    print_r(toDiff2($ast));
    $result = toDiff($ast);
    return $result;
}

function getPathsToFiles(...$paths)
{
    $pathsToFiles = array_map(function ($path) {
        return !(bool) substr_count($path, '/') ? $path = '{ASSETS}{$path}' : $path;
    }, $paths);
    return array_combine(KEYS, $pathsToFiles);
}
