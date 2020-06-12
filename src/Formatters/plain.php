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

function buildDiff($ast)
{
    $mapper = function ($child) {

        $before = isset($child['valueBefore']) ? createValue($child['valueBefore']) : '';
        $after = isset($child['valueAfter']) ? createValue($child['valueAfter']) : '';

        switch ($child['type']) {
            case ($child['type'] === 'unchanged'):
                return;
            case ($child['type'] === 'changed'):
                return "{$child['name']}' was changed. From '{$before}' to '{$after}'";
            case ($child['type'] === 'removed'):
                return "{$child['name']}' was removed";
            case ($child['type'] === 'added'):
                return "{$child['name']}' was added with value: '{$after}'";
            case ($child['type'] === 'nested'):
                return [$child['name'] => buildDiff($child['children'])];
            default:
                throw new \Exception("Type \"{$child['type']}\" not supported.");
        }
    };
    return array_map($mapper, $ast);
}

function toString($items)
{
    $reducer = function ($acc, $child) {
        if (is_string($child)) {
            $acc[] = "Property '{$child}";
            return $acc;
        } else {
            $flattened = flatten($child);
            $withouted = without($flattened, null);
            
            $key = array_key_first($child);
            $mapper = function ($item) use ($key) {
                return "Property '{$key}.{$item}";
            };
            $mapped = array_map($mapper, $withouted);

            $acc[] = join("\n", $mapped);
            return $acc;
        }
    };
    
    $reduced = array_reduce($items, $reducer, []);
    $joined = join("\n", $reduced);
    return $joined;
}
