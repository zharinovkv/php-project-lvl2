<?php

namespace Differ\Formatters\json;

function buildDiff($ast)
{
    return $ast;
}

function toString($items)
{
    return json_encode($items, JSON_PRETTY_PRINT);
}
