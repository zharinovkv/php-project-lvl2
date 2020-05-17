<?php

namespace Differ\Formatters\json;

use const Differ\settings\SPACE;
use const Differ\settings\TYPES;
use const Differ\settings\PROPS;

function getValue($value, $space_base)
{
    $space = SPACE;

    $func_object = function ($value) use ($space_base, $space) {
        $value = get_object_vars($value);
        $key = array_key_first($value);
        return "{\n{$space_base}{$space}\"{$key}\": \"{$value[$key]}\"\n{$space_base}}";
    };
    $func_bool = function ($value) {
        $val = json_encode($value);
        return "\"{$val}\"";
    };
    $func_numeric = function ($value) {
        $val = (string) $value;
        return "\"{$val}\"";
    };
    $func_string = function ($value) {
        return "\"{$value}\"";
    };

    if (is_object($value)) {
        return $func_object($value);
    } elseif (is_bool($value)) {
        return $func_bool($value);
    } elseif (is_numeric($value)) {
        return $func_numeric($value);
    } elseif (is_string($value)) {
        return $func_string($value);
    }
    return $value;
}

function space($depth, $coef1 = 1, $coef2 = 0)
{
    return str_repeat(SPACE, $depth * $coef1 - $coef2);
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

    $space_base = space($item['depth'], 2, 0);
    $space_bracket = space($item['depth'], 2, 1);

    $keys = array_keys($filtered);
    $reducer = function ($acc, $key) use ($filtered, $space_base, $space_bracket) {

        $val = getValue($filtered[$key], $space_base, $space_bracket);

        if ($key !== 'depth') {
            $acc[] = "\n{$space_base}\"{$key}\": {$val}";
        }
        return $acc;
    };
    $reduced = array_reduce($keys, $reducer, []);
    $joined = join(',', $reduced);
    return "{$space_bracket}{{$joined}\n{$space_bracket}}";
}

function render($ast)
{
    $sp = SPACE;

    $types = [
        TYPES['unchanged'] => function ($item) {
            return createItem($item);
        },
        TYPES['changed'] => function ($item) {
            return createItem($item);
        },
        TYPES['removed'] => function ($item) {
            return createItem($item);
        },
        TYPES['added'] => function ($item) {
            return createItem($item);
        },
        TYPES['nested'] => function ($item) {
            return render($item[PROPS['children']]);
        },
    ];

    $mapper = function ($acc, $child) use ($types, $sp) {

        $sb = space($child['depth'], 2, 0);
        $space_bracket = space($child['depth'], 2, 1);

        $item = $types[$child[PROPS['type']]]($child);

        if ($child[PROPS['type']] === TYPES['nested']) {
            $children = join(",\n", render($child['children']));
            $acc[] = "$sp{\n$sb\"type\": \"nested\",\n" .
                "$sb\"name\": \"{$child['name']}\",\n$sb\"children\": [\n$children\n$sb]\n$sp}";
            return $acc;
        } else {
            $acc[] = $item;
            return $acc;
        }
    };
    $result = array_reduce($ast, $mapper, []);
    return $result;
}

function toString($result)
{
    $mapper = function ($acc, $child) {
        $acc[] = $child;
        return $acc;
    };

    $result2 = array_reduce($result, $mapper, []);
    $joined = join(",\n", $result2);
    return "[\n{$joined}\n]";
}
