<?php

namespace Differ\gendiff;

use function Differ\readfile\readFile;
use function Differ\parsers\getExtention;
use function Differ\parsers\parseData;
use function Differ\ast\buildAst;

function genDiff($pathToFileBefore, $pathToFileAfter, $format = 'pretty')
{
    $contentBefore = readFile($pathToFileBefore);
    $contentAfter = readFile($pathToFileAfter);

    $extBefore = getExtention($pathToFileBefore);
    $extAfter = getExtention($pathToFileAfter);

    $dataBefore = parseData($contentBefore, $extBefore);
    $dataAfter = parseData($contentAfter, $extAfter);

    $ast = buildAst($dataBefore, $dataAfter);

    $format = "\Differ\Formatters\\{$format}\\format";
    return $format($ast);
}
