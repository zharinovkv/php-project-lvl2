<?php

namespace Differ\Formatters\plain;

use function Funct\Collection\flatten;
use function Funct\Collection\without;

use const Differ\settings\TYPES;
use const Differ\settings\PROPS;

function getValue($value)
{
    $value = is_bool($value) ? json_encode($value) : $value;
    $value = is_object($value) ? "complex value" : $value;
    return $value;
}

function createItem($item)
{
    $before = getValue($item['before']);
    $after = getValue($item['after']);

    if ($item[PROPS['type']] === 'removed') {
        return "{$item[PROPS['name']]}' was {$item[PROPS['type']]}";
    }
    if ($item[PROPS['type']] === 'changed') {
        return "{$item[PROPS['name']]}' was {$item[PROPS['type']]}. From '{$before}' to '{$after}'";
    }
    if ($item[PROPS['type']] === 'added') {
        return "{$item[PROPS['name']]}' was {$item[PROPS['type']]} with value: '{$after}'";
    }
}

function getDiff($ast)
{
    $types = [
        TYPES['unchanged'] => function ($item) {
            return null;
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
            return getDiff($item[PROPS['children']]);
        },
    ];

    $mapper = function ($acc, $child) use ($types) {

        $item = $types[$child[PROPS['type']]]($child);

        if ($child[PROPS['type']] === TYPES['nested']) {
            $acc[] = [$child['name'] => getDiff($child['children'])];
            return $acc;
        } elseif ($child[PROPS['type']] === TYPES['changed']) {
            $acc[] = $item;
            return $acc;
        } else {
            $acc[] = $item;
            return $acc;
        }
    };
    return array_reduce($ast, $mapper, []);  
}

function toString($array)
{
    $mapper = function ($acc, $child) {
        if (is_string($child)) {
            $acc[] = "Property '{$child}";
            return $acc;
        } elseif (is_array($child)) {
            $key = array_key_first($child);
            $flattened = flatten($child);
            $withouted = without($flattened, null);

            $mapper = function ($item) use ($key) {
                return "Property '{$key}.{$item}";
            };

            $mapped = array_map($mapper, $withouted);
            $acc[] = join("\n", $mapped);
            return $acc;
        }
    };
        $reduced = array_reduce($array, $mapper, []);
        $joined = join("\n", $reduced);
        return "{$joined}";
}
