<?php

namespace Differ\Formatters\pretty;

const SPACE = '    ';

function stringifyValue($value, $depth)
{
    $spaceValueKey = SPACE;
    $spaceByDepth = str_repeat(SPACE, $depth);

    $funcs = [
        'object' => function ($value) use ($spaceValueKey, $spaceByDepth) {
            $array = get_object_vars($value);
            $key = array_key_first($array);
            return "{\n{$spaceValueKey}{$spaceByDepth}{$key}: {$array[$key]}\n{$spaceByDepth}}";
        },
        'boolean' => function ($value) {
            return json_encode($value);
        }
    ];

    $type = gettype($value);
    $isFunc = in_array($type, array_keys($funcs));
    return $isFunc ? $funcs[$type]($value) : $value;
}

function createItem($item, $index, $prefix, $depth)
{
    $spaceItemName = str_repeat(SPACE, $depth - 1);
    $value = stringifyValue($item[$index], $depth);
    return "{$spaceItemName}  {$prefix} {$item['name']}: {$value}";
}

function format($ast)
{
    $inner = function ($innerData, $depth) use (&$inner) {

        $reducer = function ($acc, $child) use ($depth, &$inner) {
            switch ($child['type']) {
                case 'unchanged':
                    $acc[] = createItem($child, 'valueBefore', ' ', $depth);
                    return $acc;
                case 'changed':
                    $acc[] = createItem($child, 'valueAfter', '+', $depth);
                    $acc[] = createItem($child, 'valueBefore', '-', $depth);
                    return $acc;
                case 'removed':
                    $acc[] = createItem($child, 'valueBefore', '-', $depth);
                    return $acc;
                case 'added':
                    $acc[] = createItem($child, 'valueAfter', '+', $depth);
                    return $acc;
                case 'nested':
                    $space = str_repeat(SPACE, $depth);
                    $items = $inner($child['children'], $depth + 1);
                    $group = array_merge(["{$space}{$child['name']}: {"], $items, ["{$space}}"]);
                    $acc[] = join("\n", $group);
                    return $acc;
                default:
                    throw new \Exception("Type \"{$child['type']}\" not supported.");
            }
        };
        return array_reduce($innerData, $reducer, []);
    };

    $result = $inner($ast, 1);
    $joined = join("\n", $result);
    return "{\n{$joined}\n}";
}
