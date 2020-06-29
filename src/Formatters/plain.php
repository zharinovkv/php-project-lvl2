<?php

namespace Differ\Formatters\plain;

use function Funct\Collection\flatten;

function stringifyValue($value)
{
    $funcs = [
        'object' => fn () => "complex value",
        'boolean' => fn ($value) => json_encode($value)
    ];

    $type = gettype($value);
    $isFunc = in_array($type, array_keys($funcs));
    return $isFunc ? $funcs[$type]($value) : $value;
}

function format($ast)
{
    $inner = function ($innerData, $nameGroup) use (&$inner) {

        $mapper = function ($child) use ($nameGroup, $inner) {

            $valueBefore = isset($child['valueBefore']) ? stringifyValue($child['valueBefore']) : null;
            $valueAfter = isset($child['valueAfter']) ? stringifyValue($child['valueAfter']) : null;

            switch ($child['type']) {
                case 'unchanged':
                    return;
                case 'changed':
                    return "Property '{$nameGroup}{$child['name']}' was changed. " .
                        "From '{$valueBefore}' to '{$valueAfter}'";
                case 'removed':
                    return "Property '{$nameGroup}{$child['name']}' was removed";
                case 'added':
                    return "Property '{$nameGroup}{$child['name']}' was added with value: '{$valueAfter}'";
                case 'nested':
                    return $inner($child['children'], "{$child['name']}.");
                default:
                    throw new \Exception("Type \"{$child['type']}\" not supported.");
            }
        };
        $mapped = array_map($mapper, $innerData);
        $flattened = flatten($mapped);
        $filtered = array_filter($flattened, fn ($value) => $value !== null, ARRAY_FILTER_USE_BOTH);
        return join("\n", $filtered);
    };
    return $inner($ast, null);
}
