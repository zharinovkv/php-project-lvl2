<?php

namespace Differ\gendiff;

use function Differ\path\{buildPathToFile, getExtention};
use function Differ\readfile\readFile;
use function Differ\ast\buildAst;
use function Differ\parsers\selectParser;

function genDiff($pathToFileBefore, $pathToFileAfter, $format = 'pretty')
{
    $realPathToFileBefore = buildPathToFile($pathToFileBefore);
    $realPathToFileAfter = buildPathToFile($pathToFileAfter);

    $extentionBefore = getExtention($pathToFileBefore);
    $extentionAfter = getExtention($pathToFileAfter);

    $contentBefore = readFile($realPathToFileBefore);
    $contentAfter = readFile($realPathToFileAfter);

    $parseBefore = selectParser($extentionBefore);
    $parseAfter = selectParser($extentionAfter);

    $dataBefore = $parseBefore($contentBefore);
    $dataAfter = $parseAfter($contentAfter);

    $ast = buildAst($dataBefore, $dataAfter);
    $format = "\Differ\Formatters\\{$format}\\format";
    return $format($ast);
}
