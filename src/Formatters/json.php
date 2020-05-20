<?php

namespace Differ\Formatters\json;

use const Differ\settings\SPACE;
use const Differ\settings\TYPES;
use const Differ\settings\PROPS;

function getValue($value, $space)
{
    $spaceAdd = SPACE;

    $values = [
        'object' => function ($value) use ($space, $spaceAdd) {
            $value = get_object_vars($value);
            $key = array_key_first($value);
            return "{\n{$space}{$spaceAdd}\"{$key}\": \"{$value[$key]}\"\n{$space}}";
        },
        'boolean' => function ($value) {
            $val = json_encode($value);
            return "\"{$val}\"";
        }
    ];

    $cond = in_array(gettype($value), array_keys($values));
    return $cond ? $values[gettype($value)]($value) : "\"{$value}\"";
}

function createItem($item)
{
    $filter = function ($val, $key) {
        if ($val === null) {
            return;
        }
        return [$key => $val];
    };
    $filtered = array_filter($item, $filter, ARRAY_FILTER_USE_BOTH);

    $space = str_repeat(SPACE, $item[PROPS['depth']] * 2);
    $space_bracket = str_repeat(SPACE, $item[PROPS['depth']] * 2 - 1);

    $keys = array_keys($filtered);

    $reducer = function ($acc, $key) use ($filtered, $space) {

        $val = getValue($filtered[$key], $space);

        if ($key !== 'depth') {
            $acc[] = "\n{$space}\"{$key}\": {$val}";
        }
        return $acc;
    };
    $reduced = array_reduce($keys, $reducer, []);

    $joined = join(',', $reduced);
    return "{$space_bracket}{{$joined}\n{$space_bracket}}";
}

function getDiff($ast)
{
    $types = [
        TYPES['unchanged'] => fn ($item) => createItem($item),
        TYPES['changed'] => fn ($item) => createItem($item),
        TYPES['removed'] => fn ($item) => createItem($item),
        TYPES['added'] => fn ($item) => createItem($item),
        TYPES['nested'] => fn ($item) => getDiff($item[PROPS['children']])
    ];

    $reducer = function ($acc, $child) use ($types) {

        $space = str_repeat(SPACE, $child[PROPS['depth']]);
        $space_inner = str_repeat(SPACE, $child[PROPS['depth']] + 1);
        $item = $types[$child[PROPS['type']]]($child);

        if ($child[PROPS['type']] === TYPES['nested']) {
            $children = join(",\n", getDiff($child['children']));
            $acc[] = "{$space}{\n" .
                "{$space_inner}\"type\": \"nested\",\n" .
                "{$space_inner}\"name\": \"{$child['name']}\",\n" .
                "{$space_inner}\"children\": [\n{$children}\n" .
                "{$space_inner}]\n{$space}}";
            return $acc;
        } else {
            $acc[] = $item;
            return $acc;
        }
    };
    return array_reduce($ast, $reducer, []);
}

function toString($diff)
{
    $reducer = function ($acc, $child) {
        $acc[] = $child;
        return $acc;
    };

    $reduced = array_reduce($diff, $reducer, []);
    $joined = join(",\n", $reduced);
    return "[\n{$joined}\n]";
}
