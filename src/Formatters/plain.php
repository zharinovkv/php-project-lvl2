<?php

namespace Differ\Formatters\plain;

function createValue($value)
{
    $values = [
        'object' => fn () => "complex value",
        'boolean' => fn ($value) => json_encode($value)
    ];

    $isFunc = in_array(gettype($value), array_keys($values));
    return $isFunc ? $values[gettype($value)]($value) : $value;
}

function format($ast, $nameGroup = null)
{
    $reducer = function ($acc, $child) use ($nameGroup) {

        $valueBefore = isset($child['valueBefore']) ? createValue($child['valueBefore']) : null;
        $valueAfter = isset($child['valueAfter']) ? createValue($child['valueAfter']) : null;

        switch ($child['type']) {
            case ($child['type'] === 'unchanged'):
                break;
            case ($child['type'] === 'changed'):
                $acc[] = "Property '{$nameGroup}{$child['name']}' was changed." .
                    " From '{$valueBefore}' to '{$valueAfter}'";
                break;
            case ($child['type'] === 'removed'):
                $acc[] = "Property '{$nameGroup}{$child['name']}' was removed";
                break;
            case ($child['type'] === 'added'):
                $acc[] = "Property '{$nameGroup}{$child['name']}' was added with value: '{$valueAfter}'";
                break;
            case ($child['type'] === 'nested'):
                $acc[] = format($child['children'], "{$child['name']}.");
                break;
            default:
                throw new \Exception("Type \"{$child['type']}\" not supported.");
        }
        return $acc;
    };
    $reduced = array_reduce($ast, $reducer, []);

    return join("\n", $reduced);
}
