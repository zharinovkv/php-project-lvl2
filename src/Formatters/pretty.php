<?php

namespace Differ\Formatters\pretty;

const SPACE = '    ';

function createValue($value, $depth)
{
    $space = SPACE;
    $spaceByDepth = str_repeat(SPACE, $depth);

    $values = [
        'object' => function ($value) use ($space, $spaceByDepth) {
            $array = get_object_vars($value);
            $key = array_key_first($array);
            $val = $array[$key];
            return "{\n{$space}{$spaceByDepth}{$key}: {$val}\n{$spaceByDepth}}";
        },
        'boolean' => function ($value) {
            return json_encode($value);
        }
    ];

    $isFunc = in_array(gettype($value), array_keys($values));
    return $isFunc ? $values[gettype($value)]($value) : $value;
}

function createItem($item, $index, $prefix, $depth)
{
    $space = str_repeat(SPACE, $depth - 1);
    $value = createValue($item[$index], $depth);
    return "{$space}  {$prefix} {$item['name']}: {$value}\n";
}

function format($ast, $depth = 1)
{
    $reducer = function ($acc, $child) use ($depth) {
        switch ($child['type']) {
            case $child['type'] === 'unchanged':
                $acc .= createItem($child, 'valueBefore', ' ', $depth);
                return $acc;
            case $child['type'] === 'changed':
                $acc .= createItem($child, 'valueAfter', '+', $depth);
                $acc .= createItem($child, 'valueBefore', '-', $depth);
                return $acc;
            case $child['type'] === 'removed':
                $acc .= createItem($child, 'valueBefore', '-', $depth);
                return $acc;
            case $child['type'] === 'added':
                $acc .= createItem($child, 'valueAfter', '+', $depth);
                return $acc;
            case $child['type'] === 'nested':
                $space = str_repeat(SPACE, $depth);
                $acc .= "{$space}{$child['name']}: ";
                $acc .= format($child['children'], $depth + 1);
                $acc .= "\n";
                return $acc;
            default:
                throw new \Exception("Type \"{$child['type']}\" not supported.");
        }
    };
    $reduced = array_reduce($ast, $reducer, "");
    $space = str_repeat(SPACE, $depth - 1);
    return "{\n{$reduced}{$space}}";
}
