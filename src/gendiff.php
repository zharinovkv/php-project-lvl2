<?php

namespace Differ\gendiff;

use function Differ\path\{buildPathToFile, getExtention};
use function Differ\readfile\readFile;
use function Differ\ast\buildAst;
use function Differ\parsers\parsers;
use function Differ\formatters\formatters;

use const Differ\formatters\FORMATTERS;

function genDiff($pathToFileBefore, $pathToFileAfter, $format = 'pretty')
{
    $realPathToFileBefore = buildPathToFile($pathToFileBefore);
    $realPathToFileAfter = buildPathToFile($pathToFileAfter);

    $contentBefore = readFile($realPathToFileBefore);
    $contentAfter = readFile($realPathToFileAfter);

    $extentionBefore = getExtention($pathToFileBefore);
    $extentionAfter = getExtention($pathToFileAfter);

    $parsers = parsers();
    $dataBefore = isset($parsers[$extentionBefore]) ? $parsers[$extentionBefore]($contentBefore) :
        new \Exception("Extention \"{$extentionBefore}\" not supported.");
    $dataAfter = isset($parsers[$extentionAfter]) ? $parsers[$extentionAfter]($contentAfter) :
        new \Exception("Extention \"{$extentionAfter}\" not supported.");

    $ast = buildAst($dataBefore, $dataAfter);

    return isset(FORMATTERS[$format]) ? FORMATTERS[$format]($ast) :
        new \Exception("Formatter \"{$format}\" not supported.");
}
