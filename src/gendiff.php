<?php

namespace Differ\gendiff;

use function Differ\path\{buildPathToFile, getExtention};
use function Differ\readfile\readFile;
use function Differ\ast\buildAst;
use function Differ\parsers\parsers;
use function Differ\formatters\formatters;

function genDiff($pathToFileBefore, $pathToFileAfter, $format = 'pretty')
{
    $realPathToFileBefore = buildPathToFile($pathToFileBefore);
    $realPathToFileAfter = buildPathToFile($pathToFileAfter);

    $contentBefore = readFile($realPathToFileBefore);
    $contentAfter = readFile($realPathToFileAfter);

    $extentionBefore = getExtention($pathToFileBefore);
    $extentionAfter = getExtention($pathToFileAfter);

    $parserBefore = parsers($extentionBefore);
    $parserAfter = parsers($extentionAfter);

    $dataBefore = $parserBefore($contentBefore);
    $dataAfter = $parserAfter($contentAfter);

    $ast = buildAst($dataBefore, $dataAfter);
    return formatters($format)($ast);
}
