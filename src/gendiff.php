<?php

namespace Differ\gendiff;

use function Differ\readfile\readFile;
use function Differ\parsers\parseData;
use function Differ\ast\buildAst;

function genDiff($pathToFileBefore, $pathToFileAfter, $format = 'pretty')
{
    $contentBefore = readFile($pathToFileBefore);
    $contentAfter = readFile($pathToFileAfter);

    $dataBefore = parseData($contentBefore, $pathToFileBefore);
    $dataAfter = parseData($contentAfter, $pathToFileAfter);

    $ast = buildAst($dataBefore, $dataAfter);

    $format = "\Differ\Formatters\\{$format}\\format";
    return $format($ast);
}
