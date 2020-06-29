<?php

namespace Differ\gendiff;

use function Differ\path\{buildPathToFile, getExtention};
use function Differ\readfile\readFile;
use function Differ\ast\buildAst;

use const Differ\parsers\PARSERS;
use const Differ\formatters\FORMATTERS;

function genDiff($pathToFileBefore, $pathToFileAfter, $format = 'pretty')
{
    $realPathToFileBefore = buildPathToFile($pathToFileBefore);
    $realPathToFileAfter = buildPathToFile($pathToFileAfter);

    $contentBefore = readFile($realPathToFileBefore);
    $contentAfter = readFile($realPathToFileAfter);

    $extentionBefore = getExtention($pathToFileBefore);
    $extentionAfter = getExtention($pathToFileAfter);

    $dataBefore = isset(PARSERS[$extentionBefore]) ? PARSERS[$extentionBefore]($contentBefore) :
        new \Exception("Extention \"{$extentionBefore}\" not supported.");
    $dataAfter = isset(PARSERS[$extentionAfter]) ? PARSERS[$extentionAfter]($contentAfter) :
        new \Exception("Extention \"{$extentionAfter}\" not supported.");

    $ast = buildAst($dataBefore, $dataAfter);
    return FORMATTERS[$format]($ast);
}
