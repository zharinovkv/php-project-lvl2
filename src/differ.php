<?php

namespace Differ\differ;

use function Differ\parser\getContent;
use function Differ\parser\splitOnBeforeAndAfter;
use function Differ\parser\getAst;
use function Differ\Formatters\bydefault\toDiff;
use function Differ\Formatters\bydefault\toString;

use const Differ\settings\KEYS;
use const Differ\settings\ASSETS;

function genDiff($path_before, $path_after)
{
    $paths = getPathsToFiles($path_before, $path_after);
    $content = getContent($paths);
    [$before, $after] = splitOnBeforeAndAfter($content);
    $ast = getAst($before, $after, 1);
    $result = toDiff($ast);
    $str = toString($result);
    return $str;
}

function getPathsToFiles(...$paths)
{
    $pathsToFiles = array_map(function ($path) {
        return !(bool) substr_count($path, '/') ? $path = ASSETS . "{$path}" : $path;
    }, $paths);
    return array_combine(KEYS, $pathsToFiles);
}
