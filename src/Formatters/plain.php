<?php

namespace Differ\Formatters\plain;

use function Funct\Collection\flatten;
use function Funct\Collection\without;

function createValue($value)
{
    $values = [
        'object' => fn () => "complex value",
        'boolean' => fn ($value) => json_encode($value)
    ];

    $isFunc = in_array(gettype($value), array_keys($values));
    return $isFunc ? $values[gettype($value)]($value) : $value;
}

function createItem($child)
{
    $before = createValue($child['valueBefore']);
    $after = createValue($child['valueAfter']);

    $item = [
        'removed' => fn ($child) => "{$child['name']}' was {$child['type']}",
        'changed' => fn ($child) => "{$child['name']}' was {$child['type']}. From '{$before}' to '{$after}'",
        'added' => fn ($child) => "{$child['name']}' was {$child['type']} with value: '{$after}'"
    ];

    return $item[$child['type']]($child);
}

function buildDiff($ast)
{
    $types = [
        'unchanged' => fn () => null,
        'changed' => fn ($child) => createItem($child),
        'removed' => fn ($child) => createItem($child),
        'added' => fn ($child) => createItem($child),
        'nested' => fn ($child) => buildDiff($child['children'])
    ];

    $mapper = function ($acc, $child) use ($types) {

        $item = $types[$child['type']]($child);

        if ($child['type'] === 'nested') {
            $acc[] = [$child['name'] => buildDiff($child['children'])];
            return $acc;
        } else {
            $acc[] = $item;
            return $acc;
        }
    };
    return array_reduce($ast, $mapper, []);
}

function toString($items)
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
    $reduced = array_reduce($items, $mapper, []);
    $joined = join("\n", $reduced);
    return "{$joined}";
}
