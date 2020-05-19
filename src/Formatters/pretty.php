<?php

namespace Differ\Formatters\pretty;

use function Funct\Collection\flatten;

use const Differ\settings\SPACE;
use const Differ\settings\PLUSE;
use const Differ\settings\MINUS;
use const Differ\settings\BLANK;
use const Differ\settings\TYPES;
use const Differ\settings\PROPS;

function getValue($value, $depth)
{
    $space_pre = str_repeat(SPACE, $depth + 1);
    $space_post = str_repeat(SPACE, $depth);

    $func = function ($value) use ($space_pre, $space_post) {
        $value = get_object_vars($value);
        $key = array_key_first($value);
        $val = $value[$key];
        return "{\n{$space_pre}{$key}: {$val}\n{$space_post}}";
    };

    $value = is_bool($value) ? json_encode($value) : $value;
    $value = is_object($value) ? $func($value) : $value;
    return $value;
}

function createItem($item, $index, $pluse = PLUSE, $minus = MINUS, $blank = BLANK)
{
    $space = str_repeat(SPACE, $item[PROPS['depth']] - 1);
    $value = getValue($item[PROPS[$index]], $item[PROPS['depth']]);
    return "{$space}  {$pluse}{$minus}{$blank} {$item[PROPS['name']]}: {$value}";
}

function getDiff($ast)
{
    $types = [
        TYPES['unchanged'] => function ($item) {
            return createItem($item, 'beforeValue', null, null, BLANK);
        },
        TYPES['changed'] => function ($item) {
            $before = createItem($item, 'beforeValue', null, MINUS, null);
            $after = createItem($item, 'afterValue', PLUSE, null, null);
            return [$after, $before];
        },
        TYPES['removed'] => function ($item) {
            return createItem($item, 'beforeValue', null, MINUS, null);
        },
        TYPES['added'] => function ($item) {
            return createItem($item, 'afterValue', PLUSE, null, null);
        },
        TYPES['nested'] => function ($item) {
            return getDiff($item[PROPS['children']]);
        },
    ];

    $reducer = function ($acc, $child) use ($types) {

        $space = str_repeat(SPACE, $child[PROPS['depth']]);
        $item = $types[$child[PROPS['type']]]($child);

        if ($child[PROPS['type']] === TYPES['nested']) {
            $acc[] = [$space . $child[PROPS['name']] => getDiff($child[PROPS['children']])];
            return $acc;
        } elseif ($child[PROPS['type']] === TYPES['changed']) {
            $acc[] = $item[0];
            $acc[] = $item[1];
            return $acc;
        } else {
            $acc[] = $item;
            return $acc;
        }
    };
    return array_reduce($ast, $reducer, []);
}

function toString($array)
{
    $reducer = function ($acc, $child) {
        if (is_string($child)) {
            $acc[] = $child;
            return $acc;
        } elseif (is_array($child)) {
            $key = array_key_first($child);
            $flattened = flatten($child);
            $joined = implode("\n", $flattened);
            $acc[] = $key === 0 ? $joined : "{$key}: {\n{$joined}\n    }";
            return $acc;
        }
    };

    $reduced = array_reduce($array, $reducer, []);
    $joined = join("\n", $reduced);
    return "{\n{$joined}\n}";
}
