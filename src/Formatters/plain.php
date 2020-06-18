<?php

namespace Differ\Formatters\plain;

use function Funct\Collection\flatten;

function createValue($value)
{
    $values = [
        'object' => fn () => "complex value",
        'boolean' => fn ($value) => json_encode($value)
    ];

    $isFunc = in_array(gettype($value), array_keys($values));
    return $isFunc ? $values[gettype($value)]($value) : $value;
}

function buildDiff($ast, $nameGroup = null)
{
    $mapper = function ($child) use ($nameGroup) {

        $valueBefore = isset($child['valueBefore']) ? createValue($child['valueBefore']) : null;
        $valueAfter = isset($child['valueAfter']) ? createValue($child['valueAfter']) : null;

        switch ($child['type']) {
            case ($child['type'] === 'unchanged'):
                return;
            case ($child['type'] === 'changed'):
                return "Property '{$nameGroup}{$child['name']}' was changed. From '{$valueBefore}' to '{$valueAfter}'";
            case ($child['type'] === 'removed'):
                return "Property '{$nameGroup}{$child['name']}' was removed";
            case ($child['type'] === 'added'):
                return "Property '{$nameGroup}{$child['name']}' was added with value: '{$valueAfter}'";
            case ($child['type'] === 'nested'):
                return buildDiff($child['children'], "{$child['name']}.");
            default:
                throw new \Exception("Type \"{$child['type']}\" not supported.");
        }
    };
    $mapped = array_map($mapper, $ast);

    $flattened = flatten($mapped);
    
    $filtered = array_filter($flattened, function ($value) {
        return $value !== null;
    }, ARRAY_FILTER_USE_BOTH);

    return join("\n", $filtered);
}
