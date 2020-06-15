<?php

namespace Differ\Formatters\pretty;

use function Funct\Collection\flatten;

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
    $spaceByDepth = str_repeat(SPACE, $depth - 1);
    $value = createValue($item[$index], $depth);
    return "{$spaceByDepth}  {$prefix} {$item['name']}: {$value}";
}

function buildDiff($ast, $depth = 1)
{
    $reducer = function ($acc, $child) use ($depth) {

        switch ($child['type']) {
            case $child['type'] === 'unchanged':
                $acc[] = createItem($child, 'valueBefore', ' ', $depth);
                return $acc;
            case $child['type'] === 'changed':
                $acc[] = createItem($child, 'valueAfter', '+', $depth);
                $acc[] = createItem($child, 'valueBefore', '-', $depth);
                return $acc;
            case $child['type'] === 'removed':
                $acc[] = createItem($child, 'valueBefore', '-', $depth);
                return $acc;
            case $child['type'] === 'added':
                $acc[] = createItem($child, 'valueAfter', '+', $depth);
                return $acc;
            case $child['type'] === 'nested':
                $acc[] = [$child['name'] => buildDiff($child['children'], $depth + 1)];
                return $acc;
            default:
                throw new \Exception("Type \"{$child['type']}\" not supported.");
        }
    };
    return array_reduce($ast, $reducer, []);
}

function toString($items)
{
    $space = SPACE;

    $reduced = array_reduce($items, function ($acc, $child) use ($space) {
        if (is_array($child)) {
            $key = array_key_first($child);
            $flattened = flatten($child);
            $joined = join("\n", $flattened);
            $acc[] = "{$space}{$key}: {\n{$joined}\n{$space}}";
            return $acc;
        } else {
            $acc[] = $child;
            return $acc;
        }
    }, []);

    $joined = join("\n", $reduced);
    return "{\n{$joined}\n}";
}
