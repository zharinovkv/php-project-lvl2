<?php

namespace Differ\Formatters\json;

function format($ast)
{
    return json_encode($ast, JSON_PRETTY_PRINT);
}
