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

function createItem($item, $resume)
{
    $before = getValue($item['before']);
    $after = getValue($item['after']);

    if ($resume === 'removed') {
        return "{$item[PROPS['name']]}' was {$resume}";
    }
    if ($resume === 'changed') {
        return "{$item[PROPS['name']]}' was {$resume}. From '{$before}' to '{$after}'";
    }
    if ($resume === 'added') {
        return "{$item[PROPS['name']]}' was {$resume} with value: '{$after}'";
    }
}

function render($ast)
{
    $types = [
        TYPES['unchanged'] => function ($item) {
            return null;
        },
        TYPES['changed'] => function ($item) {
            return createItem($item, 'changed');
        },
        TYPES['removed'] => function ($item) {
            return createItem($item, 'removed');
        },
        TYPES['added'] => function ($item) {
            return createItem($item, 'added');
        },
        TYPES['nested'] => function ($item) {
            return render($item[PROPS['children']]);
        },
    ];

    $mapper = function ($acc, $child) use ($types) {

        $item = $types[$child[PROPS['type']]]($child);

        if ($child[PROPS['type']] === TYPES['nested']) {
            $acc[] = [$child['name'] => render($child['children'])];
            return $acc;
        } elseif ($child[PROPS['type']] === TYPES['changed']) {
            $acc[] = $item;
            return $acc;
        } else {
            $acc[] = $item;
            return $acc;
        }
    };

    $result = array_reduce($ast, $mapper, []);
    return $result;
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

            $join = array_map($mapper, $withouted);
            $acc[] = implode("\n", $join);
            return $acc;
        }
    };

        $result2 = array_reduce($array, $mapper, []);
    
        $joined = join("\n", $result2);
        return "{$joined}";
}
