<?php

namespace Differ\differ;

use function Differ\parser\getContent;
use function Differ\parser\splitOnBeforeAndAfter;
use function Differ\parser\getAst;

use const Differ\settings\KEYS;
use const Differ\settings\ASSETS;

function genDiff($path_before, $path_after, $format = 'pretty')
{
    $paths = getPathsToFiles($path_before, $path_after);
    $content = getContent($paths);
    [$before, $after] = splitOnBeforeAndAfter($content);
    $ast = getAst($before, $after);

    $render = "\Differ\Formatters\\{$format}\\render";
    $rendered = $render($ast);
    $toString = "\Differ\Formatters\\{$format}\\toString";
    return $toString($rendered);
}

function getPathsToFiles(...$paths)
{
    $mapper = function ($path) {
        $pathToFile = !(bool)substr_count($path, '/') ? $path = "./{$path}" : $path;

        if (!file_exists($pathToFile)) {
            throw new \Exception("По указанному пути файл {$pathToFile} не существует.");
        }
        return $pathToFile;
    };

    $pathsToFiles = array_map($mapper, $paths);
    return array_combine(KEYS, $pathsToFiles);
}
