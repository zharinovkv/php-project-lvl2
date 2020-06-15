<?php

namespace Differ\ast;

function buildAst($before, $after)
{
    $keys = array_keys(array_merge(get_object_vars($before), get_object_vars($after)));

    $mapper = function ($key) use ($before, $after) {
        if (!property_exists($after, $key)) {
            return ['type' => 'removed', 'name' => $key, 'valueBefore' => $before->$key];
        } elseif (!property_exists($before, $key)) {
            return ['type' => 'added', 'name' => $key, 'valueAfter' => $after->$key];
        } elseif (is_object($before->$key) && is_object($after->$key)) {
            return ['type' => 'nested', 'name' => $key, 'children' => buildAst($before->$key, $after->$key)];
        } elseif ($before->$key === $after->$key) {
            return ['type' => 'unchanged', 'name' => $key, 'valueBefore' => $before->$key, 'valueAfter' => $after->$key];
        } elseif ($before->$key !== $after->$key) {
            return ['type' => 'changed', 'name' => $key, 'valueBefore' => $before->$key, 'valueAfter' => $after->$key];
        }
    };

    return array_map($mapper, $keys);
}
