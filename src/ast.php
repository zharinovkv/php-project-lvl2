<?php

namespace Differ\ast;

function buildAst($dataBefore, $dataAfter)
{
    $keys = array_keys(array_merge(get_object_vars($dataBefore), get_object_vars($dataAfter)));

    $mapper = function ($key) use ($dataBefore, $dataAfter) {
        if (!property_exists($dataAfter, $key)) {
            return ['type' => 'removed', 'name' => $key, 'valueBefore' => $dataBefore->$key];
        } elseif (!property_exists($dataBefore, $key)) {
            return ['type' => 'added', 'name' => $key, 'valueAfter' => $dataAfter->$key];
        } elseif (is_object($dataBefore->$key) && is_object($dataAfter->$key)) {
            return ['type' => 'nested', 'name' => $key, 'children' => buildAst($dataBefore->$key, $dataAfter->$key)];
        } elseif ($dataBefore->$key === $dataAfter->$key) {
            return [
                'type' => 'unchanged',
                'name' => $key,
                'valueBefore' => $dataBefore->$key,
                'valueAfter' => $dataAfter->$key
            ];
        } elseif ($dataBefore->$key !== $dataAfter->$key) {
            return [
                'type' => 'changed',
                'name' => $key,
                'valueBefore' => $dataBefore->$key,
                'valueAfter' => $dataAfter->$key
            ];
        }
    };

    return array_map($mapper, $keys);
}
