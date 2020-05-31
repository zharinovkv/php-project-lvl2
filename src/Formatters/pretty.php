<?php

namespace Differ\Formatters\pretty;

use function Funct\Collection\flatten;

const SPACE = '    ';

function createValue($value, $depth)
{
    $spacePre = str_repeat(SPACE, $depth + 1);
    $spacePost = str_repeat(SPACE, $depth);

    $values = [
        'object' => function ($value) use ($spacePre, $spacePost) {
            $value = get_object_vars($value);
            $key = array_key_first($value);
            $val = $value[$key];
            return "{\n{$spacePre}{$key}: {$val}\n{$spacePost}}";
        },
        'boolean' => function ($value) {
            return json_encode($value);
        }
    ];

    $isFunc = in_array(gettype($value), array_keys($values));
    return $isFunc ? $values[gettype($value)]($value) : $value;
}

function createItem($item, $index, $prefix)
{
    $space = str_repeat(SPACE, $item['depth'] - 1);
    $value = createValue($item[$index], $item['depth']);
    return "{$space}  {$prefix} {$item['name']}: {$value}";
}

function buildDiff($ast)
{
    $types = [
        'unchanged' => fn ($item) => createItem($item, 'valueBefore', ' '),
        'changed' => function ($item) {
            $before = createItem($item, 'valueBefore', '-');
            $after = createItem($item, 'valueAfter', '+');
            return ['after' => $after, 'before' => $before];
        },
        'removed' => fn ($item) => createItem($item, 'valueBefore', '-'),
        'added' => fn ($item) => createItem($item, 'valueAfter', '+'),
        'nested' => fn ($item) => buildDiff($item['children'])
    ];

    $reducer = function ($acc, $child) use ($types) {

        $space = str_repeat(SPACE, $child['depth']);
        $item = $types[$child['type']]($child);

        if ($child['type'] === 'nested') {
            $acc[] = [$space . $child['name'] => buildDiff($child['children'])];
            return $acc;
        } elseif ($child['type'] === 'changed') {
            [$acc[], $acc[]] = [$item['after'], $item['before']];
            return $acc;
        } else {
            $acc[] = $item;
            return $acc;
        }
    };
    return array_reduce($ast, $reducer, []);
}

function toString($items)
{
    $reducer = function ($acc, $child) {
        if (is_string($child)) {
            $acc[] = $child;
            return $acc;
        } elseif (is_array($child)) {
            $key = array_key_first($child);
            $flattened = flatten($child);
            $joined = join("\n", $flattened);
            $acc[] = $key === 0 ? $joined : "{$key}: {\n{$joined}\n    }";
            return $acc;
        }
    };

    $reduced = array_reduce($items, $reducer, []);
    $joined = join("\n", $reduced);
    return "{\n{$joined}\n}";
}
