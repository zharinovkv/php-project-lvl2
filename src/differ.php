<?php

namespace Differ\differ;

use function Differ\parser\getPathsToFiles;
use function Differ\parser\getContent;
use function Differ\parser\getAst;

function genDiff($path_before, $path_after, $format = 'pretty')
{
    $paths = getPathsToFiles($path_before, $path_after);
    $content = getContent($paths);
    $ast = getAst($content);

    $getDiff = "\Differ\Formatters\\{$format}\\getDiff";
    $diff = $getDiff($ast);
    $toString = "\Differ\Formatters\\{$format}\\toString";
    return $toString($diff);
}
