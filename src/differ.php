<?php

namespace Differ\differ;

use function Differ\parser\getPathsToFiles;
use function Differ\parser\getContent;
use function Differ\parser\splitOnBeforeAndAfter;
use function Differ\parser\getAst;

function genDiff($path_before, $path_after, $format = 'pretty')
{
    $paths = getPathsToFiles($path_before, $path_after);
    $content = getContent($paths);
    [$before, $after] = splitOnBeforeAndAfter($content);
    $ast = getAst($before, $after);

    $render = "\Differ\Formatters\\{$format}\\render";
    $rendered = $render($ast);
    $toString = "\Differ\Formatters\\{$format}\\toString";
    return $toString($rendered);
}
