<?php

namespace Differ\Formatters\bydefault;

use function Funct\Collection\flatten;

use const Differ\settings\SPACE;
use const Differ\settings\PLUSE;
use const Differ\settings\MINUS;
use const Differ\settings\BLANK;
use const Differ\settings\TYPES;
use const Differ\settings\PROPS;

function getValue($value, $space)
{
    $func = function ($value) use ($space) {
        $value = get_object_vars($value);
        $key = array_key_first($value);
        $val = $value[$key];
        return "{\n{$space}" . SPACE . SPACE . "{$key}: {$val}\n{$space}" . SPACE . "}";
    };

    $value = is_bool($value) ? json_encode($value) : $value;
    $value = is_object($value) ? $func($value) : $value;
    return $value;
}

function createItem($item, $index, $pluse = PLUSE, $minus = MINUS, $blank = BLANK)
{
    $space = str_repeat(SPACE, $item[PROPS['depth']] - 1);
    $value = getValue($item[PROPS[$index]], $space);
    return "{$space}  {$pluse}{$minus}{$blank} {$item[PROPS['name']]}: {$value}";
}

function toDiff($ast)
{
    $types = [
        TYPES['unchanged'] => function ($item) {
            return createItem($item, 'beforeValue', null, null, BLANK);
        },
        TYPES['changed'] => function ($item) {
            $before = createItem($item, 'beforeValue', null, MINUS, null);
            $after = createItem($item, 'afterValue', PLUSE, null, null);
            return "{$after},{$before}";
        },
        TYPES['removed'] => function ($item) {
            return createItem($item, 'beforeValue', null, MINUS, null);
        },
        TYPES['added'] => function ($item) {
            return createItem($item, 'afterValue', PLUSE, null, null);
        },
        TYPES['nested'] => function ($item) {
            return [$item[PROPS['name']] => toDiff($item[PROPS['children']])];
        },
    ];

    $mapper = function ($acc, $child) use ($types) {

        $space = str_repeat(SPACE, $child['depth']);

        $item = $types[$child[PROPS['type']]]($child);

        if ($child[PROPS['type']] === TYPES['nested']) {
            $acc[] = [$space . $child['name'] => toDiff($child['children'])];
            return $acc;
        }

        if ($child[PROPS['type']] === TYPES['changed']) {
            $items = explode(',', $item);
            $acc[] = $items[0];
            $acc[] = $items[1];
            return $acc;
        }

        if ($child[PROPS['type']] === TYPES['unchanged'] || $child[PROPS['type']] === TYPES['added'] || $child[PROPS['type']] === TYPES['removed']) {
            $acc[] = $item;
            return $acc;
        }
    };

    $result = array_reduce($ast, $mapper, []);
    return $result;
}

function toString($arr)
{
    $mapper = function ($acc, $child) {        
        if (is_string($child)) {
            $acc[] = $child;
            return $acc;
        } elseif  (is_array($child))  {
            $key = array_key_first($child);
            $flattened = flatten($child);
            $joined = implode("\n", $flattened);
            $acc[] = $key === 0 ? $joined : "{$key}: {\n{$joined}\n    }";
            return $acc;
        }
    };

    $result = array_reduce($arr, $mapper, []);
    $joined = join("\n", $result);
    return "{\n{$joined}\n}\n";
}
