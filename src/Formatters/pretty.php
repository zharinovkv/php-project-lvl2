<?php

namespace Differ\Formatters\pretty;

const SPACE = '    ';

function extractValue($value, $depth)
{
    $space = SPACE;
    $spaceByDepth = str_repeat(SPACE, $depth);

    $funcs = [
        'object' => function ($value) use ($space, $spaceByDepth) {
            $array = get_object_vars($value);
            $key = array_key_first($array);
            return "{\n{$space}{$spaceByDepth}{$key}: {$array[$key]}\n{$spaceByDepth}}";
        },
        'boolean' => function ($value) {
            return json_encode($value);
        }
    ];

    $isFunc = in_array(gettype($value), array_keys($funcs));
    return $isFunc ? $funcs[gettype($value)]($value) : $value;
}

function createItem($item, $index, $prefix, $depth)
{
    $space = str_repeat(SPACE, $depth - 1);
    $value = extractValue($item[$index], $depth);
    return "{$space}  {$prefix} {$item['name']}: {$value}\n";
}

function format($ast)
{
    $inner = function ($innerData, $depth) use (&$inner) {

        $reducer = function ($acc, $child) use ($depth, &$inner) {
            switch ($child['type']) {
                case $child['type'] === 'unchanged':
                    $item = createItem($child, 'valueBefore', ' ', $depth);
                    return "{$acc}{$item}";
                case $child['type'] === 'changed':
                    $itemAfter = createItem($child, 'valueAfter', '+', $depth);
                    $itemBefore = createItem($child, 'valueBefore', '-', $depth);
                    return "{$acc}{$itemAfter}{$itemBefore}";
                case $child['type'] === 'removed':
                    $item = createItem($child, 'valueBefore', '-', $depth);
                    return "{$acc}{$item}";
                case $child['type'] === 'added':
                    $item = createItem($child, 'valueAfter', '+', $depth);
                    return "{$acc}{$item}";
                case $child['type'] === 'nested':
                    $space = str_repeat(SPACE, $depth);
                    $items = $inner($child['children'], $depth + 1);
                    return "{$acc}{$space}{$child['name']}: {$items}\n";
                default:
                    throw new \Exception("Type \"{$child['type']}\" not supported.");
            }
        };
        $reduced = array_reduce($innerData, $reducer, "");
        $space = str_repeat(SPACE, $depth - 1);
        return "{\n{$reduced}{$space}}";
    };
    return $inner($ast, 1);
}
