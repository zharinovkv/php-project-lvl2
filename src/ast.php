<?php

namespace Differ\ast;

function createNode($type, $key, $valueBefore, $valueAfter, $depth, $children)
{
    return [
        'type' => $type,
        'name' => $key,
        'valueBefore' => $valueBefore,
        'valueAfter' => $valueAfter,
        'depth' => $depth,
        'children' => $children
    ];
}

function buildAst($before, $after, $depth = 1)
{
    $keys = array_keys(array_merge(get_object_vars($before), get_object_vars($after)));

    $mapper = function ($key) use ($before, $after, $depth) {
        if (property_exists($before, $key) && property_exists($after, $key)) {
            if (is_object($before->$key) && is_object($after->$key)) {
                return createNode(
                    'nested',
                    $key,
                    null,
                    null,
                    $depth,
                    buildAst($before->$key, $after->$key, $depth + 1)
                );
            } else {
                if ($before->$key === $after->$key) {
                    return createNode('unchanged', $key, $before->$key, $after->$key, $depth, null);
                } elseif ($before->$key !== $after->$key) {
                    return createNode('changed', $key, $before->$key, $after->$key, $depth, null);
                }
            }
        } elseif (!property_exists($after, $key)) {
            return createNode('removed', $key, $before->$key, null, $depth, null);
        } elseif (!property_exists($before, $key)) {
            return createNode('added', $key, null, $after->$key, $depth, null);
        }
    };

    return array_map($mapper, $keys);
}
