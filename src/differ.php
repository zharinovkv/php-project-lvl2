<?php

namespace Differ\differ;

use function Differ\parsers\readFile;
use function Differ\ast\buildAst;

function genDiff($filePathBefore, $filePathAfter, $format = 'pretty')
{
    $dataBefore = readFile($filePathBefore);
    $dataAfter = readFile($filePathAfter);

    $ast = buildAst($dataBefore, $dataAfter);

    $buildDiff = "\Differ\Formatters\\{$format}\\buildDiff";
    $diff = $buildDiff($ast);
    $toString = "\Differ\Formatters\\{$format}\\toString";
    return $toString($diff);
}
