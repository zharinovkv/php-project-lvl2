<?php

namespace Differ\Formatters\json;

const SPACE = '    ';

function createValue($value, $space)
{
    $spaceInner = SPACE;

    $values = [
        'object' => function ($value) use ($space, $spaceInner) {
            $val = get_object_vars($value);
            $key = array_key_first($val);
            return "{\n{$space}{$spaceInner}\"{$key}\": \"{$val[$key]}\"\n{$space}}";
        },
        'boolean' => function ($value) {
            $val = json_encode($value);
            return "\"{$val}\"";
        }
    ];

    $isFunc = in_array(gettype($value), array_keys($values));
    return $isFunc ? $values[gettype($value)]($value) : "\"{$value}\"";
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

    $space = str_repeat(SPACE, $item['depth'] * 2);
    $spaceBracket = str_repeat(SPACE, $item['depth'] * 2 - 1);
    $keys = array_keys($filtered);

    $reducer = function ($acc, $key) use ($filtered, $space) {
        $value = createValue($filtered[$key], $space);
        if ($key !== 'depth') {
            $acc[] = "\n{$space}\"{$key}\": {$value}";
        }
        return $acc;
    };
    $reduced = array_reduce($keys, $reducer, []);

    $joined = join(',', $reduced);
    return "{$spaceBracket}{{$joined}\n{$spaceBracket}}";
}

function buildDiff($ast)
{
    $types = [
        'unchanged' => fn ($item) => createItem($item),
        'changed' => fn ($item) => createItem($item),
        'removed' => fn ($item) => createItem($item),
        'added' => fn ($item) => createItem($item),
        'nested' => fn ($item) => buildDiff($item['children'])
    ];

    $reducer = function ($acc, $child) use ($types) {

        $space = str_repeat(SPACE, $child['depth']);
        $spaceInner = str_repeat(SPACE, $child['depth'] + 1);

        $item = $types[$child['type']]($child);

        if ($child['type'] === 'nested') {
            $children = join(",\n", buildDiff($child['children']));
            $acc[] = "{$space}{\n" .
                "{$spaceInner}\"type\": \"nested\",\n" .
                "{$spaceInner}\"name\": \"{$child['name']}\",\n" .
                "{$spaceInner}\"children\": [\n{$children}\n" .
                "{$spaceInner}]\n{$space}}";
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
        $acc[] = $child;
        return $acc;
    };

    $reduced = array_reduce($items, $reducer, []);
    $joined = join(",\n", $reduced);
    return "[\n{$joined}\n]";
}
