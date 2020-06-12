<?php

namespace Differ\gendiff;

use function Differ\parsers\readFile;
use function Differ\ast\buildAst;

function genDiff($filePathBefore, $filePathAfter, $format = 'pretty')
{
    $dataBefore = readFile($filePathBefore);
    $dataAfter = readFile($filePathAfter);

/*     print_r($dataBefore);
    print_r($dataAfter); */

    $ast = buildAst($dataBefore, $dataAfter);

    //print_r($ast);

    $buildDiff = "\Differ\Formatters\\{$format}\\buildDiff";
    $diff = $buildDiff($ast);
    
    $toString = "\Differ\Formatters\\{$format}\\toString";
    return $toString($diff);
}
