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
    $result = $toString($rendered);
    return $result;
}

function getPathsToFiles(...$paths)
{
    $assets = ASSETS;

    $mapper = function ($path) use ($assets) {
        $pathToFile = !(bool)substr_count($path, '/') ? $path = "{$assets}{$path}" : $path;

        if (!file_exists($pathToFile)) {
            throw new \Exception("По указанному адресу файл {$pathToFile} не существует.");
        }
        return $pathToFile;
    };

    $pathsToFiles = array_map($mapper, $paths);
    return array_combine(KEYS, $pathsToFiles);
}
