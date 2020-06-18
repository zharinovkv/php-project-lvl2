<?php

namespace Differ\Formatters\json;

function buildDiff($ast)
{
    return json_encode($ast, JSON_PRETTY_PRINT);
}
